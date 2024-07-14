<?php

class members extends Backend {

	private $m_tree = 'members_folders';
	private $m_content = 'members';
	private $m_junction = 'members_by_folder';
	private $m_media = 'members_media';
	private $m_pagination = 0;
	private $m_perrow = 5;

	public function __construct() {
		$this->M_DIR = 'backend/modules/members/';
		$this->setTemplates(
			array(
				'deleteItem'=>$this->M_DIR.'forms/deleteItem.html',
				'deleteItemResult'=>$this->M_DIR.'forms/deleteItemResult.html',
				'main'=>$this->M_DIR.'members.html',
				'form'=>$this->M_DIR.'forms/form.html',
				'showContentTree'=>$this->M_DIR.'forms/contenttree.html',
				'membersInfo'=>$this->M_DIR.'forms/membersInfo.html',
				'folderProperties'=>$this->M_DIR.'forms/folder.html',
				'showFolderContent'=>$this->M_DIR.'forms/folderContent.html',
				'folderInfo'=>$this->M_DIR.'forms/folderInfo.html',
				'articleList'=>$this->M_DIR.'forms/articleList.html',
				'showSearchForm'=>$this->M_DIR.'forms/searchForm.html',
				'addContent'=>$this->M_DIR.'forms/addContent.html',
				'addressForm'=>$this->M_DIR.'forms/addressForm.html',
				'addressList'=>$this->M_DIR.'forms/addressList.html',
				'editAddress'=>$this->M_DIR.'forms/editAddress.html',
				'editAddressSuccess'=>$this->M_DIR.'forms/editAddressSuccess.html',
				'getProfile'=>$this->M_DIR.'forms/getProfile.html',
				'header'=>$this->M_DIR.'forms/heading.html',
				'editProfile'=>$this->M_DIR.'forms/editProfile.html',
				'editProfileResult'=>$this->M_DIR.'forms/editProfileResult.html',
				'addContentResult'=>$this->M_DIR.'forms/addContentResult.html',
				'addFolder'=>$this->M_DIR.'forms/addFolder.html',
				'addItem'=>$this->M_DIR.'forms/addItem.html',
				'showOrders'=>$this->M_DIR.'forms/showOrders.html',
				'memberOrder'=>$this->M_DIR.'forms/memberOrder.html',
				'loadMedia'=>$this->M_DIR.'forms/loadMedia.html',
				'editMedia'=>$this->M_DIR.'forms/editMedia.html',
				'listMedia'=>$this->M_DIR.'forms/listMedia.html',
				'editMediaSuccess'=>$this->M_DIR.'forms/editMediaSuccess.html',
				'deleteMedia'=>$this->M_DIR.'forms/deleteMedia.html'
			)
		);
		$this->setFields(array(
			'editMedia'=>array(
				'name'=>array('type'=>'input','required'=>true),
				'description'=>array('type'=>'textarea','required'=>false,'class'=>'mceAdvanced'),
				'teaser'=>array('type'=>'textarea','required'=>false,'class'=>'mceSimple'),
				'filename'=>array('type'=>'fileupload','required'=>true,'validation'=>'fileupload','database'=>false),
				'submit'=>array('type'=>'submitbutton','name'=>'save','value'=>'Save','database'=>false),
				'id'=>array('type'=>'tag','database'=>false),
				'member_id'=>array('type'=>'tag'),
				'folder_id'=>array('type'=>'tag'),
				'editMedia'=>array('type'=>'hidden','value'=>1,'database'=>false)
			),
			'loadMedia'=>array(
				'member_id'=>array('type'=>'tag'),
				'folder_id'=>array('type'=>'tag')
			),
			'listMedia'=>array(
			),
			'deleteItem'=>array(
				'options'=>array('name'=>'deleteItem','database'=>false),
				'j_id'=>array('type'=>'tag'),
				'deleteItem'=>array('type'=>'hidden','value'=>1),
				'cancel'=>array('type'=>'radiobutton','name'=>'action','value'=>'cancel','checked'=>'checked'),
				'one'=>array('type'=>'radiobutton','name'=>'action','value'=>'one'),
				'all'=>array('type'=>'radiobutton','name'=>'action','value'=>'all')
			),
			'header'=>array(),
			'addressForm'=>array(),
			'addressList'=>array(
				'line1'=>array('type'=>'tag','reformatting'=>true),
				'line2'=>array('type'=>'tag','reformatting'=>true),
				'city'=>array('type'=>'tag','reformatting'=>true),
			),
			'editProfile'=>array(
				'options'=>array('method'=>'post','action'=>'/modit/ajax/editProfile/members','name'=>'editProfile'),
				'profile'=>array('type'=>'textarea','required'=>true,'id'=>'profile_editor','prettyName'=>'Profile','class'=>'mceAdvanced'),
				'teaser'=>array('type'=>'textarea','required'=>false,'id'=>'profile_teaser','prettyName'=>'Teaser','class'=>'mceSimple'),
				'image1'=>array('type'=>'tag'),
				'image2'=>array('type'=>'tag'),
				'imagesel_c'=>array('type'=>'image','unknown'=>true,'database'=>false,'id'=>'imagesel_c'),
				'imagesel_d'=>array('type'=>'image','unknown'=>true,'database'=>false,'id'=>'imagesel_d'),
				'editProfile'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'p_id'=>array('type'=>'tag','database'=>false),
				'submit'=>array('type'=>'submitbutton','value'=>'Save Profile','database'=>false)
			),
			'getProfile'=>array(
				'id'=>array('type'=>'tag'),
				'title'=>array('type'=>'tag')
			),
			'editAddress'=>array(
				'options'=>array('method'=>'post','action'=>'/modit/ajax/editAddress/members'),
				'editAddress'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'addresstype'=>array('type'=>'select','required'=>true,'sql'=>'select id, value from code_lookups where type = "memberAddressTypes"','prettyName'=>'Address Type'),
				'ownertype'=>array('type'=>'hidden','value'=>'member'),
				'ownerid'=>array('type'=>'hidden','id'=>'ownerid'),
				'firstname'=>array('type'=>'input','required'=>false,'prettyName'=>'First Name'),
				'lastname'=>array('type'=>'input','required'=>false,'prettyName'=>'Last Name'),
				'email'=>array('type'=>'input','required'=>false,'prettyName'=>'Email','validation'=>'email'),
				'line1'=>array('type'=>'input','required'=>true,'prettyName'=>'Address Line 1'),
				'line2'=>array('type'=>'input','required'=>false),
				'city'=>array('type'=>'input','required'=>true,'prettyName'=>'City'),
				'country_id'=>array('type'=>'countryselect','required'=>true,'id'=>'country_id','prettyName'=>'Country'),
				'province_id'=>array('type'=>'provinceselect','required'=>true,'id'=>'province_id','prettyName'=>'Province'),
				'postalcode'=>array('type'=>'input','required'=>true,'prettyName'=>'Postal Code'),
				'phone1'=>array('type'=>'input'),
				'phone2'=>array('type'=>'input'),
				'fax'=>array('type'=>'input'),
				'tax_address'=>array('type'=>'hidden'),
				'addresses'=>array('type'=>'select','database'=>false,'id'=>'addressSelector'),
				'latitude'=>array('type'=>'textfield','required'=>true,'class'=>'def_field_small','value'=>0.0,'validation'=>'number'),
				'longitude'=>array('type'=>'textfield','required'=>true,'class'=>'def_field_small','value'=>0.0,'validation'=>'number'),
				'geocode'=>array('type'=>'checkbox','value'=>1,'database'=>false),
				'save'=>array('type'=>'submitbutton','database'=>false,'value'=>'Save Address')
			),
			'addContent'=>array(
				'options'=>array('method'=>'post','action'=>'/modit/ajax/addContent/members'),
				'firstname'=>array('type'=>'input','required'=>true,'prettyName'=>'First Name'),
				'lastname'=>array('type'=>'input','required'=>true,'prettyName'=>'Last Name'),
				'company'=>array('type'=>'input','required'=>false,'prettyName'=>'Company'),
				'password'=>array('type'=>'password','validation'=>'password','required'=>false,'autocomplete'=>'off'),
				'enabled'=>array('type'=>'checkbox','value'=>1),
				'email'=>array('type'=>'input','required'=>false,'validation'=>'email','prettyName'=>'Email','autocomplete'=>'off'),
				'expires'=>array('type'=>'datepicker','required'=>false,'id'=>'addExpires','prettyName'=>'Expires'),
				'featured'=>array('type'=>'checkbox','required'=>false,'value'=>1),
				'enabled'=>array('type'=>'checkbox','required'=>false,'value'=>1),
				'deleted'=>array('type'=>'checkbox','required'=>false,'value'=>1),
				'id'=>array('type'=>'tag','database'=>false),
				'biography'=>array('type'=>'textarea','required'=>false,'id'=>'memberBio','class'=>'mceAdvanced'),
				'submit'=>array('type'=>'submitbutton','database'=>false,'value'=>'Save Member'),
				'addContent'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'addresses'=>array('type'=>'select','database'=>false,'id'=>'addressSelector'),
				'addresses'=>array('type'=>'select','database'=>false,'id'=>'addressSelector'),
				'image1'=>array('type'=>'tag'),
				'image2'=>array('type'=>'tag'),
				'imagesel_a'=>array('type'=>'image','unknown'=>true,'database'=>false,'id'=>'imagesel_a'),
				'imagesel_b'=>array('type'=>'image','unknown'=>true,'database'=>false,'id'=>'imagesel_b'),
				'destFolders'=>array('name'=>'destFolders','type'=>'select','multiple'=>'multiple','required'=>false,'id'=>'destFolders','database'=>false,'options'=>$this->nodeSelect(0, 'members_folders', 2, false, false),'reformatting'=>false,'prettyName'=>'Member Of')
			),
			'showSearchForm'=>array(
				'options'=>array('action'=>'showSearchForm','name'=>'searchForm','id'=>'search_form'),
				'opt_created'=>array('type'=>'select','name'=>'opt_created','lookup'=>'search_options'),
				'opt_expires'=>array('type'=>'select','name'=>'opt_expires','lookup'=>'search_options'),
				'opt_name'=>array('type'=>'select','name'=>'opt_name','lookup'=>'search_string'),
				'name'=>array('type'=>'input','required'=>false),
				'opt_email'=>array('type'=>'select','name'=>'opt_email','lookup'=>'search_string'),
				'email'=>array('type'=>'input','required'=>false),
				'opt_username'=>array('type'=>'select','name'=>'opt_username','lookup'=>'search_string'),
				'username'=>array('type'=>'input','required'=>false),
				'created'=>array('type'=>'datepicker','required'=>false),
				'expires'=>array('type'=>'datepicker','required'=>false,'id'=>'searchExpires'),
				'enabled'=>array('type'=>'select','lookup'=>'boolean'),
				'deleted'=>array('type'=>'select','lookup'=>'boolean'),
				'featured'=>array('type'=>'select','lookup'=>'boolean'),
				'showSearchForm'=>array('type'=>'hidden','value'=>1),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'sortby'=>array('type'=>'hidden','value'=>'created'),
				'sortorder'=>array('type'=>'hidden','value'=>'desc'),
				'folder'=>array('type'=>'select','optionslist' => array('table'=>$this->m_tree,'root'=>0,'indent'=>2,'inclusive'=>false),'database'=>false),
				'nonmember'=>array('type'=>'checkbox','value'=>1),
				'quicksearch'=>array('type'=>'input','name'=>'quicksearch','required'=>false),
				'opt_quicksearch'=>array('type'=>'hidden','value'=>'like'),
				'pager'=>array('type'=>'select','required'=>true,'value'=>$this->m_perrow,'lookup'=>'paging','id'=>'pager'),
				'submit'=>array('type'=>'submitbutton','value'=>'Search')
			),
			'showFolderContent'=>array(
				'options'=>array('action'=>'showPageContent'),
				'description'=>array('type'=>'tag','reformatting'=>false),
				'image'=>array('type'=>'image','unknown'=>true),
				'rollover_image'=>array('type'=>'image','unknown'=>true),
				'sortby'=>array('type'=>'hidden','value'=>'created'),
				'sortorder'=>array('type'=>'hidden','value'=>'desc'),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'pager'=>array('type'=>'select','required'=>true,'value'=>$this->m_perrow,'lookup'=>'paging','id'=>'pager'),
				'showFolderContent'=>array('type'=>'hidden','value'=>1)
			),
			'main' => array(
				'test'=>array('type'=>'tag')
			),
			'form' => array(),
			'folderProperties' => array(
				'options'=>array(
					'action'=>'/modit/members/showPageProperties',
					'method'=>'post'
				),
				'title'=>array('type'=>'textfield','required'=>true,'prettyName'=>'Title'),
				'showPageProperties'=>array('type'=>'hidden','value'=>1, 'database'=>false),
				'alternate_title'=>array('type'=>'textfield','required'=>false),
				'p_id'=>array('type'=>'select','required'=>true,'database'=>false,'optionslist'=>array('table'=>$this->m_tree,'root'=>0,'indent'=>2,'inclusive'=>true)),
				'enabled'=>array('type'=>'checkbox','required'=>false,'value'=>1),
				'notes'=>array('type'=>'textarea','required'=>false,'class'=>'mceNoEditor'),
				'description'=>array('type'=>'textarea','required'=>false, 'id'=>'folderDescription','class'=>'mceAdvanced'),
				'teaser'=>array('type'=>'textarea','required'=>false, 'id'=>'folderTeaser','class'=>'mceSimple'),
				'id'=>array('type'=>'tag', 'database'=>false),
				'image'=>array('type'=>'tag'),
				'rollover_image'=>array('type'=>'tag'),
				'imagesel_a'=>array('type'=>'image','unknown'=>true,'database'=>false,'id'=>'imagesel_a'),
				'imagesel_b'=>array('type'=>'image','unknown'=>true,'database'=>false,'id'=>'imagesel_b'),
				'template_id'=>array('type'=>'select','required'=>false,'sql'=>'select template_id,title from templates group by title order by title'),
				'submit'=>array('type'=>'submitbutton','value'=>'Save','database'=>false)
			),
			'showContentTree' => array(),
			'membersInfo' => array(),
			'showMembersContent' => array(),
			'folderInfo' => array(
				'title'=>array('type'=>'tag'),
				'alternate_title'=>array('type'=>'tag'),
				'notes'=>array('type'=>'tag','reformatting'=>false),
				'description'=>array('type'=>'tag','reformatting'=>false),
				'image' => array('type'=>'image','unknown'=>true),
				'rollover_image'=>array('type'=>'image','unknown'=>true)
			),
			'articleList' => array(
				'id'=>array('type'=>'tag'),
				'title'=>array('type'=>'tag'),
				'created'=>array('type'=>'datestamp','mask'=>'d-M-Y h:m:i'),
				'enabled'=>array('type'=>'booleanIcon'),
				'featured'=>array('type'=>'booleanIcon'),
				'deleted'=>array('type'=>'booleanIcon'),
				'expires'=>array('type'=>'datestamp','mask'=>'d-M-Y','suppressNull'=>true)
			),
			'showOrders'=>array(
				'order_status'=>array('type'=>'select','multiple'=>true,'required'=>false,'lookup'=>'orderStatus'),
				'search'=>array('type'=>'submitbutton','value'=>'Search'),
				'o_id'=>array('type'=>'hidden'),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'showOrders'=>array('type'=>'hidden','value'=>1)
			)
		));
	
		parent::__construct ();
	}
	
