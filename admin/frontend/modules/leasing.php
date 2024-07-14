<?php

class leasing extends Frontend {

	public function __construct($id,$module = array()) {
		parent::__construct();
		$this->m_dir = ADMIN.'frontend/forms/leasing/';
		$this->m_moduleId = $id;
		$this->m_module = $module;
		$this->m_leaseId = array_key_exists('lease_id',$_REQUEST) ? $_REQUEST['lease_id'] : 0;
		$this->m_images = "/images/leases/";
	}

	function getModule() {
		$module = parent::getModule();
		if ($this->hasOption('buyUrl') && array_key_exists('addToCart',$_REQUEST)) {
			$this->logMessage(__FUNCTION__,sprintf('redirecting after purchase to [%s]',$this->getOption('buyUrl')),1);
			header('Location: '.$this->getOption('buyUrl'));
			return "";
		}
		//
		//	not sure if this should be template_allow_override or allow_override. process_overrides should handle it and i think allow_oveeride is correct
		//
		if (array_key_exists('cat_id',$_REQUEST) && $module['template_allow_override']) {
			$this->logMessage(__FUNCTION__,sprintf('overriding module folder from [%s] to [%s] as per config',$module['folder_id'],$_REQUEST['cat_id']),2);
			$module['folder_id'] = $_REQUEST['cat_id'];
		}
		return $module;
	}
	
	private function buildSql($module,$addLimit = false) {
		if ($module['folder_id'] > 0) {
			if ($module['include_subfolders'] != 0) {
				$sql = sprintf('select p.id from lease_folders p, lease_folders p1 where p1.id = %d and p.left_id >= p1.left_id and p.right_id <= p1.right_id and p.enabled = 1',$module['folder_id']);
				$tmp = array_merge(array(0),$this->fetchScalarAll($sql));
				$this->logMessage(__FUNCTION__,sprintf('sub folder sql [%s]',$sql),3);
				$sql = sprintf("select p.*, j.sequence, f.left_id, j.folder_id from leases p, lease_by_folder j, lease_folders f where f.id = j.folder_id and p.deleted = 0 and p.enabled = 1 and p.published = 1 and j.folder_id in (%s) and p.id = j.lease_id",implode(',',$tmp));
			}
			else
				$sql = sprintf("select p.*, j.sequence, f.left_id, j.folder_id from leases p, lease_by_folder j, lease_folders f where f.id = j.folder_id and p.deleted = 0 and p.enabled = 1 and p.published = 1 and j.folder_id = %d and p.id = j.lease_id",$module['folder_id']);
		}
		else
			$sql = "select p.*, 0 as sequence, 0 as left_id, 0 as folder_id from leases p where p.deleted = 0 and p.enabled = 1 and p.published = 1";
		if (array_key_exists('lease_list',$module) && count($module['lease_list']) > 0)
			$sql .= sprintf(' and p.id in (%s)',implode(',',$module['lease_list']));
		if (strlen($module['where_clause']) > 0)
			$sql .= " and ".$module['where_clause'];
		if ($module['featured'])
			$sql .= " and featured = 1";
		if ($this->hasOption('unique')) {
			$pos = strpos($sql,'from');
			$tmp = sprintf('select min(concat(left_id,"|",p.id)) %s group by p.id',substr($sql,$pos-1));
			$test = $this->fetchScalarAll($tmp);
			$this->logMessage(__FUNCTION__,sprintf('unique code produced [%s] with [%s] pos [%s] sql [%s]',print_r($test,true),$tmp,$pos,$sql),2);
			$sql .= sprintf(' and concat(left_id,"|",p.id) in ("%s")',implode($test,'","'));
		}
		if (strlen($module['sort_by']) > 0) {
			$sql .= " order by ".$module['sort_by'];
		}
		else
			$sql .= " order by left_id, sequence";
		if ($addLimit) {
	 		if (strlen($module['records']) > 0) {
				$tmp = explode(',',$module['records']);
				if (count($tmp) > 1)
					$total = $tmp[0]*$tmp[1];
				else
					$total = $tmp[0];
				if ($total > 0)
					$sql .= " limit ".$total;
			}
		}
		$this->logMessage(__FUNCTION__,sprintf('sql [%s]',$sql),3);
		return $sql;
	}

	function formatData($data) {
		for($x = 1; $x < 11; $x++) {
			if (array_key_exists('image'.$x,$data) && strlen($data['image'.$x]) > 0) {
				$tmp = new image();
				$tmp->addAttributes(array('src'=>$data['image'.$x],'alt'=>sprintf("%s %s",$data["year"],$data["make"])));
				$data['img_image'.$x] = $tmp->show();
			}
		}
		$options = $this->fetchAll(sprintf("select c.value, c.id, c.type, cf.title from code_lookups c, code_folders cf, lease_options lo where lo.lease_id = %d and c.id = lo.code_id and cf.tableName = c.type order by cf.left_id, c.sort", $data["id"]));
		$data["options"] = array();
		$data["options_value"] = array();
		foreach($options as $key=>$rec) {
			if (!array_key_exists($rec["type"],$data["options"])) $data["options"][$rec["type"]] = array();
			if (!array_key_exists($rec["title"],$data["options_value"])) $data["options_value"][$rec["title"]] = array();
			$data["options"][$rec["type"]][$rec["id"]] = $rec;
			$data["options_value"][$rec["title"]][] = $rec["value"];
		}
		$data['url'] = $this->getUrl('lease',$data['id'],$data);
		$data['href'] = sprintf('<a href="%s">',$data['url']);
		$data['href_end'] = '</a>';
		$data['make_name'] = $this->fetchScalar(sprintf('select name from lease_make where id = %d',$data['make']));
		$data['model_name'] = $this->fetchScalar(sprintf('select model from lease_model where id = %d',$data['model']));
		$d1 = date_create(date("Y-m-d"));
		$d2 = date_create($data["lease_expiry"]);
		$i = date_diff($d2,$d1);
		$data["lease_remaining"] = $i->format("%y")*12 + $i->format("%m");
		if ($data["lease_remaining"] < 0) $data["lease_remaining"] = 0;
		$o_mod = new members(0);

		$member = $this->fetchSingle(sprintf("select * from members where id = %d", $data["owner_id"]));
		$p = array("id"=>$member["id"]);
		$data["owner"] = $o_mod->formatData($member);
		$data["owner"]["profile"] = $o_mod->formatProfile($p,$member);

		if ($data["contact_id"] > 0) {
			$member = $this->fetchSingle(sprintf("select * from members where id = %d", $data["contact_id"]));
			$p = array("id"=>$member["id"]);
			$data["contact"] = $o_mod->formatData($member);
			$data["contact"]["profile"] = $o_mod->formatProfile($p,$member);
		}

		if ($data["cash_incentive"] > 0) {
			if ($data["lease_remaining"] > 0)
				$diff = round($data["cash_incentive"] / $data["lease_remaining"],2);
			else $diff = $data["cash_incentive"];
			$data["effective_payment"] = $data["lease_taxes_inc"] - $diff;
		}
		elseif ($data["recoup"] > 0) {
			if ($data["lease_remaining"] > 0)
				$diff = round($data["recoup"] / $data["lease_remaining"],2);
			else $diff = $data["recoup"];
			$data["effective_payment"] = $data["lease_taxes_inc"] + $diff;
		}
		else {
			$data["effective_payment"] = $data["lease_taxes_inc"];
		}
//$this->logMessage(__FUNCTION__,sprintf("effective [%s] recoup [%s] diff [%s] remaining [%s]", $data["effective_payment"], $data["recoup"], $diff, $data["lease_remaining"]), 1);

		$this->logMessage(__FUNCTION__,sprintf('return data [%s]',print_r($data,true)),2);
		return $data;
	}