	function __destruct() {
	
	}

	function show($injector = null) {
		$form = new Forms();
		$form->init($this->getTemplate('main'),array('name'=>'adminMenu'));
		$frmFields = $form->buildForm($this->getFields('main'));
		if ($injector == null || strlen($injector) == 0) {
			$injector = $this->moduleStatus(true);
		}
		$form->addTag('injector', $injector, false);
		return $form->show();
	}

	function showForm() {
		$form = new Forms();
		$form->init($this->getTemplate('form'),array('name'=>'adminMenu'));
		$flds = $form->buildForm($this->getFields('form'));
		$form->getField('contenttree')->addAttribute('value',$this->buildTree($this->m_tree));
		if (count($_POST) > 0) {
			$form->addData($_POST);
			if ($form->validate()) {
				$this->addMessage('Validated');
				$tmp = array();
				foreach($_POST as $key=>$value) {
					$fld = new tag();
					$tmp[] = $fld->show(sprintf('name: %s value: [%s] post: [%s]', $key, $form->getData($key), $value));
				}
				$form->addTag('info', implode('<br/>',$tmp), false);
			}
			else {
				$this->addError('Validation failed');
			}
		}
		return $form->show();
	}

	function showContentTree() {
		$form = new Forms();
		$form->init($this->getTemplate('showContentTree'),array());
		$form->addTag('tree',$this->buildTree($this->m_tree, array(), "ajaxBuild", array(0=>"<ol>%s</ol>",1=>"<li class='collapsed'>%s%s</li>",3=>"")),false);
		if ($this->isAjax())
			return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
		else
			return $form->show();
	}