	function formatFolder($data,$module = array()) {
		$data['url'] = $this->getUrl('leaseFolder',$data['id'],$data);
		if (array_key_exists("image",$data) && strlen($data['image']) > 0) {
			$tmp = new image();
			$tmp->addAttributes(array('src'=>$data['image'],'alt'=>htmlentities(strip_tags($data['title']))));
			$data['img_image'] = $tmp->show();
		}
		if (array_key_exists("rollover_image",$data) && strlen($data['rollover_image']) > 0) {
			$tmp = new image();
			$tmp->addAttributes(array('src'=>$data['rollover_image'],'alt'=>htmlentities(strip_tags($data['title']))));
			$data['img_rollover_image'] = $tmp->show();
		}
		if (array_key_exists('folder_id',$module) && $data['id'] == $module['folder_id']) $data['active'] = 'active';
		if (array_key_exists('cat_id',$_REQUEST) && $data['id'] == $_REQUEST['cat_id']) $data['active'] = 'active';
		$level = 1;
		$breadcrumbs = array();
		$breadcrumbs[$level] = sprintf('<a href="%s" class="sequence-~seq~">%s</a>',$this->getUrl('category',$data['id'],$data),htmlspecialchars($data['title']));
		$tmp = $data;
		$ct = 0;
		while(($tmp = $this->fetchSingle(sprintf('select * from lease_folders where level = %d and left_id <= %d and right_id >= %d',$tmp['level']-1,$tmp['left_id'],$tmp['right_id']))) && $ct < 10) {
			$level += 1;
			$this->logMessage(__FUNCTION__,sprintf('breadcrumb [%s] folder [%s]',print_r($breadcrumbs,true),print_r($tmp,true)),3);
			$breadcrumbs[$level] = sprintf('<a href="%s" class="sequence-~seq~">%s</a>',$this->getUrl('category',$tmp['id'],$tmp),htmlspecialchars($tmp['title']));
			$ct++;
		}
		if ($this->hasOption('breadcrumbs')) {
			$this->logMessage(__FUNCTION__,sprintf('truncating breadcrumbs as per config [%d]',$this->getOption('breadcrumbs')),2);
			if ($this->getOption('breadcrumbs') < 0) $breadcrumbs = array_slice($breadcrumbs,0,$this->getOption('breadcrumbs'));
		}
		$data['breadcrumbs'] = implode('&nbsp;>>&nbsp;',array_reverse($breadcrumbs));
		$this->logMessage(__FUNCTION__,sprintf('return data [%s]',print_r($data,true)),3);
		return $data;
	}

	function formatReview($data) {
		$data["formattedCreated"] = date(GLOBAL_DEFAULT_DATETIME_FORMAT,strtotime($data['created']));
		return $data;
	}