	function showPageProperties($fromMain = false) {
		$result = array();
		$return = 'true';
		if (!(array_key_exists('id',$_REQUEST) && $data = $this->fetchSingle(sprintf('select * from %s where id = %d',$this->m_tree, $_REQUEST['id']))))
			$data = array('enabled'=>1,'id'=>0,'p_id'=>0,'image'=>'','rollover_image'=>'');
		else {
			//
			//	get the parent node as well
			//
			$data['p_id'] = 0;
			if ($data['level'] > 1) {
				if ($p = $this->fetchSingle(sprintf('select * from %s where level = %d and left_id < %d and right_id > %d', $this->m_tree, $data['level'] - 1, $data['left_id'], $data['right_id'])))
					$data['p_id'] = $p['id'];
			}
		}
		$form = new Forms();
		$form->init($this->getTemplate('folderProperties'),array('name'=>'folderProperties'));
		$frmFlds = $this->getFields('folderProperties');
		$data['imagesel_a'] = $data['image'];
		$data['imagesel_b'] = $data['rollover_image'];
		$customFields = new custom();
		if (method_exists($customFields,'memberFolderDisplay')) {
			$custom = $customFields->memberFolderDisplay($data);
			$form->addTag('customTab',sprintf('<li><a href="#tabs-custom">%s</a></li>',$custom['description']),false);
			$html = $form->getHTML();
			$html = str_replace('%%customInfo%%',$custom['form'],$html);
			$form->setHTML($html);
			$frmFlds = array_merge($frmFlds,$custom['fields']);
			$this->logMessage(__FUNCTION__,sprintf("custom [%s]",print_r($custom,true)),1);
		}
		$frmFlds = $form->buildForm($frmFlds);
		$form->addData($data);
		if (count($_POST) > 0 && array_key_exists('showPageProperties',$_POST)) {
			$_POST['imagesel_a'] = $_POST['image'];
			$_POST['imagesel_b'] = $_POST['rollover_image'];
			$form->addData($_POST);
			$valid = $form->validate();
			if ($valid) {
				if (array_key_exists('options',$frmFlds)) unset($frmFlds['options']);
				$values = array();
				$flds = array();
				if ($data['id'] == 0) {
					$mptt = new mptt($this->m_tree);
					$data['id'] = $mptt->add($_POST['p_id'],999,array('title'=>'to be replaced'));
				} 
				else {
					//
					//	did we move the parent folder?
					//
					if ($data['level'] > 1)
						$parent = $this->fetchSingle(sprintf('select * from %s where level = %d and left_id < %d and right_id > %d', $this->m_tree, $data['level'] - 1, $data['left_id'], $data['right_id']));
					else $parent['id'] = 0;
					if ($_POST['p_id'] != $parent['id']) {
						$this->logMessage('showPageProperties', sprintf('moving [%d] to [%d] posted[%d]',$data['id'],$parent['id'], $_POST['p_id']), 1);
						$mptt = new mptt($this->m_tree);
						$mptt->move($data['id'], $_POST['p_id']);
					}
				}
				foreach($frmFlds as $key=>$fld) {
					if (!(array_key_exists('database',$fld) && $fld['database'] == false)) {
						$values[] = $form->getData($fld['name']);
						if ($data['id'] > 0)
							$flds[] = sprintf('%s = ?',$fld['name']);
						else
							$flds[] = $fld['name'];
					}
				}
				$stmt = $this->prepare(sprintf('update %s set %s where id = %d',$this->m_tree,implode(',',$flds),$data['id']));
				$stmt->bindParams(array_merge(array(str_repeat('s', count($flds))),$values));
				if ($stmt->execute()) {
					if (method_exists($customFields,'memberFolderUpdate')) {
						$customFields->memberFolderUpdate(array_merge(array('id'=>$data["id"]),$flds),$_REQUEST);
					}
					if ($this->isAjax()) {
						$this->logMessage('showPageProperties', 'executing ajax success return', 3);
						$this->addMessage('Record successfully added');
						return $this->ajaxReturn(array(
								'status'=>'true',
								'html'=>'',
								'url'=>'/modit/members?p_id='.$data['id']
						));
					}
				} else {
					$this->addError('Error occurred');
					$form->addTag('errorMessage',$this->showErrors(),false);
					if ($this->isAjax()) {
						$this->logMessage('showPageProperties', 'executing ajax error return', 3);
						return $this->ajaxReturn(array(
								'status'=>'false',
								'html'=>$form->show()
						));
					}
				}
				$form->addTag('errorMessage','Record added succesfully');
			}
			else {
				$return = 'false';
				$form->addTag('errorMessage','Form validation failed');
			}
		}
	
		if ($this->isAjax())
			return $this->ajaxReturn(array(
					'status'=>$return,
					'html'=>$form->show()
			));
		elseif ($fromMain)
		return $form->show();
		else
			$this->show($form->show());
	}

	function ajaxBuild($data, $table, $wrappers, $submenu) {
		switch($table) {
			case $this->m_tree:
				$value = new tag(false);
				$mptt = new mptt($table);
				$children = $mptt->fetchChildren($data['id']);
				if (count($_REQUEST) > 0 && array_key_exists('p_id',$_REQUEST)) {
					$expanded = $_REQUEST['p_id'] == $data['id'] ?  'active' : '';
				}
				else $expanded='';
				if (count($submenu) > 0) {
					$return = array('value'=>sprintf('<div class="wrapper"><div class="spacer"><a href="#" class="toggler" onclick="toggle(this);return false;">+</a></div><a href="#" id="li_%d" class="%s icon_folder info">%s</a></div>',$data['id'], $expanded, htmlspecialchars($data['title'])),'submenu'=>$submenu);
				}
				else {
					$return = array('value'=>sprintf('<div class="wrapper"><div class="spacer">&nbsp;</div><a href="#" id="li_%d" class="%s icon_folder info">%s</a></div>',$data['id'], $expanded, htmlspecialchars($data['title'])),'submenu'=>array());
				}
				break;
			default:
				break;
		}
		return $return;
	}

	function getFolderInfo($fromMain = false) {
		if (array_key_exists('p_id',$_REQUEST)) {
			if ($data = $this->fetchSingle(sprintf('select * from %s where id = %d',$this->m_tree,$_REQUEST['p_id']))) {
				$form = new Forms();
				$data['notes'] = nl2br($data['notes']);
				$template = 'folderInfo';
				$frmFields = $this->getFields($template);
				$form->init($this->getTemplate($template), array());
				$frmFields = $form->buildForm($frmFields);
				$form->addData($data);
				if ($this->isAjax())
					return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
				elseif ($fromMain)
				return $form->show();
				else
					return $this->show($form->show());
			}
		}
	}