	function leases() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->init($this->m_dir.$module['outer_html']);
		$sql = $this->buildSql($module);
		$pagination = $this->getPagination($sql,$module,$recordCount);
		$outer->addTag('recordCount',$recordCount);
		$leases = $this->fetchAll($sql);
		$this->logMessage(__FUNCTION__,sprintf('sql [%s] count [%d]',$sql,count($leases)),2);
		$return = array();
		$frm = new Forms();
		$frm->init($this->m_dir.$module['inner_html']);
		$ct = 0;
		$flds = $this->config->getFields($module['configuration']);
		if ($this->hasOption('grpPrefix')) $return[] = $this->getOption('grpPrefix');
		foreach($leases as $key=>$lease) {
			if ($lease['folder_id'] == 0) $lease['folder_id'] = $module['folder_id'];
			$ct += 1;
			if ($module['rows'] > 0 && $ct > $module['columns']) {
				$ct = 1;
				if ($this->hasOption('grpSuffix')) $return[] = $this->getOption('grpSuffix');
				if ($this->hasOption('grpPrefix')) 
					$return[] = $this->getOption('grpPrefix');
				else
					$return[] = '<div class="clearfix"></div>';
			}
			$frm->reset();
			$frm->buildForm($flds);
			$frm->addTag('sequence',$ct);
			if ($lease['folder_id'] > 0 && $fldr = $this->fetchSingle(sprintf('select * from lease_folders where id = %d and enabled = 1',$lease['folder_id']))) {
				$lease['folder'] = $this->formatFolder($fldr);
			}
			$frm->addData($this->formatData($lease));
			$subdata = $this->subForms($this->m_module['fetemplate_id'],null,array('folder_id'=>$module['folder_id'],'lease_id'=>$lease['id']),'inner');
			$this->logMessage(__FUNCTION__,sprintf('subforms [%s]',print_r($subdata,true)),3);
			foreach($subdata as $key=>$value) {
				$frm->addTag($key,$value,false);
			}
			$return[] = $frm->show();
		}
		if ($this->hasOption('grpSuffix')) $return[] = $this->getOption('grpSuffix');
		$outer->addTag('pagination',$pagination,false);
		if ($module['folder_id'] != 0) {
			if ($fldr = $this->fetchSingle(sprintf('select * from lease_folders where id = %d',$module['folder_id'])))
				$outer->addData($this->formatFolder($fldr));
		}
		$outer->addTag('leases',implode('',$return),false);
		$subdata = $this->subForms($this->m_module['fetemplate_id'],null,array('folder_id'=>$module['folder_id']),'outer');
		$this->logMessage(__FUNCTION__,sprintf('subforms [%s]',print_r($subdata,true)),3);
		foreach($subdata as $key=>$value) {
			$outer->addTag($key,$value,false);
		}
		$tmp = $outer->show();
		return $tmp;
	}
	
	function listing() {
		if (!$module = $this->getModule())
			return "";
		$this->parseOptions($module['misc_options']);
		//$sql = $this->buildSql($module,true);
		if ($module['folder_id'] != 0)
			$root = $this->fetchSingle(sprintf('select * from lease_folders where id = %d',$module['folder_id']));
		else
			$root = array('left_id'=>0,'right_id'=>999999,'level'=>0,'id'=>0,'title'=>'','image'=>'','rollover_image'=>'');
		$outer = new Forms();
		$outer->init($this->m_dir.$module['outer_html']);
		$flds = $this->config->getFields($module['configuration']);
		$outer->buildForm($flds);
		if ($this->hasOption('postParam') && array_key_exists($this->getOption('postParam'),$_POST)) {
			$this->logMessage(__FUNCTION__,sprintf('adding post to outer as per config [%s]',$this->getOption('postParam')),2);
			$outer->addData($_POST);
		}
		if ($this->hasOption('typeSelect')) {
			$menu = $this->buildOpt($root,$module,0);
		}
		else {
			$menu = sprintf('<ul class="level_0 %s">%s</ul>',$this->getOption("ul_class"),$this->buildUL($root,$module,0));
		}
		$outer->addTag('listing',$menu,false);
		$subdata = $this->subForms($module['fetemplate_id'],null,array(),'outer');
		$this->logMessage(__FUNCTION__,sprintf('outer subforms [%s]',print_r($subdata,true)),4);
		foreach($subdata as $key=>$value) {
			$outer->addTag($key,$value,false);
		}
		$outer->addData($this->formatFolder($root));
		$tmp = $outer->show();
		return $tmp;
	}

	private function buildUL($root,$module,$root_level) {
		$this->logMessage(__FUNCTION__,sprintf("root [%d] root_level [%d]",$root['id'],$root_level),2);
		if ($this->hasOption('maxLevel') && $root_level >= $this->getOption('maxLevel')) {
			$this->logMessage(__FUNCTION__,sprintf('max level exceeded',$this->getOption('maxLevel')),2);
			return "";
		}
		if (array_key_exists('internal_link',$root) && $root['internal_link'] > 0) {
			$this->logMessage(__FUNCTION__,sprintf("replacing folder from internal link"),1);
			$root = $this->fetchSingle(sprintf('select * from lease_folders where id = %d',$root['internal_link']));
		}
		$level = $this->fetchAll(sprintf('select * from lease_folders where level = %d and left_id > %d and right_id < %d and enabled = 1 order by left_id',$root['level']+1,$root['left_id'],$root['right_id']));
		$form = new Forms();
		$form->init($this->m_dir.$module['inner_html']);
		$menu = array();
		$seq = 0;
		foreach($level as $key=>$item) {
			$seq += 1;
			$form->reset();
			$item = $this->formatFolder($item,$module);
			$form->addData($item);
			$form->addTag('sequence',$seq);
			$form->addTag('level',$root_level+1);
			$subdata = $this->subForms($this->m_module['fetemplate_id'],null,array('folder_id'=>$item['internal_link'] > 0 ? $item['internal_link'] : $item['id']),'inner');
			$this->logMessage(__FUNCTION__,sprintf('subforms [%s]',print_r($subdata,true)),3);
			foreach($subdata as $key=>$value) {
				$form->addTag($key,$value,false);
			}
			$tmp = $form->show();
			$hasSubmenu = false;
			if (($subMenu = $this->buildUL($item,$module,$root_level+1)) != '') {
				$tmp .= sprintf('<ul class="level_%d submenu">%s</ul>',$root_level+1,$subMenu);
				$hasSubmenu = true;
			}
			if ($this->hasOption('delim') && $seq < count($level)) $tmp .= sprintf('<span class="delim">%s</span>',$this->getOption('delim'));
			if (strlen($module['parm1']) > 0) {
				$li = new Forms();
				$li->init($this->m_dir.$module['parm1']);
				$li->addData(array_merge(array('delim'=>$this->getOption('delim'),
					'span'=>$tmp,'sequence'=>$seq,'hasSubmenu'=>$hasSubmenu ? 'hasSubmenu':'','item'=>$tmp), $item));
				$menu[] = $li->show();
			}
			else
				$menu[] = sprintf('<li class="sequence_%d %s %s">%s</li>',$seq,$hasSubmenu ? 'hasSubmenu':'',array_key_exists('active',$item)?$item['active']:'',$tmp);
		}
		$this->logMessage(__FUNCTION__,sprintf("return [%s]",print_r($menu,true)),3);
		return implode("",$menu);
	}
	
	function details() {
		if (!$module = $this->getModule())
			return "";
		$this->parseOptions($module['misc_options']);
		$outer = new Forms();
		$outer->init($this->m_dir.$module['outer_html']);
		if (array_key_exists('lease_id',$_REQUEST) || $this->hasOption('showAlways')) {
			if ($module['folder_id'] != 0) {
				if ($fldr = $this->fetchSingle(sprintf('select * from lease_folders where enabled = 1 and id = %d',$module['folder_id']))) {
					$outer->addData($this->formatFolder($fldr));
				}
				if (array_key_exists('lease_id',$_REQUEST))
					$sql = sprintf('select p.* from leases p where p.id = %d and p.deleted = 0 and p.published = 1 and p.enabled = 1 and exists (select 1 from lease_by_folder f where f.lease_id = p.id and f.folder_id = %d)',$_REQUEST['lease_id'],$module['folder_id']);
				else
					$sql = $this->buildSql($module,true);
			}
			else {
				$sql = sprintf('select * from lease_folders where enabled = 1 and id in (select folder_id from lease_by_folder where lease_id = %d) order by rand() limit 1',$this->m_leaseId);
				if ($fldr = $this->fetchSingle($sql)) {
					$outer->addData($this->formatFolder($fldr));
					$module['folder_id'] = $fldr['id'];
				}
				if (array_key_exists('lease_id',$_REQUEST))
					$sql = sprintf('select * from leases where id = %d and deleted = 0 and published = 1 and enabled = 1',$_REQUEST['lease_id']);
				else
					$sql = $this->buildSql($module,true);
			}
			$leases = $this->fetchAll($sql);
			$this->logMessage(__FUNCTION__,sprintf('sql [%s] found [%d]',$sql,count($leases)),2);
			$return = array();
			$inner = new Forms();
			$inner->init($this->m_dir.$module['inner_html']);
			$flds = $this->config->getFields($module['configuration']);
			foreach($leases as $lease) {
				$lease['folder_id'] = $module['folder_id'];
				$inner->reset();
				$inner->addData($this->formatData($lease));
				$subdata = $this->subForms($this->m_module['fetemplate_id'],null,array("lease_id"=>$lease["id"]),'inner');
				$this->logMessage(__FUNCTION__,sprintf('subforms [%s]',print_r($subdata,true)),3);
				foreach($subdata as $key=>$value) {
					$inner->addTag($key,$value,false);
				}
				$return[] = $inner->show();
			}
			$subdata = $this->subForms($this->m_module['fetemplate_id'],null,array(),'outer');
			$this->logMessage(__FUNCTION__,sprintf('subforms [%s]',print_r($subdata,true)),3);
			foreach($subdata as $key=>$value) {
				$outer->addTag($key,$value,false);
			}
			if (count($return) > 0) {
				$outer->addTag('leases',implode('',$return),false);
				$tmp = $outer->show();
			}
			else $tmp = '';
			return $tmp;
		}
	}

	function folderRelations() {
		if (!$module = $this->getModule())
			return "";
		$this->logMessage(__FUNCTION__,sprintf("module [%s]",print_r($module,true)),2);
		if ($module['folder_id'] == 0) {
			$this->logMessage(__FUNCTION__,'bail - no default folder',1);
			return '';
		}
		if (!$this->hasOption('templateId')) {
			$this->logMessage(__FUNCTION__,sprintf('attempt to implement cross class functionality with no config [%s] this [%s] request [%s]',print_r($module,true),print_r($this,true),print_r($_REQUEST,true)),1,true);
			return "";
		}
		$fn = $this->fetchSingle(sprintf('select t.id as fetemplate_id, t.module_function, m.classname from fetemplates t, modules m where t.id = %d and m.id = t.module_id',$this->getOption('templateId')));
		$tmp = $this->m_module;
		$tmp['classname'] = $fn['classname'];
		$tmp['fetemplate_id'] = $fn['fetemplate_id'];
		$tmp['module_function'] = $fn['module_function'];
		switch($fn['classname']) {
		case 'gallery':
			$module['id'] = $this->getOption('templateId');
			$html = $this->galleryFolder($fn,$module['folder_id']);
			break;
		}
		return $html;
	}

	function itemRelations() {
		if (!$module = $this->getModule())
			return "";
		$this->logMessage(__FUNCTION__,sprintf("module [%s]",print_r($module,true)),2);
		if (!$this->hasOption('templateId')) {
			$this->logMessage(__FUNCTION__,sprintf('attempt to implement cross class functionality with no config [%s] this [%s] request [%s]',print_r($module,true),print_r($this,true),print_r($_REQUEST,true)),1,true);
			return "";
		}
		$fn = $this->fetchSingle(sprintf('select t.id as fetemplate_id, t.module_function, m.classname from fetemplates t, modules m where t.id = %d and m.id = t.module_id',$this->getOption('templateId')));
		$tmp = $this->m_module;
		$tmp['classname'] = $fn['classname'];
		$tmp['fetemplate_id'] = $fn['fetemplate_id'];
		$tmp['module_function'] = $fn['module_function'];
		switch($fn['classname']) {
			case 'calendar':
				$module['id'] = $this->getOption('templateId');
				$html = $this->calendar($fn);
				break;
			case 'members':
				$module['id'] = $this->getOption('templateId');
				$html = $this->members($fn);
				break;
			case 'gallery':
				$module['id'] = $this->getOption('templateId');
				$html = $this->gallery($fn);
				break;
			case 'coupons':
				$module['id'] = $this->getOption('templateId');
				$html = $this->coupons($fn);
				break;
			case 'news':
				$module['id'] = $this->getOption('templateId');
				$html = $this->news($fn);
				break;
			case 'blog':
				$module['id'] = $this->getOption('templateId');
				$html = $this->blog($fn);
				break;
			default:
				$html = '';
		}
		return $html;
	}

	private function galleryFolder($module,$folder) {
		$this->logMessage(__FUNCTION__,sprintf('parms [%s]',print_r($module,true)),2);
		if ($folders = $this->fetchSingle(sprintf('select * from relations where owner_type = "leasefolder" and owner_id = %d and related_type = "galleryfolder"',$folder))) {
			$module['folder_id'] = $folders['related_id'];
		}
		else {
			$folders = array();
			$this->logMessage(__FUNCTION__,sprintf('no gallery for leaseFolder %d',$folder),1);
			return "";
		}
		$obj = new gallery($module['fetemplate_id'],$module);
		if (method_exists('gallery',$module['module_function'])) {
			$this->logMessage(__FUNCTION__,sprintf('invoking gallery with [%s]',print_r($module,true)),2);
			$html = $obj->{$module['module_function']}();
			return $html;
		}
		else {
			$this->logMessage(__FUNCTION__,sprintf('bail - no function [%s] in gallery for leaseFolder %d',$module['module_function'],$folder),1,true);
		}
	}

	private function news($module) {
		$this->logMessage(__FUNCTION__,sprintf('parms [%s]',print_r($module,true)),2);
		if ($folders = $this->fetchSingle(sprintf('select * from relations where owner_type = "news" and related_id = %d and related_type = "newsfolder"',$this->m_leaseId))) {
			$module['folder_id'] = $folders['related_id'];
		}
		else {
			$folders = array();
			$module['folder_id'] = 0;
			$this->logMessage(__FUNCTION__,sprintf('bail - no news for lease %d',$this->m_leaseId),1);
		}
		if ($items = $this->fetchScalarAll(sprintf('select owner_id as news_id from relations where owner_type = "news" and related_id = %d and related_type = "lease"',$this->m_leaseId)))
			$module['news_list'] = $items;
		else {
			$items = array();
			$this->logMessage(__FUNCTION__,sprintf('bail - no news for lease %d',$this->m_leaseId),2);
		}
		if (count($folders) == 0 && count($items) == 0) {
			$this->logMessage(__FUNCTION__,sprintf('bail - no items or folders for lease %d',$this->m_leaseId),1);
			return "";
		}
		$obj = new news($module['fetemplate_id'],$module);
		if (method_exists('news',$module['module_function'])) {
			$this->logMessage(__FUNCTION__,sprintf('invoking news with [%s]',print_r($module,true)),2);
			$html = $obj->{$module['module_function']}();
			return $html;
		}
		else {
			$this->logMessage(__FUNCTION__,sprintf('bail - no function [%s] in news for lease %d',$module['module_function'],$this->m_leaseId),1,true);
		}
	}

	private function blog($module) {
		if (array_key_exists('lease_id',$this->m_module))
			$p_id = $this->m_module['lease_id'];
		else
			$p_id = $this->m_leaseId;
		if ($folders = $this->fetchSingle(sprintf('select * from relations where owner_type = "blogfolder" and related_id = %d and related_type = "lease"',$p_id))) {
			$module['folder_id'] = $folders['related_id'];
		}
		else {
			$folders = array();
			$module['folder_id'] = 0;
			$this->logMessage(__FUNCTION__,sprintf('no blog folder for lease %d',$this->m_leaseId),2);
		}
		if ($items = $this->fetchScalarAll(sprintf('select owner_id as blog_id from relations where owner_type = "blog" and related_id = %d and related_type = "lease"',$p_id)))
			$module['blog_list'] = $items;
		else {
			$items = array();
			$this->logMessage(__FUNCTION__,sprintf('no blog for lease %d',$p_id),2);
		}
		if (count($folders) == 0 && count($items) == 0) {
			$this->logMessage(__FUNCTION__,sprintf('bail - no items or folders for lease %d',$p_id),1);
			return "";
		}
		$obj = new blog($module['fetemplate_id'],$module);
		if (method_exists('blog',$module['module_function'])) {
			$html = $obj->{$module['module_function']}();
			return $html;
		}
		else {
			$this->logMessage(__FUNCTION__,sprintf('bail - no function [%s] in blog for lease %d',$module['module_function'],$p_id),1,true);
		}
	}

	function search() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->init($this->m_dir.$module['outer_html']);
		$outer->setModule($module);
		$flds = $this->config->getFields($module['configuration']);
		$outer->buildForm($flds);
		if (array_key_exists("search-form",$_SESSION)) {
			//
			//	see if there is a previous search - use if no current search
			//
			if (!array_key_exists(__FUNCTION__,$_POST)) $_POST = $_SESSION["search-form"];
		}
		if (array_key_exists(__FUNCTION__,$_POST) && $_POST[__FUNCTION__] == 1) {
			$_SESSION["search-form"] = $_POST;
			$outer->addData($_POST);
			$data = $_POST;
			if ($outer->validate()) {
				$opts = array();
				$having = array();
				if (array_key_exists("body_style",$_POST) && is_array($_POST["body_style"]) && count($_POST["body_style"]) > 0) {
					$opts[] = sprintf(" l.id in (select lease_id from lease_options where code_table = 'body_style' and code_id in (%s))",implode(",",array_values($_POST["body_style"])));
				}
				$make = array();
				if (array_key_exists("domestic",$_POST) && is_array($_POST["domestic"]) && count($_POST["domestic"]) > 0) $make = array_merge($make,$_POST["domestic"]);
				if (array_key_exists("import",$_POST) && is_array($_POST["import"]) && count($_POST["import"]) > 0) $make = array_merge($make,$_POST["import"]);
				if (count($make) > 0) {
					$opts[] = sprintf(" l.make in (%s)",implode(",",array_values($make)));
				}
				if (array_key_exists("province_id",$_POST) && $_POST["province_id"] > 0) {
					$opts[] = sprintf(" l.id in (select l.id from leases, addresses a where a.province_id = %d and a.ownertype = 'member' and a.ownerid = l.owner_id)",$_POST["province_id"]);
				}
				if (array_key_exists("amt_from",$_POST) && $_POST["amt_from"] > 0) {
					$opts[] = sprintf(" l.lease_taxes_inc >= %d",$outer->getData("amt_from"));
				}
				if (array_key_exists("amt_to",$_POST) && $_POST["amt_to"] > 0) {
					$opts[] = sprintf(" l.lease_taxes_inc <= %d",$outer->getData("amt_to"));
				}
				if (array_key_exists("km_from",$_POST) && $_POST["km_from"] > 0) {
					$opts[] = sprintf(" l.mileage >= %d",$outer->getData("km_from"));
				}
				if (array_key_exists("km_to",$_POST) && $_POST["km_to"] > 0) {
					$opts[] = sprintf(" l.mileage <= %d",$outer->getData("km_to"));
				}

				if (array_key_exists("months_from",$_POST) && $_POST["months_from"] > 0) {
					//$opts[] = sprintf(" PERIOD_DIFF(date_format(lease_expiry,'%%Y%%m'),DATE_FORMAT(now(),'%%Y%%m')) >= %d",$outer->getData("months_from"));
					$having[] = sprintf(" months_remaining >= %d",$outer->getData("months_from"));
				}
				if (array_key_exists("months_to",$_POST) && $_POST["months_to"] > 0) {
					//$opts[] = sprintf(" PERIOD_DIFF(date_format(lease_expiry,'%%Y%%m'),DATE_FORMAT(now(),'%%Y%%m')) <= %d",$outer->getData("months_to"));
					$having[] = sprintf(" months_remaining <= %d",$outer->getData("months_to"));
				}
				if (array_key_exists("lease_id",$_POST) && $_POST["lease_id"] > 0) {
					$opts = array(sprintf("l.id = %d",$_POST["lease_id"]));
				}
				$this->logMessage(__FUNCTION__,sprintf("search options are [%s] [%s]",print_r($opts,true), print_r($having,true) ),1);
				//if (count($opts) == 0) {
				//	$outer->addFormError("You must select some search criteria");
				//}
				//else {
					if (strlen($module["inner_html"]) > 0) {
						if (strlen($module["where_clause"]) > 0) {
							$opts[] = sprintf(" l.published = 1 and l.enabled = 1 and l.deleted = 0 and %s", $module["where_clause"]);
						}
						else
							$opts[] = " l.published = 1 and l.enabled = 1 and l.deleted = 0 ";
						$sql = sprintf("select count(0), PERIOD_DIFF(date_format(lease_expiry,'%%Y%%m'),DATE_FORMAT(now(),'%%Y%%m')) as months_remaining from leases l where %s", implode(" and ",$opts));
						if (count($having) > 0) $sql .= sprintf( "having %s", implode(" and ", $having ));
						$tot_ct = $this->fetchScalar($sql);
						$pg = 0;
						$sql = sprintf("select l.*, PERIOD_DIFF(date_format(lease_expiry,'%%Y%%m'),DATE_FORMAT(now(),'%%Y%%m')) as months_remaining from leases l where %s",implode(" and ", $opts));
						if (count($having) > 0) $sql .= sprintf( "having %s", implode(" and ", $having ));
						$pagination = $this->getPagination($sql,$module,$tot_ct,$pg);
						$this->logMessage(__FUNCTION__,sprintf("pagination is [%s] ct [%d] pg [%d]",$pagination,$tot_ct,$pg),1);
						$recs = $this->fetchAll($sql);
						$inner = new Forms();
						$inner->init($this->m_dir.$module['inner_html']);
						$leases = array();
						$ct = 0;
						foreach($recs as $key=>$value) {
							$ct += 1;
							if ($module['rows'] > 0 && $ct > $module['columns']) {
								$ct = 1;
								if ($this->hasOption('grpSuffix')) $leases[] = $this->getOption('grpSuffix');
								if ($this->hasOption('grpPrefix')) 
									$leases[] = $this->getOption('grpPrefix');
								else
									$leases[] = '<div class="clearfix"></div>';
							}
							$inner->reset();
							$inner->addData($this->formatData($value));
							$leases[] = $inner->show();
						}
						if ($this->hasOption('grpSuffix')) $news[] = $this->getOption('grpSuffix');
						$this->logMessage(__FUNCTION__,sprintf("leases are [%s]",print_r($leases,true)),1);
						$inner->init($this->m_dir.$module['parm1']);
						$inner->addTag("leases",implode("",$leases),false);
						$inner->addTag("count",$tot_ct == "" ? 0 : $tot_ct);
						$inner->addTag("pagination",$pagination,false);
						$this->logMessage(__FUNCTION__,sprintf("inner is [%s]",print_r($inner,true)),1);
						$outer->addTag("leases",$inner->show(),false);
					}
				//}
			}
		}
		return $outer->show();
	}

	function create() {
	}

	function getModels() {
		$m_id = array_key_exists("m_id",$_REQUEST) ? $_REQUEST["m_id"] : 0;
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->init($this->m_dir.$module['outer_html']);
		$outer->setModule($module);
		$flds = $this->config->getFields($module['configuration']);
		$flds["model"]["sql"] = sprintf("select id, model from lease_model where make_id = %d order by model",$m_id);
		$outer->buildForm($flds);
		return $outer->show();
	}

	function getYears() {
		$m_id = array_key_exists("m_id",$_REQUEST) ? $_REQUEST["m_id"] : 0;
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->init($this->m_dir.$module['outer_html']);
		$outer->setModule($module);
		$flds = $this->config->getFields($module['configuration']);
		if (!($rec = $this->fetchSingle(sprintf("select * from lease_model where id = %d",$m_id))))
			$rec = array("start_year"=>"2005","end_year"=>date("Y",strtotime("today + 1 year")));
		$flds["year"]["sequence"] = sprintf("%d|%d",$rec["start_year"],$rec["end_year"] == "9999" ? date("Y",strtotime("today + 1 year")) : $rec["end_year"]);
		$outer->buildForm($flds);
		return $outer->show();
	}

	function vehicleInfo() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->init($this->m_dir.$module['outer_html']);
		$outer->setModule($module);
		$flds = $this->config->getFields($module['configuration']);
		$outer->buildForm($flds);
		if (array_key_exists("lease",$_SESSION) && array_key_exists("vehicle",$_SESSION["lease"])) $outer->addData($_SESSION["lease"]["vehicle"]["data"]);
		if (count($_POST) > 0 && array_key_exists(__FUNCTION__,$_POST) && $_POST[__FUNCTION__] > 0) {
			$outer->addData($_POST);
			//$this->logMessage(__FUNCTION__,sprintf("test 1a"),1);
			//$x = $this->fetchOptions(sprintf("select id,model from lease_model where enabled=1 and make_id=1 order by model",$outer->getData("make")));
			//$this->logMessage(__FUNCTION__,sprintf("test 2a [%s]",print_r($x,true)),1);
			$outer->getField("model")->setOptions($this->fetchOptions(sprintf("select id,model from lease_model where enabled=1 and make_id=%d order by model",$outer->getData("make"))));
			if (!($rec = $this->fetchSingle(sprintf("select * from lease_model where id = %d",$outer->getData("model")))))
				$rec = array("start_year"=>"2005","end_year"=>date("Y",strtotime("today + 1 year")));
			$outer->getField("year")->addAttribute("sequence",sprintf("%d|%d",$rec["start_year"],$rec["end_year"] == "9999" ? date("Y",strtotime("today + 1 year")) : $rec["end_year"]));
			if ($outer->validate()) {
				$_SESSION["lease"]["vehicle"]["data"] = $_POST;
				$_SESSION["lease"]["vehicle"]["config"] = $module['configuration'];
				$outer->init($this->m_dir.$module['inner_html']);
			}
		}
		return $outer->show();
	}

	function leaseInfo() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->init($this->m_dir.$module['outer_html']);
		$outer->setModule($module);
		$flds = $this->config->getFields($module['configuration']);
		$outer->buildForm($flds);
		if (array_key_exists("lease",$_SESSION) && array_key_exists("financial",$_SESSION["lease"])) $outer->addData($_SESSION["lease"]["financial"]["data"]);
		if (count($_POST) > 0 && array_key_exists(__FUNCTION__,$_POST) && $_POST[__FUNCTION__] > 0) {
			if (count($_FILES) > 0) {
				$return = array();
				$messages = array();
				if (!($status = $this->processUploadedFiles(array('Image'),$return,$messages))) {
					foreach($messages as $key=>$value)
						$outer->addFormError($value);
				} else {
					foreach($return as $key=>$value) {
						$_POST[$key] = $value["name"];
					}
				}
			}
			$outer->addData($_POST);
			$_SESSION["lease"]["financial"]["data"] = $_POST;
			if ($status = $outer->validate()) {
				$data = array_merge($_SESSION["lease"]["vehicle"]["data"],$_POST);
				$tmp = $this->config->getFields($_SESSION["lease"]["vehicle"]["config"]);
				$flds = array_merge($tmp,$flds);
				$outer->buildForm($flds);
				$outer->addData($data);
				$outer->validate();	// rechecks step 2 info
				$this->logMessage(__FUNCTION__,sprintf("merged data is [%s] fields are [%s]",print_r($data,true),print_r($flds,true)),1);
				$tbl = $data["tbl"];
				unset($data["tbl"]);
				$rec = array();
				foreach($flds as $key=>$value) {
					if ((!array_key_exists("database",$value)) || $value["database"] == true) {
						$rec[$value["name"]] = $outer->getData($value["name"]);
					}
				}
				$this->logMessage(__FUNCTION__,sprintf("values for db are [%s]",print_r($rec,true)),1);
				$rec["owner_id"] = $this->getUserInfo("id");
				$rec["created"] = date(DATE_ATOM);
				$rec["enabled"] = 1;
				$rec["published"] = 0;
				$stmt = $this->prepare(sprintf('insert into leases(%s) values(%s)', implode(', ',array_keys($rec)), str_repeat('?, ', count($rec)-1).'?'));
				$stmt->bindParams(array_merge(array(str_repeat("s",count($rec))),array_values($rec)));
				$this->beginTransaction();
				if ($valid = $stmt->execute()) {
					$id = $this->insertId();
					$p = array("lease_id"=>$id,"folder_id"=>$outer->getData("f_id"));
					$stmt = $this->prepare(sprintf("insert into lease_by_folder( %s ) values(%s? )",implode(",",array_keys($p)), str_repeat("?, ",count($p)-1)));
					$stmt->bindParams(array_merge(array(str_repeat("s",count($p))),array_values($p)));
					$valid &= $stmt->execute();
				}
				if ($valid) {
					//
					//	store the options
					//
					foreach($tbl as $key=>$value) {
						$tmp = is_array($value) ? $value : array(0=>$value);
						foreach($tmp as $skey=>$svalue) {
							$stmt = $this->prepare(sprintf("insert into lease_options(lease_id, code_table, code_id) values(?, ?, ?)"));
							$stmt->bindParams(array("dsd",$id,$key,$svalue));
							$valid &= $stmt->execute();
						}
					}
				}
				if ($valid) {
					$dir = $this->m_images.$id."/";
					mkdir(".".$dir);
					//rename(".".$outer->getData("image1"),".".$dir.$tmp[count($tmp)-1]);
					$images = array();	//"image1"=>str_replace("/files/",$dir,$outer->getData("image1")));
					for($x = 1; $x < 11; $x++) {
						if (strlen($tmp = $outer->getData("image".$x)) > 0) {
							$p = explode("/",$outer->getData("image".$x));
							rename(".".$outer->getData("image".$x),".".$dir.$p[count($p)-1]);
							$images["image".$x] = str_replace("/files/",$dir,$tmp);
						}
					}
					$stmt = $this->prepare(sprintf("update leases set %s=? where id = %d", implode("=?, ",array_keys($images)), $id ));
					$stmt->bindParams(array_merge(array(str_repeat("s",count($images))),array_values($images)));
					$stmt->execute();
				 	$this->commitTransaction();
					$emails = $this->configEmails("contact");
					$body = new Forms();
					$mailer = new MyMailer();
					$mailer->Subject = sprintf("New Lease Posted - %s", SITENAME);
					$body = new Forms();
					$sql = sprintf('select * from htmlForms where class = %d and type = "newLease"',$this->getClassId('leasing'));
					$html = $this->fetchSingle($sql);

					$body->setHTML($html['html']);
					$data = $this->formatData($this->fetchSingle(sprintf("select * from leases where id = %d",$id)));
					$data["user"] = $_SESSION["user"]["info"];
					$data["user"]["address"] = $this->fetchSingle(sprintf("select * from addresses where ownertype='member' and ownerid = %d",$this->getUserInfo("id")));
					$body->addData($data);
					$body->setOption('formDelimiter','{{|}}');
					$this->logMessage(__FUNCTION__,sprintf("body is [%s]",print_r($body,true)),1);
					$mailer->Body = $body->show();
					$mailer->From = 'noreply@'.HOSTNAME;
					$mailer->FromName = $data['user']['firstname'].' '.$data['user']['lastname'];
					$mailer->AddReplyTo($data['user']['email'],$data['user']['firstname'].' '.$data['user']['lastname']);
					$mailer->IsHTML(true);
					foreach($emails as $key=>$value) {
						$mailer->addAddress($value['email'],$value['name']);
					}
					if (!$mailer->Send()) {
						$this->logMessage(__FUNCTION__,sprintf("Email send failed [%s]",print_r($mailer,true)),1,true);
					}
				 	$outer->init($this->m_dir.$module['inner_html']);
				}
				else {
				 	$outer->addFormError("An error occurred. The Web Master has been notified");
				 	$this->rollbackTransaction();
				}
			}
		}
		return $outer->show();
	}

	function gallery() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->init($this->m_dir.$module['outer_html']);
		$outer->setModule($module);
		$flds = $this->config->getFields($module['configuration']);
		if (!($data = $this->fetchSingle(sprintf("select * from leases where id = %d",$this->m_leaseId))))
			$data = array("id"=>0,"image1"=>"","image2"=>"","image3"=>"","image4"=>"");
		$data = $this->formatData($data);
		$outer->addData($data);
		$inner = new Forms();
		$inner->init($this->m_dir.$module['inner_html']);
		$images = array();
		for($x = 1; $x < 11; $x++) {
			if (array_key_exists("img_image".$x,$data)) {
				$inner->addData(array("image"=>$data["image".$x],"img_image"=>$data["img_image".$x]));
				$this->logMessage(__FUNCTION__,sprintf("inner is [%s]",print_r($inner,true)),1);
				$images[] = $inner->show();
			}
		}
		$outer->addTag("images",implode("",$images),false);
		return $outer->show();
	}

	function amenities() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->init($this->m_dir.$module['outer_html']);
		$outer->setModule($module);
		$flds = $this->config->getFields($module['configuration']);
		if (!($data = $this->fetchSingle(sprintf("select * from leases where id = %d",$this->m_leaseId))))
			$data = array("id"=>0);
		$data = $this->formatData($data);
		$outer->addData($data);
		$inner = new Forms();
		$inner->init($this->m_dir.$module['inner_html']);
		$opt = new Forms();
		$opt->init($this->m_dir.$module['parm1']);
		$ct = 0;
		for($x = 0; $x < $module["columns"]; $x++) {
			$options[$x] = array();
		}
		foreach($data["options_value"] as $key=>$rec) {
			$inner->addTag("title",$key);
			$opts = array();
			foreach($rec as $skey=>$srec) {
				$opt->addTag("option",$srec);
				$opts[] = $opt->show();
			}
			$inner->addTag("options",implode("",$opts),false);
			$options[$ct][] = $inner->show();
			$ct += 1;
			if ($ct >= $module["columns"]) $ct = 0;
		}
		$this->logMessage(__FUNCTION__,sprintf("options is [%s] module [%s]",print_r($options,true),print_r($module,true)),1);
		$result = array();
		foreach($options as $key=>$rec) {
			$result[] = $this->getOption('grpPrefix');
			$result[] = implode("",array_values($rec));
			$result[] = $this->getOption('grpSuffix');
		}
		$outer->addTag("options",implode("",$result),false);
		return $outer->show();
	}

	function myLeases() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->setModule($module);
		$outer->init($this->m_dir.$module['outer_html']);
		$sql = sprintf("select *, 0 as folder_id from leases where owner_id = %d",$this->getUserInfo("id"));
		if ($this->hasOption("pagination")) {
			$p = $this->depairOption($this->getOption("pagination"));
			foreach($p as $key=>$value) {
				$p[$key] = sprintf("%s%s%s",ADMIN,$this->m_dir,$value);
			}
			$pagination = $this->getPagination($sql,$module,$recordCount,$p);
		}
		else $pagination = $this->getPagination($sql,$module,$recordCount);
		$outer->addTag('recordCount',$recordCount);
		$leases = $this->fetchAll($sql);
		$this->logMessage(__FUNCTION__,sprintf('sql [%s] count [%d]',$sql,count($leases)),2);
		$return = array();
		$frm = new Forms();
		$frm->init($this->m_dir.$module['inner_html']);
		$ct = 0;
		$flds = $this->config->getFields($module['configuration']);
		if ($this->hasOption('grpPrefix')) $return[] = $this->getOption('grpPrefix');
		foreach($leases as $key=>$lease) {
			if ($lease['folder_id'] == 0) $lease['folder_id'] = $module['folder_id'];
			$ct += 1;
			if ($module['rows'] > 0 && $ct > $module['columns']) {
				$ct = 1;
				if ($this->hasOption('grpSuffix')) $return[] = $this->getOption('grpSuffix');
				if ($this->hasOption('grpPrefix')) 
					$return[] = $this->getOption('grpPrefix');
				else
					$return[] = '<div class="clearfix"></div>';
			}
			$frm->reset();
			$frm->buildForm($flds);
			$frm->addTag('sequence',$ct);
			if ($lease['folder_id'] > 0 && $fldr = $this->fetchSingle(sprintf('select * from lease_folders where id = %d and enabled = 1',$lease['folder_id']))) {
				$lease['folder'] = $this->formatFolder($fldr);
			}
			$frm->addData($this->formatData($lease));
			$subdata = $this->subForms($this->m_module['fetemplate_id'],null,array('folder_id'=>$module['folder_id'],'lease_id'=>$lease['id']),'inner');
			$this->logMessage(__FUNCTION__,sprintf('subforms [%s]',print_r($subdata,true)),3);
			foreach($subdata as $key=>$value) {
				$frm->addTag($key,$value,false);
			}
			$return[] = $frm->show();
		}
		if ($this->hasOption('grpSuffix')) $return[] = $this->getOption('grpSuffix');
		$outer->addTag('pagination',$pagination,false);
		if ($module['folder_id'] != 0) {
			if ($fldr = $this->fetchSingle(sprintf('select * from lease_folders where id = %d',$module['folder_id'])))
				$outer->addData($this->formatFolder($fldr));
		}
		$outer->addTag('leases',implode('',$return),false);
		$subdata = $this->subForms($this->m_module['fetemplate_id'],null,array('folder_id'=>$module['folder_id']),'outer');
		$this->logMessage(__FUNCTION__,sprintf('subforms [%s]',print_r($subdata,true)),3);
		foreach($subdata as $key=>$value) {
			$outer->addTag($key,$value,false);
		}
		$tmp = $outer->show();
		return $tmp;
	}

	function getModuleInfo() {
		return parent::getModuleList(array('leases','listing','details','folderRelations','itemRelations','search','vehicleInfo','leaseInfo','getModels','getYears','gallery','amenities','myLeases'));
	}

}

?>