	function showPageContent($fromMain = false) {
		$p_id = array_key_exists('p_id',$_REQUEST) ? $_REQUEST['p_id'] : 0;
		$form = new Forms();
		if ($p_id > 0 && $data = $this->fetchSingle(sprintf('select * from %s where id = %d',$this->m_tree,$p_id))) {
			if (strlen($data['alternate_title']) > 0) $data['connector'] = '&nbsp;-&nbsp;';
			$form->init($this->getTemplate('showFolderContent'),array('name'=>'showFolderContent'));
			$frmFields = $this->getFields('showFolderContent');
			$frmFields = $form->buildForm($frmFields);
			if (array_key_exists('pagenum',$_REQUEST)) 
				$pageNum = $_REQUEST['pagenum'];
			else
				$pageNum = 1;	// no 0 based calcs
			if ($pageNum <= 0) $pageNum = 1;
			$perPage = $this->m_perrow;
			if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
			$count = $this->fetchScalar(sprintf('select count(n.id) from %s n where n.id in (select f.member_id from %s f where f.folder_id = %d)', $this->m_content, $this->m_junction, $_REQUEST['p_id']));
			$pagination = $this->pagination($count, $perPage, $pageNum, 
				array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
						'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'));
			$start = ($pageNum-1)*$perPage;
			$sortby = 'id';
			$sortorder = 'desc';
			if (count($_POST) > 0 && array_key_exists('showFolderContent',$_POST)) {
				$sortby = $_POST['sortby'];
				$sortorder = $_POST['sortorder'];
				$form->addData($_POST);
			} else {
				$sortby = 'sequence';
				$sortorder = 'asc';
			}
			$sql = sprintf('select a.*, f.id as j_id from %s a left join %s f on a.id = f.member_id where f.folder_id = %d order by %s %s limit %d,%d',  $this->m_content, $this->m_junction, $_REQUEST['p_id'],$sortby, $sortorder, $start,$perPage);
			$members = $this->fetchAll($sql);
			$this->logMessage('showPageContent', sprintf('sql [%s], records [%d]',$sql, count($members)), 2);
			$articles = array();
			foreach($members as $article) {
				$frm = new Forms();
				$tmp = $this->getFields('articleList');
				$frm->init($this->getTemplate('articleList'),array());
				$tmp = $frm->buildForm($tmp);
				$frm->addData($article);
				$articles[] = $frm->show();
			}
			$form->addTag('articles',implode('',$articles),false);
			$form->addTag('pagination',$pagination,false);
			$form->addData($data);
		}
		$form->addTag('heading',$this->getHeader(),false);
		if ($this->isAjax()) {
			$tmp = $form->show();
			return $this->ajaxReturn(array('status'=>'true','html'=>$tmp)); 
		}
		elseif ($fromMain)
		return $form->show();
		else
			return $this->show($form->show());
	}

	function showSearchForm($fromMain = false,$msg = '') {
		$form = new Forms();
		$form->init($this->getTemplate('showSearchForm'),array('name'=>'showSearchForm','persist'=>true));
		$frmFields = $form->buildForm($this->getFields('showSearchForm'));
		if (count($_POST) == 0)
			if (array_key_exists('formData',$_SESSION) && array_key_exists('membersSearchForm', $_SESSION['formData']))
				$_POST = $_SESSION['formData']['membersSearchForm'];
			else
				$_POST = array('showSearchForm'=>1,'deleted'=>0,'sortby'=>'created','sortorder'=>'desc');
		$this->logMessage(__FUNCTION__,sprintf("post is [%s]",print_r($_POST,true)),1);
		if (count($_POST) > 0 && array_key_exists('showSearchForm',$_POST)) {
			$form->addData($_POST);
			if ($form->validate()) {
				if (strlen($form->getData("quicksearch")) > 0) {
					$_SESSION['formData']['membersSearchForm'] = array('showSearchForm'=>1,'opt_quicksearch'=>'like','quicksearch'=>$form->getData("quicksearch"),'pager'=>$form->getData("pager"),'deleted'=>0);
				}
				else
					$_SESSION['formData']['membersSearchForm'] = $form->getAllData();
				$srch = array();
				foreach($frmFields as $key=>$value) {
					switch($key) {
						case 'quicksearch':
							if (array_key_exists('opt_quicksearch',$_POST) && $_POST['opt_quicksearch'] != '' && $value = $form->getData($key)) {
								//$srch = array();
								if ($_POST['opt_quicksearch'] == 'like' && strpos($value,'%',0) === false) {
									$value = '%'.$value.'%';
								}
								$srch = array(sprintf(' (firstname %s "%s" or lastname %s "%s" or concat(firstname," ",lastname) %s "%s" or email %s "%s") ',
									$_POST['opt_quicksearch'],$this->escape_string($value),
									$_POST['opt_quicksearch'],$this->escape_string($value),
									$_POST['opt_quicksearch'],$this->escape_string($value),
									$_POST['opt_quicksearch'],$this->escape_string($value)),'deleted = 0');
								$frmFields = array();
								continue 2;
							}
							break;
						case 'name':
							if (array_key_exists('opt_name',$_POST) && $_POST['opt_name'] != '' && $value = $form->getData($key)) {
								if ($_POST['opt_name'] == 'like' && strpos($value,'%',0) === false) {
									$value = '%'.$value.'%';
								}
								$srch[] = sprintf(' (firstname %s "%s" or lastname %s "%s") ',
									$_POST['opt_name'],$this->escape_string($value),$_POST['opt_name'],$this->escape_string($value));
							}
							break;
						case 'username':
						case 'email':
							if (array_key_exists('opt_'.$key,$_POST) && $_POST['opt_'.$key] != '' && $value = $form->getData($key)) {
								if ($_POST['opt_'.$key] == 'like' && strpos($value,'%',0) === false) {
									$value = '%'.$value.'%';
								}
								$srch[] = sprintf(' %s %s "%s" ', $key, $_POST['opt_'.$key],$this->escape_string($value));
							}
							break;
						case 'created':
						case 'expires':
							if (array_key_exists('opt_'.$key,$_POST) && $value = $form->getData($key)) {
								if ($_POST['opt_'.$key] == 'like') {
									$this->addError('Like is not supported for dates');
								}
								else
									$srch[] = sprintf(' %s %s "%s"',$key, $_POST['opt_'.$key],$this->escape_string($value));
							}
							break;
						case 'folder':
							if (($value = $form->getData($key)) > 0) {
								$srch[] = sprintf(' n.id in (select member_id from %s where folder_id = %d) ', $this->m_junction, $value);
							}
							break;
						case 'featured':
						case 'enabled':
						case 'deleted':
							if (!is_null($value = $form->getData($key)))
								if (strlen($value) > 0) {
									$srch[] = sprintf(' %s = %s',$key,$this->escape_string($value));
								}
							break;
						case 'nonmember':
							if ($form->getData($key) != 0)
								$srch[] = sprintf(' not exists (select 1 from %s where member_id = n.id) ',$this->m_junction);
							break;
						default:
							break;
					}
				}
				$this->logMessage("showSearchForm",sprintf("srch [%s]",print_r($srch,true)),2);
				if (count($srch) > 0) {
					if (array_key_exists('pagenum',$_POST))
						$pageNum = $_POST['pagenum'];
					else
						$pageNum = 1;	// no 0 based calcs
					$perPage = $this->m_perrow;
					if (array_key_exists('pager',$_POST)) $perPage = $_POST['pager'];
					$count = $this->fetchScalar(sprintf('select count(n.id) from %s n where 1=1 and %s', $this->m_content, implode(' and ',$srch)));
					$pageNum = max(1,min($pageNum, (floor(($count-1)/$perPage)+1)));
					$form->setData('pagenum', $pageNum);
					$pagination = $this->pagination($count, $perPage, $pageNum,
							array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
									'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'));
					$start = ($pageNum-1)*$perPage;
					$sort = 'created desc';
					if (array_key_exists('sortby',$_POST)) {
						$sortby = $_POST['sortby'];
						$sortorder = $_POST['sortorder'];
						if ($sortby == 'name') {
							$sort = sprintf('lastname %s, firstname %s',$sortorder,$sortorder);
						}
						else
							$sort = $sortby.' '.$sortorder;
					}
					//$sql = sprintf('select n.*, j.id as j_id from %s n, %s j where n.id = j.member_id and j.id = (select min(j1.id) from %s j1 where j1.member_id = n.id) and %s order by %s limit %d,%d',
					//	 $this->m_content, $this->m_junction, $this->m_junction, implode(' and ',$srch),$sort, $start,$perPage);
					$sql = sprintf('select n.*, 0 as j_id from %s n where %s order by %s limit %d,%d',
						 $this->m_content, implode(' and ',$srch),$sort, $start,$perPage);
					$recs = $this->fetchAll($sql);
					$this->logMessage('showSearchForm', sprintf('sql [%s] records [%d]',$sql,count($recs)), 2);
					$articles = array();
					foreach($recs as $article) {
						$frm = new Forms();
						$frm->init($this->getTemplate('articleList'),array());
						$tmp = $frm->buildForm($this->getFields('articleList'));
						$frm->addData($article);
						$articles[] = $frm->show();
					}
					$form->addTag('articles',implode('',$articles),false);
					$form->addTag('pagination',$pagination,false);
					$form->addTag('statusMessage',sprintf('We found %d record%s matching the criteria',$count,$count > 1 ? 's' : ''));
				}
			}
		}
		$form->addTag('heading',$this->getHeader(),false);
		if (strlen($msg) > 0) $form->addTag('statusMessage',$msg,false);
		if ($this->isAjax()) {
			$tmp = $form->show();
			return $this->ajaxReturn(array('status'=>'true','html'=>$tmp));
		}
		elseif ($fromMain)
			return $form->show();
		else
			return $this->show($form->show());
	}

	function showSearchResults($fromMain = false) {
		$form = new Forms();
		$form->init($this->getTemplate('showSearchResults'),array('name'=>'showSearchResults','persist'=>true));
		$frmFields = $form->buildForm($this->getFields('showSearchResults'));
		if ($this->isAjax()) {
			$tmp = $form->show();
			return $this->ajaxReturn(array('status'=>'true','html'=>$tmp));
		}
		elseif ($fromMain)
			return $form->show();
		else
			return $this->show($form->show());
		
	}

	function addContent($fromMain = false) {
		$form = new Forms();
		$form->init($this->getTemplate('addContent'),array('name'=>'addContent'));
		$frmFields = $this->getFields('addContent');
		if (!(array_key_exists('m_id',$_REQUEST) && $_REQUEST['m_id'] > 0 && $data = $this->fetchSingle(sprintf('select * from %s where id = %d', $this->m_content, $_REQUEST['m_id'])))) {
			$data = array('id'=>0,'published'=>false,'image1'=>'','image2'=>''); 
		}
		$data['destFolders'] = array();
		if (count($_REQUEST) > 0 && array_key_exists('destFolders',$_REQUEST) || $data['id'] > 0) {
			$ids = array();
			if (array_key_exists('destFolders',$_REQUEST)) {
				$ids = $_REQUEST['destFolders'];
				if (!is_array($ids)) $ids = array($ids);
			}
			if ($data['id'] > 0) {
				$tmp = $this->fetchScalarAll(sprintf('select folder_id from %s where member_id = %d', $this->m_junction, $data['id']));
				$ids = array_merge($ids,$tmp);
			}
			if (count($ids) > 0) {
				$data['destFolders'] = $ids;
			}
		}
		$profiles = array();
		foreach($data['destFolders'] as $key=>$value) {
			$profiles[] = $this->getProfile($data['id'],$value);
		}
		$data['profiles'] = implode("",$profiles);
		$data['imagesel_a'] = $data['image1'];
		$data['imagesel_b'] = $data['image2'];
		$form->addData($data);
		$form->addTag('addressForm',$this->loadAddresses($data['id']),false);
		$status = 'false';	//assume it failed
		$customFields = new custom();
		if (method_exists($customFields,'memberDisplay')) {
			$custom = $customFields->memberDisplay($data);
			$form->addTag('customTab',sprintf('<li><a href="#tabs-custom">%s</a></li>',$custom['description']),false);
			$html = $form->getHTML();
			$html = str_replace('%%customInfo%%',$custom['form'],$html);
			$form->setHTML($html);
			$frmFields = array_merge($frmFields,$custom['fields']);
		}
		$frmFields = $form->buildForm($frmFields);
		if (count($_POST) > 0 && array_key_exists('addContent',$_POST)) {
			$_POST['imagesel_a'] = array_key_exists('image1',$_POST) ? $_POST['image1'] : '';
			$_POST['imagesel_b'] = array_key_exists('image1',$_POST) ? $_POST['image2'] : '';
			$form->addData($_POST);
			$valid = $form->validate();
			if ($valid) {
				if (array_key_exists('username',$_POST) && $_POST['username'] != '') {
					//
					//	make sure noone else has the username
					//
					$ct = $this->fetchScalar(sprintf('select count(0) from %s where username = "%s" and id != %d',$this->m_content, $_POST['username'], $data['id']));
					if ($ct > 0) {
						$this->addError('Username is already used');
						$valid = false;
					}
				}
			}
			if ($valid) {
				$id = $_POST['m_id'];
				//
				//	check to see if the password has been changed - if not unset it, otherwise encode it
				//
				$pwd = $this->fetchScalar(sprintf('select password from members where id = %d',$id));
				if ($form->getData('password') == $pwd) {
					$this->logMessage('addContent','password was not changed, unsetting it',3);
					unset($frmFields['password']);
				}
				else {
					$this->logMessage('addContent','encrypting password',3);
					$form->setData('password',SHA1($form->getData('password')));
				}
				unset($frmFields['m_id']);
				unset($frmFields['options']);
				foreach($frmFields as $key=>$fld) {
					if (!(array_key_exists('database',$fld) && $fld['database'] == false)) {
						$values[] = $form->getData($fld['name']);	//$_REQUEST[$fld['name']];
						if ($data['id'] > 0)
							$flds[sprintf('%s = ?',$fld['name'])] = $form->getData($fld['name']);//$_REQUEST[$fld['name']];
						else
							$flds[$fld['name']] = $form->getData($fld['name']);//$_REQUEST[$fld['name']];
					}
				}
				$adding = $id == 0;
				if ($id == 0) {
					$flds['created'] = date('c');
					$stmt = $this->prepare(sprintf('insert into %s(%s) values(%s)', $this->m_content, implode(',',array_keys($flds)), str_repeat('?,', count($flds)-1).'?'));
					$stmt->bindParams(array_merge(array(str_repeat('s', count($flds))),array_values($flds)));
				}
				else {
					$stmt = $this->prepare(sprintf('update %s set %s where id = %d', $this->m_content, implode(',',array_keys($flds)),$data['id']));
					$stmt->bindParams(array_merge(array(str_repeat('s', count($flds))),array_values($flds)));
				}
				$this->beginTransaction();
				if ($stmt->execute()) {
					if ($id == 0) {
						$id = $this->insertId();
						$flds["id"] = $id;
					}
					else
						$flds["id"] = $data["id"];
					$base_dir = sprintf('../images/members/%d',$id);
					if (!file_exists($base_dir)) mkdir($base_dir);
					if (array_key_exists('destFolders',$_POST))
						$destFolders = $_POST['destFolders'];
					else $destFolders = "0";
					if (!is_array($destFolders)) $destFolders = array($destFolders);
					//
					//	delete folders we are no longer a member of
					//
					$this->execute(sprintf('delete from %s where member_id = %d and folder_id not in (%s)', $this->m_junction, $id,implode(',',$destFolders)));
					$new = $this->fetchScalarAll(sprintf('select id from %s where id in (%s) and id not in (select folder_id from %s where member_id = %d and folder_id in (%s))',
						$this->m_tree,implode(',',$destFolders),$this->m_junction,$id,implode(',',$destFolders)));
					$status = true;
					foreach($new as $key=>$folder) {
						$obj = new preparedStatement(sprintf('insert into %s(member_id,folder_id) values(?,?)',$this->m_junction));
						$obj->bindParams(array('dd',$id,$folder));
						$status = $status && $obj->execute();
						$sub_dir = sprintf('%s/%d',$base_dir,$folder);
						if (!file_exists($sub_dir)) mkdir($sub_dir);
					}
					if ($status) {
						$this->commitTransaction();
						if (method_exists($customFields,'memberUpdate')) {
							$customFields->memberUpdate($flds,$_REQUEST);
						}
						if ($adding) {
							//
							//	let the address editing kick in now
							//
							$this->addMessage('Member Added');
							$form->setData('id',$id);
							$profiles = array();
							foreach($data['destFolders'] as $key=>$value) {
								$profiles[] = $this->getProfile($id,$value);
							}
							$form->addTag('profiles',implode("",$profiles),false);
							return $this->ajaxReturn(array(
								'status' => 'true',
								'html' => $form->show()
							));
						}
						else {
							$form->init($this->getTemplate('addContentResult'));
							//return $this->ajaxReturn(array('status' => 'true','url' => sprintf('/modit/members?p_id=%d',$destFolders[0])));
							return $this->ajaxReturn(array('status' => 'true','html' => $form->show()));
						}
					}
					else {
						$this->rollbackTransaction();
						$this->addError('Error creating the record');
					}
				} else {
					$this->rollbackTransaction();
					$this->addError('Error creating the record');
				}
			}
			else
				$this->addError('form validation failed');
			$form->addTag('errorMessage',$this->showMessages(),false);
		}
		if ($this->isAjax()) {
			$tmp = $form->show();
			return $this->ajaxReturn(array(
				'status' => $status,
				'html' => $tmp
			));
		}
		elseif ($fromMain)
			return $form->show();
		else
			return $this->show($form->show());
	}

	function moveArticle() {
		$src = 0;
		$dest = 0;
		$status = false;
		if (array_key_exists('src',$_REQUEST)) $src = $_REQUEST['src'];
		if (array_key_exists('dest',$_REQUEST)) $dest = $_REQUEST['dest'];
		if ($_REQUEST['type'] == 'tree') {
			if ($src == 0 || $dest == 0 || !array_key_exists('type',$_REQUEST)) {
				$this->addError('Either source or destination was missing');
				return $this->ajaxReturn(array('status' => 'false'));
			}
			if ($folder = $this->fetchSingle(sprintf('select * from %s where id = %d',$this->m_tree,$dest))) {
				$curr = $this->fetchScalar(sprintf('select member_id from %s where id = %d',$this->m_junction,$src));
				$status = true;
				if (array_key_exists('move',$_REQUEST)) {
					//
					//	move it - delete all other folders
					//
					$this->logMessage('moveArticle', sprintf('moving store %d to folder %d',$src,$dest),2);
					$this->beginTransaction();
					if ($status = $this->execute(sprintf('delete from %s where id = %d', $this->m_junction, $src))) {
						if (!$this->fetchSingle(sprintf('select * from %s where member_id = %d and folder_id = %d',$this->m_junction,$curr,$dest))) {
							$obj = new preparedStatement(sprintf('insert into %s(member_id,folder_id) values(?,?)',$this->m_junction));
							$obj->bindParams(array('dd',$curr,$dest));
							$status = $obj->execute();
						}
					}
					if ($status)
						$this->commitTransaction();
					else
						$this->rollbackTransaction();
				}
				else {
					//
					//	add it - if it doesn't already exist
					//
					$curr = $src;
					$this->logMessage('moveArticle', sprintf('cloning member %d to folder %d',$curr,$dest),2);
					if (!($this->fetchSingle(sprintf('select * from %s where member_id = %d and folder_id = %d',$this->m_junction,$curr,$dest)))) {
						$obj = new preparedStatement(sprintf('insert into %s(member_id,folder_id) values(?,?)',$this->m_junction));
						$obj->bindParams(array('dd',$curr,$dest));
						$status = $obj->execute();
					}
				}
			} else {
				$status = false;
				$this->addError('Could not locate the destination folder');
			}
		}
		else {
			if ($src == 0 || $dest < 0) {
				$this->addMessage('Either source or destination was missing');
				return $this->ajaxReturn(array('status' => 'false'));
			}
			$src = $this->fetchSingle(sprintf('select * from %s where id = %d',$this->m_junction,$src));
			$sql = sprintf('select * from %s where folder_id = %d order by sequence limit %d,1',$this->m_junction,$src['folder_id'],$dest);
			$dest = $this->fetchSingle($sql);
			$this->logMessage("moveArticle",sprintf("move src [%s] to dest [%s] sql [%s]",print_r($src,true),print_r($dest,true),$sql),2);
			if (count($src) == 0 || count($dest) == 0) {
				$status = false;
				$this->addMessage('Either the source or destination article was not found');
			}
			else {
				//
				//	swap the order of the images
				//
				$this->logMessage('moveArticle', sprintf('swap the sort order of %d and %d',$src['id'],$dest['id']),2);
				$this->beginTransaction();
				$sql = sprintf('update %s set sequence = %d where id = %s',
					$this->m_junction, $src['sequence'] < $dest['sequence'] ? $dest['sequence']+1 : $dest['sequence']-1, $src['id']);
				if ($this->execute($sql)) {
					$this->resequence($src['folder_id']);
					$this->commitTransaction();
					$status = true;
				}
				else {
					$this->rollbackTransaction();
					$status = false;
				}					
			}
		}
		return $this->ajaxReturn(array(
				'status'=>$status?'true':'false'
		));
	}

	function resequence($folder) {
		$this->logMessage('resequence', "resequencing folder $folder", 2);
		$articles = $this->fetchAll(sprintf('select * from %s where folder_id = %d order by sequence',$this->m_junction,$folder));
		$seq = 10;
		foreach($articles as $article) {
			$this->execute(sprintf('update %s set sequence = %d where id = %d',$this->m_junction,$seq,$article['id']));
			$seq += 10;
		}
	}

	function deleteArticle() {
		$form = new Forms();
		$form->init($this->getTemplate('deleteItem'));
		$flds = $form->buildForm($this->getFields('deleteItem'));
		if (count($_REQUEST) > 0 && $_REQUEST['j_id'] == 0)
			$form->getField('one')->addAttribute('disabled','disabled');
		$form->addData($_REQUEST);
		if (count($_REQUEST) > 0 && array_key_exists('deleteItem',$_REQUEST)) {
			if ($form->validate()) {
				$type = $form->getData('action');
				switch($type) {
					case 'cancel':
						return $this->ajaxReturn(array('status'=>'true','code'=>'closePopup();'));
						break;
					case 'all':
						//$img = $this->fetchScalar(sprintf('select member_id from %s where id = %d',$this->m_junction,$_REQUEST['j_id']));
						$this->execute(sprintf('delete from %s where member_id = %d',$this->m_junction,$_REQUEST['m_id']));
						$this->execute(sprintf('update %s set deleted = 1 where id = %d',$this->m_content,$_REQUEST['m_id']));
						break;
					case 'one':
						$status = $this->execute(sprintf('delete from %s where id = %d',$this->m_junction,$_REQUEST['j_id']));
						$ct = $this->fetchScalar(sprintf('select count(0) from %s where member_id = %d',$this->m_junction,$_REQUEST['m_id']));
						if ($ct == 0)
							$this->execute(sprintf('update %s set deleted = 1 where id = %d',$this->m_content,$_REQUEST['m_id']));
						break;
					default:
						break;
				}
				$form->init($this->getTemplate('deleteItemResult'));
			}
		}
		return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
	}

	function editAddress() {
		if (!array_key_exists('a_id',$_REQUEST))
			return $this->ajaxReturn(array('status'=>'false','html'=>'No id passed'));
		$a_id = $_REQUEST['a_id'];
		$o_id = $_REQUEST['o_id'];
		$status = true;
		if (!($data = $this->fetchSingle(sprintf('select a.* from addresses a where a.id = %d and a.ownertype = "member" and a.ownerid = %d',$a_id,$o_id)))) {
			$data = array('id'=>0,'ownertype'=>'member','ownerid'=>$o_id);
			$addresses = array();
		}
		else 
			$addresses = $this->fetchAll(sprintf('select * from addresses where ownertype = "member" and ownerid = %d and deleted = 0',$o_id));
		$form = new Forms();
		$form->init($this->getTemplate('editAddress'),array('name'=>'editAddress'));
		$frmFields = $this->getFields('editAddress');
		if (count($addresses) > 0) {
			$frmFields['delete'] = array('type'=>'button','class'=>'def_field_submit','value'=>'Delete Address','database'=>false,'onclick'=>sprintf('deleteAddress(%d,%d);return false;',$a_id,$o_id));
		}
		$frmFields = $form->buildForm($frmFields);
		$form->addData($data);
		if (count($_POST) > 0 && array_key_exists('editAddress',$_POST)) {
			$form->addData($_POST);
			if ($form->validate()) {
				unset($frmFields['options']);
				$form->setData('tax_address',$this->fetchScalar(sprintf('select extra from code_lookups where id = %d',$_POST['addresstype'])));
				if ($form->getData("geocode") == 1) {
					$lat = 0;
					$lng = 0;
					$status = $this->geocode($form->getAllData(),$lat,$lng);
					if ($status) {
						$form->setData("latitude",$lat);
						$form->setData("longitude",$lng);
					}
				}
				foreach($frmFields as $key=>$fld) {
					if (!(array_key_exists('database',$fld) && $fld['database'] == false)) {
						$flds[$fld['name']] = $form->getData($fld['name']);//$_REQUEST[$fld['name']];
					}
				}
				if ($data['id'] == 0) {
					$stmt = $this->prepare(sprintf('insert into addresses(%s) values(%s)', implode(',',array_keys($flds)), str_repeat('?,', count($flds)-1).'?'));
				}
				else {
					$stmt = $this->prepare(sprintf('update addresses set %s=? where id = %d', implode('=?,',array_keys($flds)),$data['id']));
				}
				$stmt->bindParams(array_merge(array(str_repeat('s', count($flds))),array_values($flds)));
				$this->beginTransaction();
				if ($stmt->execute()) {
					if ($data["id"] == 0) {
						$data["id"] = $this->insertId();
						$this->logMessage(__FUNCTION__,sprintf("set id to [%s]",$data["id"]),1);
					}
					$this->commitTransaction();
					$form->init($this->getTemplate(__FUNCTION__."Success"));
				}
				else {
					$this->rollbackTransaction();
					$this->addError('An error occurred updating the database');
				}
			}
			else {
				$this->addError('Form validation Failed');
				$status = false;
			}
		}
		$addresses = $this->fetchAll(sprintf('select * from addresses where ownertype = "member" and ownerid = %d and deleted = 0',$o_id));
		return $this->ajaxReturn(array('status'=>$status,'html'=>$form->show()));
	}

	function deleteAddress() {
		if (array_key_exists('a_id',$_REQUEST) && array_key_exists('o_id',$_REQUEST)) {
			$this->logMessage('deleteAddress',sprintf('deleting id [%d] owner [%d]',$_REQUEST['a_id'],$_REQUEST['o_id']),1);
			if ($data = $this->fetchSingle(sprintf('select * from addresses where ownertype = "%s" and id = %d and ownerid = %d',$_REQUEST['type'],$_REQUEST['a_id'],$_REQUEST['o_id']))) {
				$this->execute(sprintf('update addresses set deleted = 1 where id = %d',$_REQUEST['a_id']));
				return $this->ajaxReturn(array('status'=>'true'));
			}
		}
	}

	function getProfile($member_id,$folder_id) {
		$this->logMessage("getProfile",sprintf("($member_id,$folder_id)"),1);
		if ($rec = $this->fetchSingle(sprintf('select p.*, t.title from members_by_folder p, members_folders t where p.member_id = %d and p.folder_id = %d and t.id = p.folder_id',$member_id,$folder_id))) {
			$form = new Forms();
			$form->init($this->getTemplate('getProfile'));
			$flds = $form->buildForm($this->getFields('getProfile'));
			$form->addData($rec);
			return $form->show();
		}
	}

	function editProfile() {
		if (array_key_exists('p_id',$_REQUEST) && $data = $this->fetchSingle(sprintf('select * from members_by_folder where id = %d',$_REQUEST['p_id']))) {
			$form = new Forms();
			$form->init($this->getTemplate('editProfile'));
			$formFlds = $this->getFields('editProfile');

			$customFields = new custom();
			if (method_exists($customFields,'memberGroupDisplay')) {
				$custom = $customFields->memberGroupDisplay();
				$form->addTag('customTab',sprintf('<li><a href="#tabs-custom">%s</a></li>',$custom['description']),false);
				$html = $form->getHTML();
				$html = str_replace('%%customInfo%%',$custom['form'],$html);
				$form->setHTML($html);
				$formFlds = array_merge($formFlds,$custom['fields']);
			}

			$formFlds = $form->buildForm($formFlds);
			$data['imagesel_c'] = $data['image1'];
			$data['imagesel_d'] = $data['image2'];
			$form->addData($data);
			if (count($_POST) > 0 && array_key_exists('editProfile',$_POST)) {
				$form->addData($_POST);
				if ($form->validate()) {
					$this->logMessage("editProfile",sprintf("validated"),2);
					$flds = array();
					foreach($formFlds as $key=>$fld) {
						if (!(array_key_exists('database',$fld) && $fld['database'] == false)) {
							if ($key != 'options')
								$flds[$key] = $form->getData($fld['name']);
						}
					}
					$stmt = $this->prepare(sprintf('update %s set %s where id = %d',$this->m_junction, implode('=?, ',array_keys($flds))."=?",$data['id']));
					$stmt->bindParams(array_merge(array(str_repeat('s', count($flds))),array_values($flds)));
					$this->beginTransaction();
					if (!$stmt->execute()) {
						$this->addError('Update Failed');
						$this->rollbackTransaction();
						$status = 'false';
					}
					else {
						$this->commitTransaction();
						$this->addMessage('Record Updated');
						$status = 'true';
					}
					$form->init($this->getTemplate('editProfileResult'));
					return $this->ajaxReturn(array('status'=>$status,'html'=>$form->show()));
				}
				else {
					$form->addError('Form Validation Failed');
					$this->logMessage("editProfile",sprintf("failed validation"),2);
				}

			}
			return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
		}
	}
	
	function loadAddresses($passed_id = null,$byAjax = true) {
		if ($passed_id == null) {
			if (array_key_exists('o_id',$_REQUEST))
				$o_id = $_REQUEST['o_id'];
			else
				$o_id = $_REQUEST['m_id'];
		}
		else
			$o_id = $passed_id;
		$addresses = $this->fetchAll(sprintf('select a.*, c.value as addressType from addresses a, code_lookups c where ownertype = "member" and ownerid = %d and deleted = 0 and c.id = a.addressType',$o_id));
		$addressForm = new Forms();
		$addressForm->init($this->getTemplate('addressForm'));
		$addrForm = new Forms();
		$addrForm->init($this->getTemplate('addressList'));
		$addrFields = $addrForm->buildForm($this->getFields('addressList'));
		$addressList = array();
		foreach($addresses as $rec) {
			$addrForm->addData($rec);
			$addressList[] = $addrForm->show();
		}
		$addressForm->addTag('addresses',implode('',$addressList),false);
		if (!is_null($passed_id)) {
			$this->logMessage("loadAddresses",sprintf("returning normal show pass_id [%s] byAjax [%s]",$passed_id,$byAjax),3);
			return $addressForm->show();
		}
		else {
			$this->logMessage("loadAddresses",sprintf("returning ajax result show pass_id [%s] isAjax [%s]",$passed_id,$this->isAjax()),3);
			return $this->ajaxReturn(array('status'=>'true','html'=>$addressForm->show()));
		}
	}

	function moduleStatus($fromMain = 0) {
		if (array_key_exists('formData',$_SESSION) && array_key_exists('membersSearchForm', $_SESSION['formData'])) {
			$_POST = $_SESSION['formData']['membersSearchForm'];
			$msg = '';
		}
		else {
			$ct = $this->fetchScalar(sprintf('select count(0) from %s where deleted = 0 and enabled = 0',$this->m_content));
			if ($ct == 0) {
				$_POST = array('showSearchForm'=>1,'deleted'=>0,'sortby'=>'created','sortorder'=>'desc','pager'=>$this->m_perrow);
				$msg = "Showing latest members added";
			}
			else {
				$_POST = array('showSearchForm'=>1,'enabled'=>0,'sortby'=>'created','sortorder'=>'desc','pager'=>$this->m_perrow);
				$msg = "Showing disabled members";
			}
		}
		$result = $this->showSearchForm($fromMain,$msg);
		return $result;
	}

	function getHeader() {
		$form = new Forms();
		$form->init($this->getTemplate('header'));
		$flds = $form->buildForm($this->getFields('showSearchForm'));
		if (count($_POST) > 0 && array_key_exists('showSearchForm',$_POST))
			$form->addData($_POST);
		return $form->show();
	}

	function addFolder($fromMain = false) {
		$form = new Forms();
		$form->init($this->getTemplate('addFolder'));
		if ($fromMain)
			return $form->show();
		else
			return $this->show($form->show());
	}

	function addItem($fromMain = false) {
		$form = new Forms();
		$form->init($this->getTemplate('addItem'));
		if ($fromMain)
			return $form->show();
		else
			return $this->show($form->show());
	}

	function showOrders() {
		if (array_key_exists('o_id',$_REQUEST))
			$data = $this->fetchSingle(sprintf('select * from members where id = %d',$_REQUEST['o_id']));
		else $data = array('id'=>0);
		$form = new Forms();
		$form->init($this->getTemplate('showOrders'));
		$formFlds = $form->buildForm($this->getFields('showOrders'));
		$form->addData($_REQUEST);
		$where = sprintf('select * from orders where member_id = %d',$data['id']);
		$and[] = "1=1";
		if (count($_POST) > 0 && array_key_exists("showOrders",$_POST)) {
			$status = 0;
			if (array_key_exists("order_status",$_POST)) {
				foreach($_POST["order_status"] as $key=>$value) {
					$status |= $value;
				}
			}
			if ($status > 0)
				$and[] = sprintf("order_status & %d = %d",$status,$status);
		}
		$count = $this->fetchScalar(sprintf("select count(0) from orders where member_id = %d and %s",$data['id'], implode(" and ",$and)));
		$perPage = $_SESSION["formData"]["membersSearchForm"]["pager"];
		$pageNum = array_key_exists('pagenum',$_POST) ? $_POST['pagenum'] : 1;
		$pagination = $this->pagination($count, $perPage, $pageNum, 
				array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
						'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html','url'=>'"/modit/ajax/showOrders/members"','dest'=>'altPopup'));
		$start = ($pageNum-1)*$perPage;
		$orders = $this->fetchAll(sprintf("%s and %s order by id desc limit %d,%d",$where,implode(" and ",$and),$start,$perPage));
		$form->addTag("pagination",$pagination,false);
		$return = array();
		$inner = new Forms();
		$inner->init($this->getTemplate('memberOrder'));
		$innerFlds = $inner->buildForm($this->getFields('memberOrder'));
		$orderObj = new orders();
		foreach($orders as $order) {
			$inner->addData($orderObj->formatOrder($order));
			$return[] = $inner->show();
		}
		$form->addTag('orders',implode('',$return),false);
		$form->addData($data);
		return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
	}

	function loadMedia() {
		$form = new Forms();
		$form->init($this->getTemplate('loadMedia'),array('name'=>'loadMedia'));
		$frmFields = $form->buildForm($this->getFields('loadMedia'));
		$f_id = array_key_exists('f_id',$_REQUEST) ? $_REQUEST['f_id'] : 0;
		$m_id = array_key_exists('m_id',$_REQUEST) ? $_REQUEST['m_id'] : 0;
		$form->setData('member_id',$m_id);
		$form->setData('folder_id',$f_id);
		$media = $this->fetchAll(sprintf('select * from %s where folder_id = %d and member_id = %d',$this->m_media,$f_id,$m_id));
		$inner = new Forms();
		$inner->init($this->getTemplate('listMedia'),array('name'=>'listMedia'));
		$iFields = $this->getFields('listMedia');
		$return = array();
		foreach($media as $key=>$value) {
			$inner->addData($value);
			$return[] = $inner->show();
		}
		$form->addTag('media',implode('',$return),false);
		return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
	}

	function editMedia() {
		$form = new Forms();
		$form->init($this->getTemplate('editMedia'),array('name'=>'editMedia'));
		$frmFields = $this->getFields('editMedia');
		if (array_key_exists('m_id',$_REQUEST)) {
			$form->setData('member_id',$_REQUEST['m_id']);
		}
		if (array_key_exists('f_id',$_REQUEST)) {
			$form->setData('folder_id',$_REQUEST['f_id']);
		}
		if (!(array_key_exists('p_id',$_REQUEST) && $data = $this->fetchSingle(sprintf('select * from %s where id = %d',$this->m_media,$_REQUEST['p_id'])))) {
			$data = array('id'=>0);
		}
		if ($data['id'] != 0) {
			$frmFields['filename'] = array('type'=>'tag','required'=>false,'database'=>false,'reformatting'=>false);
			$tmp = new Forms();
			$tmp->init($this->M_DIR.'forms/play'.$data['type'].'.html');
			$tmp->addData($data);
			$data['filename'] = $tmp->show();
			$this->logMessage('editMedia',sprintf('reset filename'),1);
		}
		else $this->logMessage('editMedia',sprintf('must be an add [%s]',print_r($data,true)),1);
		$this->logMessage('editMedia',sprintf('frmFields [%s]',print_r($frmFields,true)),1);
		$frmFields = $form->buildForm($frmFields);
		$form->addData($data);
		if (array_key_exists('editMedia',$_REQUEST)) {
			$form->addData($_REQUEST);
			$valid = $form->validate();
			$this->logMessage('editMedia',sprintf('validate returned [%s]',$valid),1);
			$return = array();
			if ($valid && $data['id'] == 0) {
				if (count($_FILES) == 0) {
					$valid = false;
					$this->addFormError('No file was attached');
				}
				else {
					if (!($valid = $this->processUploadedFiles(array('Image','Video','Audio'),$return,$messages))) {
						foreach($messages as $key=>$value)
							$form->addFormError($value);
					}
				}
			}
			if ($valid) {
				foreach($frmFields as $key=>$fld) {
					if (!(array_key_exists('database',$fld) && $fld['database'] == false)) {
						$values[] = $form->getData($fld['name']);
						if ($data['id'] > 0)
							$flds[sprintf('%s = ?',$fld['name'])] = $form->getData($fld['name']);
						else
							$flds[$fld['name']] = $form->getData($fld['name']);
					}
				}
				if ($data['id'] == 0) {
					$flds['filename'] = $return['filename']['name'];
					$flds['type'] = $return['filename']['type'];
				}
				$adding = $data['id'] == 0;
				if ($adding) {
					$stmt = $this->prepare(sprintf('insert into %s(%s) values(%s)', $this->m_media, implode(',',array_keys($flds)), str_repeat('?,', count($flds)-1).'?'));
					$stmt->bindParams(array_merge(array(str_repeat('s', count($flds))),array_values($flds)));
				}
				else {
					$stmt = $this->prepare(sprintf('update %s set %s where id = %d', $this->m_media, implode(',',array_keys($flds)),$data['id']));
					$stmt->bindParams(array_merge(array(str_repeat('s', count($flds))),array_values($flds)));
				}
				$this->beginTransaction();
				if ($stmt->execute()) {
					$form->addFormError('data saved');
					$this->commitTransaction();
					$form->init($this->getTemplate('editMediaSuccess'));
				}
				else
					$form->addFormError('An Error occurred');
					$this->rollbackTransaction();
			}
		}
		return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
	}
	
	function deleteMedia() {
		$form = new Forms();
		$form->init($this->getTemplate('deleteMedia'));
		$form->addData($_REQUEST);
		if (array_key_exists('p_id',$_REQUEST)) {
			$file = $this->fetchScalar(sprintf('select filename from %s where id = %d and folder_id = %d and member_id = %d',$this->m_media,$_REQUEST['p_id'],$_REQUEST['f_id'],$_REQUEST['m_id']));
			$status = $this->execute(sprintf('delete from %s where id = %d and folder_id = %d and member_id = %d',$this->m_media,$_REQUEST['p_id'],$_REQUEST['f_id'],$_REQUEST['m_id']));
			unlink('..'.$file);
		}
		else $status = false;
		return $this->ajaxReturn(array('status'=>$status,'html'=>$form->show()));
	}

	function deleteContent() {
		if (array_key_exists('p_id',$_REQUEST)) {
			$ct = $this->fetchScalar(sprintf('select count(0) from %s where folder_id = %d',$this->m_junction,$_REQUEST['p_id']));
			if ($ct > 0) {
				$this->addError('Events are still attached to this folder');
				return $this->ajaxReturn(array('status'=>'false'));
			}
			$ct = $this->fetchScalar(sprintf('select count(0) from %s t1, %s t2 where t2.id = %d and t1.left_id > t2.left_id and t1.right_id < t2.right_id and t1.level > t2.level',$this->m_tree, $this->m_tree, $_REQUEST['p_id']));
			if ($ct > 0) {
				$this->addError('Other categories are still attached to this folder');
				return $this->ajaxReturn(array('status'=>'false'));
			}
			$ct = $this->fetchScalar(sprintf('select count(0) from members_by_folder where folder_id = %d', $_REQUEST['p_id']));
			if ($ct > 0) {
				$this->addError('Site Members are still attached to this folder');
				return $this->ajaxReturn(array('status'=>'false'));
			}
			if ($ct > 0) {
				$this->addError('Other categories are still attached to this folder');
				return $this->ajaxReturn(array('status'=>'false'));
			}

			if (!$this->deleteCheck('calendar',$_REQUEST['p_id'],$inUse)) {
				$this->addError('Some Pages or Templates still use this folder');
				foreach($inUse as $key=>$value) {
					$this->addError($value);
				}
				return $this->ajaxReturn(array('status'=>'false'));
			}
			$mptt = new mptt($this->m_tree);
			$mptt->delete($_REQUEST['p_id']);
			return $this->ajaxReturn(array('status'=>'true'));
		}
	}

}

?>