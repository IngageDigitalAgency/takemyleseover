<?php

function customRateSort( $r1, $r2 ) {
	if ($r1['net'] == $r2['net'])
		return 0;
	return ($r1['net'] < $r2['net']) ? -1 : 1;
}

class custom extends Frontend {

	private $m_dir = '';
	protected $module;
	public function __construct($id,$module = array()) {
		parent::__construct();
		$this->m_dir = ADMIN.'frontend/forms/custom/';
		$this->m_moduleId = $id;
		$this->m_module = $module;
		$this->m_fields = array(
			// special cases for internal use only [duped in Frontend and used for backend config]
			'getFileList'=>array(	
				'options'=>array('private'=>true),
				'files'=>array('type'=>'select','name'=>'file_list')
			),
			'getModuleInfo'=>array(
				'options'=>array('private'=>true),
				'functions'=>array('type'=>'select','name'=>'module_function')
			),
			'siteSearch'=>array(
				'siteSearchForm'=>array('type'=>'hidden','value'=>1),
				'siteSearchText'=>array('type'=>'textfield','placeholder'=>'What Inspires You?','class'=>'form-control has-feedback'),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'btn'=>array('type'=>'submitbutton','value'=>'SEARCH','class'=>'btn form-control')
			),
			'manufacturerSearch'=>array(
				"manufacturerSearch"=>array("type"=>"hidden","value"=>1,"name"=>"manufacturerSearch"),
				"manufacturerList"=>array("type"=>"hidden","value"=>1,"name"=>"manufacturerList"),
				"pagenum"=>array("type"=>"tag","value"=>0)
			),
			'manufacturerSearchRow'=>array(
				"manufacturer"=>array("type"=>"checkbox","name"=>"manufacturer[]","value"=>"%%id%%")
			),
			'addToCart'=>array(
				'quantity'=>array('type'=>'number','name'=>'product_quantity','value'=>1,'required'=>true,'validation'=>'number','class'=>"cart-plus-minus-box"),
				'product_id'=>array('type'=>'hidden','value'=>'%%id%%'),
				'customAddToCart'=>array('type'=>'hidden','value'=>1)
			),
			'addToCartDetail'=>array(
				'quantity'=>array('type'=>'number','name'=>'product_quantity','value'=>1,'required'=>true,'validation'=>'number','class'=>"form-control text-right cart-plus-minus-box"),
				'product_id'=>array('type'=>'hidden','value'=>'%%id%%'),
				'customAddToCart'=>array('type'=>'hidden','value'=>1)
			),
			'addToCart_x1'=>array(
				'quantity'=>array('type'=>'hidden','name'=>'product_quantity','value'=>1,'required'=>true,'validation'=>'number'),
				'product_id'=>array('type'=>'hidden','value'=>'%%id%%'),
				'customAddToCart'=>array('type'=>'hidden','value'=>1)
			),
			'updateCart'=>array(
				'quantity'=>array('type'=>'number','name'=>'quantity[%%key%%]','value'=>'%%quantity%%','validation'=>'number'),
				'remove'=>array('type'=>'hidden','required'=>false,'name'=>'removeProduct[%%key%%]','value'=>'0'),
				'updateCart'=>array('type'=>'hidden','value'=>1)
			),
			'contactUs'=>array(
				'name'=>array('type'=>'textfield','required'=>true,'prettyName'=>'Your Name','class'=>'form-control input-box','placeholder'=>'Your Name'),
				'email'=>array('type'=>'textfield','required'=>true,'validation'=>'email','class'=>'form-control input-box','prettyName'=>'Email','placeholder'=>'Email'),
				'phone'=>array('type'=>'textfield','required'=>false,'class'=>'form-control input-box','prettyName'=>'Phone','placeholder'=>'Phone'),
				'message'=>array('type'=>'textarea','class'=>'form-control'),
				'contactUs'=>array('type'=>'hidden','value'=>1),
				'captcha'=>array('type'=>'invisibleCaptcha','required'=>true,'prettyName'=>'Captcha Field','validation'=>'captcha'),
				'r_id'=>array('type'=>'hidden','value'=>rand()),
				't_id'=>array('type'=>'hidden','value'=>'%%module:fetemplate_id%%'),
				'r_secret'=>array('type'=>'tag','value'=>0,'persist'=>true),
				'g-recaptcha-response'=>array('type'=>'hidden'),
				'submitBtn'=>array('type'=>'submitbutton','value'=>'Send Your Request','class'=>'action-cart')
			),
			'siteSearch'=>array(
				'siteSearchForm'=>array('type'=>'hidden','value'=>1),
				'siteSearchText'=>array('type'=>'textfield','required'=>false,'placeholder'=>'Search entire store here ...')
			),
			'creditApplication'=>array(
				'contactUs'=>array('type'=>'hidden','value'=>1),
				'r_id'=>array('type'=>'hidden','value'=>rand()),
				'company'=>array('type'=>'textfield','name'=>'company','required'=>true,'class'=>'form-control','placeholder'=>'Company' ),
				'incorporated'=>array('type'=>'checkbox','value'=>1,'class'=>'form-control','checked'=>false,'checkType'=>1,'placeholder'=>'' ),
				'partnership'=>array('type'=>'checkbox','value'=>1,'class'=>'form-control','checked'=>false,'checkType'=>1,'placeholder'=>'' ),
				'soleProprietor'=>array('type'=>'checkbox','value'=>1,'class'=>'form-control','checked'=>false,'checkType'=>1,'placeholder'=>'' ),
				'line1'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','prettyName'=>'Address Line 1','placeholder'=>'Address' ),
				'suite'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Suite' ),
				'city'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','placeholder'=>'City' ),
				'country_id'=>array('type'=>'countrySelect','required'=>true,'class'=>'form-control','prettyName'=>'Country','placeholder'=>'Country'),
				'province_id'=>array('type'=>'provinceSelect','required'=>true,'class'=>'form-control','prettyName'=>'Province/State','placeholder'=>'Province'),
				'postalCode'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','prettyName'=>'Postal/Zip Code','placeholder'=>'Postal/Zip Code'),
				'phone'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','placeholder'=>'Phone'),
				'fax'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Fax'),
				'email'=>array('type'=>'textfield','required'=>true,'validation'=>'email','class'=>'form-control','placeholder'=>'Email'),
				'contact'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','prettyName'=>'Contact Name','placeholder'=>'Contact Name'),
				'contactExt'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Contact Ext'),
				'apContact'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','prettyName'=>'AP Contact','placeholder'=>'A/P Contact'),
				'apExt'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'A/P Ext'),
				'businessType'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','prettyName'=>'Business Type','placeholder'=>'Business Type'),
				'expectedBillings'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control','prettyName'=>'Expected Billings','placeholder'=>'Expected Billings'),
				'owner1'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Owner #1'),
				'owner2'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Owner #2'),
				'owner3'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Owner #3'),
				'comments'=>array('type'=>'textarea','required'=>false,'class'=>'form-control','placeholder'=>''),
				'bankName'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','prettyName'=>'Bank Name','placeholder'=>'Bank Name'),
				'bankAddress'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','prettyName'=>'Bank Address','placeholder'=>'Bank Address'),
				'bankManager'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','prettyName'=>'Bank Manager','placeholder'=>'Bank Manager'),
				'bankPhone'=>array('type'=>'textfield','required'=>true,'class'=>'form-control','prettyName'=>'Bank Phone','placeholder'=>'Bank Phone'),
				'trName1'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Name','placeholder'=>'Ref #1 Company'),
				'trName2'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Name','placeholder'=>'Ref #2 Company'),
				'trName3'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Name','placeholder'=>'Ref #3 Company'),
				'trAddress1'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Address','placeholder'=>'Ref #1 Address'),
				'trAddress2'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Address','placeholder'=>'Ref #2 Address'),
				'trAddress3'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Address','placeholder'=>'Ref #3 Address'),
				'trContact1'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Contact','placeholder'=>'Ref #1 Name'),
				'trContact2'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Contact','placeholder'=>'Ref #2 Name'),
				'trContact3'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Contact','placeholder'=>'Ref #3 Name'),
				'trPhone1'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Phone','placeholder'=>'Ref #1 Phone'),
				'trPhone2'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Phone','placeholder'=>'Ref #2 Phone'),
				'trPhone3'=>array('type'=>'textfield','required'=>false,'class'=>'form-control','placeholder'=>'Phone','placeholder'=>'Ref #3 Phone'),
				'submitBtn'=>array('type'=>'submitbutton','value'=>'Apply Today','class'=>'action-cart')
			),
			'categoryView'=>array(
				'pager'=>array('type'=>'select','options'=>array('8'=>'8','12'=>'12','16'=>'16')),
				'pagenum'=>array('type'=>'hidden')
			),
			'ropeSling'=>array(
				'grade'=>array('type'=>'select','required'=>true,'class'=>'form-control','onchange'=>'setWireClass(this);'),
				'length'=>array('type'=>'number','required'=>true,'min'=>0,'class'=>'form-control text-right'),
				'lengthUnits'=>array('type'=>'select','lookup'=>'dimensions','required'=>true,'class'=>'form-control'),
				'xxtype'=>array('type'=>'select','required'=>false,'class'=>'form-control'),
				'size'=>array('type'=>'select','required'=>true,'class'=>'form-control','onchange'=>'setSize(this);return false;'),
				'ropeSling'=>array('type'=>'hidden','value'=>1)
			),
			'chainSling'=>array(
				'grade'=>array('type'=>'select','required'=>true,'class'=>'form-control','onchange'=>'setChainClass(this);'),
				'legs'=>array('type'=>'select','required'=>true,'options'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),'onchange'=>'chainLegs(this);','class'=>'form-control text-right'),
				'length'=>array('type'=>'number','required'=>true,'min'=>0,'class'=>'form-control text-right'),
				'lengthUnits'=>array('type'=>'select','lookup'=>'dimensions','required'=>true,'class'=>'form-control'),
				'hook1'=>array('type'=>'select','name'=>'hook[1]','required'=>true,'class'=>'form-control'),
				'hook2'=>array('type'=>'select','name'=>'hook[2]','required'=>true,'class'=>'form-control'),
				'hook3'=>array('type'=>'select','name'=>'hook[3]','required'=>true,'class'=>'form-control'),
				'hook4'=>array('type'=>'select','name'=>'hook[4]','required'=>true,'class'=>'form-control'),
				'size'=>array('type'=>'select','required'=>true,'class'=>'form-control','onchange'=>'setSize(this);return false;'),
				'adjustable'=>array('type'=>'select','lookup'=>'chain_adjustable','required'=>true,'class'=>'form-control'),
				'chainSling'=>array('type'=>'hidden','value'=>1)
			),
			'roundSling'=>array(
				'length'=>array('type'=>'number','required'=>true,'min'=>1,'class'=>'form-control text-right'),
				'lengthUnits'=>array('type'=>'select','lookup'=>'dimensions','required'=>true,'class'=>'form-control'),
				'color'=>array('type'=>'select','required'=>true,'lookup'=>'roundsling_grade','class'=>'form-control'),
				'roundSling'=>array('type'=>'hidden','value'=>1)
			),
			'webSling'=>array(
				'webType'=>array('type'=>'select','lookup'=>'websling_type','required'=>true,'onchange'=>'webTypeClass(this);','class'=>'form-control'),
				'webMaterial'=>array('type'=>'select','lookup'=>'websling_material','required'=>true,'class'=>'form-control'),
				'webPly'=>array('type'=>'select','options'=>array('1'=>'1 Ply','2'=>'2 Ply'),'required'=>true,'class'=>'form-control','onchange'=>'webTypeClass(this);'),
				'size'=>array('type'=>'select','required'=>true,'class'=>'form-control','onchange'=>'setSize(this);return false;','value'=>'2"'),
				'length'=>array('type'=>'number','required'=>true,'min'=>1,'class'=>'form-control text-right'),
				'lengthUnits'=>array('type'=>'select','lookup'=>'dimensions','required'=>true,'class'=>'form-control'),
				'webSling'=>array('type'=>'hidden','value'=>1)
			)
		);
	}

	function postSaleProcessing($orderId,$valid,$caller) {
		if (array_key_exists('cart',$_SESSION) && array_key_exists('abandoned',$_SESSION['cart'])) {
			if (array_key_exists('id',$_SESSION['cart']['abandoned'])) {
				$this->execute(sprintf('delete from cart_header where id = %d',$_SESSION['cart']['abandoned']['id']));
				$this->execute(sprintf('delete from cart_lines where order_id = %d',$_SESSION['cart']['abandoned']['id']));
			}
			unset($_SESSION['cart']['abandoned']);
		}
		foreach($_SESSION['cart']['products'] as $key=>$prod) {
			if (array_key_exists("inventory_id",$prod) && $prod["inventory_id"] > 0) {
				Inventory::updateInventory( $prod["inventory_id"], -$prod["quantity"] * $prod["qty_multiplier"], $orderId, sprintf("Order #%s", $orderId) );
			}
		}
		$orderHtml = "";
		$emails = $this->configEmails("ecommerce");
		if (count($emails) == 0)
			$emails = $this->configEmails("contact");
		$this->logMessage('postSaleProcessing',sprintf('notifying on order [%d] status [%s] emails [%s] caller [%s]',$orderId,$valid,print_r($emails,true),print_r($caller,true)),1);
		$body = new Forms();
		$mailer = new MyMailer();
		$mailer->Subject = sprintf("Order Processing - %s", SITENAME);
		$body = new Forms();
		$sql = sprintf('select * from htmlForms where class = %d and type = "orderEmail"',$this->getClassId('product'));
		$html = $this->fetchSingle($sql);
		$body->setHTML($html['html']);
		if (!$order = $this->fetchSingle(sprintf('select o.*, m.firstname, m.lastname, m.email from orders o, members m where o.id = %d and m.id = o.member_id',$orderId)))
			$this->logMessage(__FUNCTION__,sprintf('cannot locate order #[%d]',$orderId),1,true);
		$body->addData($this->formatOrder($order));
		if ($caller->hasOption('receiptPrint') && $module = $this->fetchSingle(sprintf('select t.*, m.classname, t.id as fetemplate_id from fetemplates t, modules m where t.id = %d and m.id = t.module_id',$caller->getOption('receiptPrint')))) {
			$this->logMessage('postSaleProcessing',sprintf('caller module [%s] this [%s]',print_r($module,true),print_r($this,true)),4);
			$class = new $module['classname']($module['id'],$module);
			$orderHtml = $class->{$module['module_function']}();
			$body->addTag('order',$orderHtml,false);
		}
		if ($this->hasOption('receiptPrint') && $module = $this->fetchSingle(sprintf('select t.*, m.classname from fetemplates t, modules m where t.id = %d and m.id = t.module_id',$this->getOption('receiptPrint')))) {
			$this->logMessage('postSalePrcessing',sprintf('this module [%s] this [%s]',print_r($module,true),print_r($this,true)),2);
			$class = new $module['classname']($module['id'],$module);
			$orderHtml = $class->{$module['module_function']}();
			$body->addTag('order',$orderHtml,false);
		}
		$body->setOption('formDelimiter','{{|}}');
		$mailer->Body = $body->show();
		$mailer->From = $order['email'];
		$mailer->FromName = $order['firstname'].' '.$order['lastname'];
		$mailer->IsHTML(true);
		foreach($emails as $key=>$value) {
			$mailer->addAddress($value['email'],$value['name']);
		}
		if (!$mailer->Send()) {
			$this->logMessage('postSaleProcessing',sprintf("Email send failed [%s]",print_r($mailer,true)),1,true);
		}
		if (($this->hasOption('userEmail') || $caller->hasOption('userEmail')) && strlen($orderHtml) > 0) {
			/*
				User Email here
			*/
			$this->logMessage(__FUNCTION__,'sending user receipt email',3);
			$mailer = new MyMailer();
			$mailer->Subject = sprintf("Your Order Receipt - %s", SITENAME);
			$body = new Forms();
			$sql = sprintf('select * from htmlForms where class = %d and type = "userEmail"',$this->getClassId('product'));
			$html = $this->fetchSingle($sql);
			$body->setHTML($html['html']);
			$body->addData($this->formatOrder($order));
			$body->addTag('order',$orderHtml,false);
			$body->setOption('formDelimiter','{{|}}');
			$mailer->Body = $body->show();
			$mailer->From = $emails[0]['email'];
			$mailer->FromName = $emails[0]['name'];
			$mailer->IsHTML(true);	
			$mailer->addAddress($order['email'],$order['firstname'].' '.$order['lastname']);
			if (!$mailer->Send()) {
				$this->logMessage(__FUNCTION__,sprintf("User Email send failed [%s]",print_r($mailer,true)),1,true);
			}
		}
	}

	function xxpostNewsletterSignup($memberId,$obj,$module) {
		$obj->logMessage(__FUNCTION__,sprintf('member id [%d]',$memberId),1);
		$outer = new Forms();
		$outer->init($this->m_dir.$module['parm2']);
		if ($data = $this->fetchSingle(sprintf("select * from subscriber where id = %d", $memberId))) {
			$outer->addData($data);
			$html = $outer->show();
			$mailer = new MyMailer();
			$mailer->Subject = sprintf($module["parm3"], SITENAME);
			$mailer->Body = $html;
			$mailer->From = 'noreply@'.HOSTNAME;
			$mailer->FromName = SITENAME;
			$mailer->IsHTML(true);	
			$mailer->addAddress($data['email'],$data['firstname'].' '.$data['lastname']);
			if (!$mailer->Send()) {
				$this->logMessage(__FUNCTION__,sprintf("Newsletter signup Email send failed [%s]",print_r($mailer,true)),1,true);
			}
		}
	}

	function rssParse($data) {
		$ct = preg_match("/<p>(.*?)<\/p>/s",$data['description'],$results);
		if ($ct > 0)
			$data['description'] = $results[0];
		$this->logMessage(__FUNCTION__,sprintf('ct = [%s], results [%s] data [%s]',$ct,print_r($results,true),print_r($data,true)),1);
		return $data;
	}

	function initHook() {
	}

	function preRecalc($cart) {
		return $cart;
	}

	function calcShipping($cart) {
		return $cart;
	}

	function initCart($cart) {
		$cart['header']['freeShipping'] = 0;
		return $cart;
	}

	function formatCart($cart) {
		$cart['header']['formattedFreeShipping'] = $this->my_money_format($cart['header']['freeShipping']);
		$cart['totalSavings'] = $cart['header']['savings'] + $cart['header']['freeShipping'] - $cart['header']['line_discounts'];
		$cart['header']['formattedTotalSavings'] = $this->my_money_format($cart['totalSavings']);
		return $cart;
	}

	function manufacturerList() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->setModule($module);
		$outer->init($this->m_dir.$module['outer_html']);
		$inner = new Forms();
		$inner->setModule($module);
		$inner->init($this->m_dir.$module['inner_html']);
		$flds = $outer->buildForm($this->config->getFields($module['configuration']));
		$flds = $inner->buildForm($this->config->getFields($module['configuration']."Row"));
		$recs = $this->fetchAll(sprintf("select *, id from code_lookups where type='manufacturer' order by sort"));
		$rows = array();
		if (array_key_exists(__FUNCTION__,$_REQUEST) && !array_key_exists("manufacturer",$_REQUEST)) $_REQUEST["manufacturer"] = array();
		if (array_key_exists("manufacturer_id",$_SESSION) & !array_key_exists("manufacturer",$_REQUEST)) {
			$_REQUEST["manufacturer"] = $_SESSION["manufacturer_id"];
			$_REQUEST[__FUNCTION__] = 1;
		}
		foreach($recs as $k=>$v) {
			$inner->setData("autoCheck","");
			$inner->addData($v);
			if (count($_REQUEST) > 0 && array_key_exists(__FUNCTION__,$_REQUEST) && array_key_exists("manufacturer",$_REQUEST)) {
				$_SESSION["manufacturer_id"] = $_REQUEST["manufacturer"];
				foreach($_REQUEST["manufacturer"] as $sk=>$sv) {
					$this->logMessage(__FUNCTION__,sprintf("compare [%s] vs [%s]",$v["id"],$sv),1);
					if ($sv == $v["id"]) {
						$inner->setData("autoCheck",1);
						$inner->setData("manufacturer",$v["id"]);
						$this->logMessage(__FUNCTION__,sprintf("form [%s]", print_r($inner,true)),1);
					}
				}
			}
			$rows[] = $inner->show();
		}
		$outer->setData("rows",implode("",$rows));
		return $outer->show();
	}

	function manufacturerSearch() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->setModule($module);
		$outer->init($this->m_dir.$module['outer_html']);
		$inner = new Forms();
		$inner->setModule($module);
		$inner->init($this->m_dir.$module['inner_html']);
		$flds = $outer->buildForm($this->config->getFields($module['configuration']));
		$flds = $inner->buildForm($this->config->getFields($module['configuration']."Row"));
		if (!(array_key_exists("manufacturer_id",$_SESSION) && count($_SESSION["manufacturer_id"]) > 0)) $_SESSION["manufacturer_id"] = $this->fetchScalarAll(sprintf("select id from code_lookups where type='manufacturer'"));
		$p = new product(0,$module);
		$sql = sprintf("select p.* from product p where p.manufacturer_id in (%s) and p.deleted = 0 and p.published = 1 and p.enabled = 1", implode(",",$_SESSION["manufacturer_id"]));
		$additional = array();
		if ($module["folder_id"] != 0) {
			if ($module["include_subfolder"] != 0) {
				$ids = $this->fetchScalar(sprintf("select id from product_folders pf1, pf2 where pf1.id = %d and pf2.left_id >= pf1.left_id and pf2.right_id <= pf1.right_id", $module["folder_id"]));
				$additional["folder_id"] = sprintf(" and p.id in (select product_id from product_by_folder where folder_id in (%s))", implode(",",$ids));
			}
			else {
				$additional["folder_id"] = sprintf(" and p.id in (select product_id from product_by_folder where folder_id = %d)", $module["folder_id"]);
			}
		}
		$sql .= implode(" ",$additional);
		$pagination = $this->getPagination($sql,$module,$recordCount);
		$products = $this->fetchAll($sql);
		$rows = array();
		foreach($products as $k=>$v) {
			$inner->addData($p->formatData($v));
			$rows[] = $inner->show();
		}
		$outer->setData("products",implode("",$rows));
		$outer->setData("pagination",$pagination,false);
		return $outer->show();
	}

	function cookieTest() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->setModule($module);
		$outer->init($this->m_dir.$module['outer_html']);
		$parm = $this->getOption("cookie");
		$this->logMessage(__FUNCTION__,sprintf("cookies [%s]", print_r($_COOKIE,true)),1);
		if (!(array_key_exists($parm,$_COOKIE) && $_COOKIE[$parm] == "1")) {
			$subdata = $this->subForms($this->m_module['fetemplate_id'],null,array(),'outer');
			foreach($subdata as $key=>$value) {
				$outer->addTag($key,$value,false);
			}
			return $outer->show();
		}
	}

	function setCookie() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->setModule($module);
		$outer->init($this->m_dir.$module['outer_html']);
		$parm = $this->getOption("cookie");
		$_COOKIE[$parm] = "1";
		$subdata = $this->subForms($this->m_module['fetemplate_id'],null,array('folder_id'=>$folder['id']),'outer');
		foreach($subdata as $key=>$value) {
			$outer->addTag($key,$value,false);
		}
		return $outer->show();
	}

	function provinceName($fld,$obj) {
		return $this->fetchScalar(sprintf("select province_code from provinces where id = %d",$obj->getData("province_id")));
	}

	function countryName($fld,$obj) {
		return $this->fetchScalar(sprintf("select country from countries where id = %d", $obj->getData("country_id")));
	}

	function ropeSling() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->setModule($module);
		$outer->init($this->m_dir.$module['outer_html']);
		if (array_key_exists(__FUNCTION__,$_POST)) $outer->addData($_POST);
		$inner = new Forms();
		$inner->setModule($module);
		$inner->init($this->m_dir.$module['inner_html']);
		$spec = new Forms();
		$spec->init($this->m_dir.$module['parm1']);
		$flds = $this->config->getFields($module['configuration']);
		$flds['grade']['lookup'] = $this->getOption("spec_table");
		$flds['type']['lookup'] = $this->getOption("type_table");
		$flds['size']['lookup'] = $this->getOption("size_table");
		$flds = $outer->buildForm($flds);
		$flds = $inner->buildForm($this->config->getFields($module['configuration']."Row"));
		$flds = $spec->buildForm($this->config->getFields($module['configuration']."Row"));
		$data = $this->fetchAll(sprintf("select * from code_lookups where type='%s' order by sort", $this->getOption("spec_table")));
		$tables = array();
		foreach($data as $k=>$v) {
			$tmp = array();
			$s = str_replace("\n", '|', $v["extra"]);
			$grades = explode('|',$s);
			$this->logMessage(__FUNCTION__,sprintf("grades [%s] v [%s]", print_r($grades,true), print_r($s,true)),1);
			$v["rowspan"] = count($grades);
			$v["row"] = -1;
			$inner->addData($v);
			$inner->setData("type",$v);
			foreach($grades as $k1=>$v1) {
				$inner->setData("row",$k1);
				$recs = array();
				$specs = explode("^",$v1);
				foreach($specs as $sk=>$sv) {
					if ($sk > 0)
						$spec->setData("spec",number_format($sv, 0, '.', ','));
					else {
						$spec->setData("spec",$sv);
						$inner->setData("size",$sv);
					}
					$recs[] = $spec->show();
				}
				$inner->setData("specs",implode("",$recs));
				$tmp[] = $inner->show();
			}
			$tables[$v["code"]] = implode("",$tmp);
		}
		$outer->setData("tables",$tables);
		return $outer->show();
	}

	function roundSling() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->setModule($module);
		$outer->init($this->m_dir.$module['outer_html']);
		if (array_key_exists(__FUNCTION__,$_POST)) $outer->addData($_POST);
		$inner = new Forms();
		$inner->setModule($module);
		$inner->init($this->m_dir.$module['inner_html']);
		$spec = new Forms();
		$spec->init($this->m_dir.$module['parm1']);
		$flds = $this->config->getFields($module['configuration']);
		$flds['type']['lookup'] = $this->getOption("type_table");
		$flds = $outer->buildForm($flds);
		$flds = $inner->buildForm($this->config->getFields($module['configuration']."Row"));
		$flds = $spec->buildForm($this->config->getFields($module['configuration']."Row"));
		$data = $this->fetchAll(sprintf("select * from code_lookups where type='%s' order by sort", $this->getOption("spec_table")));
		$tables = array();
		foreach($data as $k=>$v) {
			$tmp = array();
			$s = str_replace("\n", '|', $v["extra"]);
			$colors = explode('^',$s);
			$inner->addData($v);
			$inner->setData("colors",$colors);
			$tables[] = $inner->show();
		}
		$outer->setData("specs",implode("",$tables));
		$this->logMessage(__FUNCTION__,sprintf("outer show [%s] form [%s] data [%s]", $outer->show(), print_r($outer,true), print_r($data,true)),1);
		return $outer->show();
	}

	function chainSling() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->setModule($module);
		$outer->init($this->m_dir.$module['outer_html']);
		if (array_key_exists(__FUNCTION__,$_POST)) $outer->addData($_POST);
		$this->logMessage(__FUNCTION__,sprintf("post [%s] request [%s] outer [%s]", print_r($_POST,true), print_r($_REQUEST,true), print_r($outer,true)),1);
		$inner = new Forms();
		$inner->setModule($module);
		$inner->init($this->m_dir.$module['inner_html']);
		$spec = new Forms();
		$spec->init($this->m_dir.$module['parm1']);
		$flds = $this->config->getFields($module['configuration']);
		$flds['grade']['lookup'] = $this->getOption("spec_table");
		$flds['type']['lookup'] = $this->getOption("type_table");
		$flds['size']['lookup'] = $this->getOption("size_table");
		$flds['hook1']['sql'] = sprintf("select p.name, p.name from product p, product_by_folder pf where pf.folder_id = %d and p.id = pf.product_id and p.deleted = 0 and p.enabled = 1 and p.published = 1 order by p.name", $this->getOption("hook_folder"));
		$flds['hook2']['sql'] = $flds['hook1']['sql'];
		$flds['hook3']['sql'] = $flds['hook1']['sql'];
		$flds['hook4']['sql'] = $flds['hook1']['sql'];
		$flds = $outer->buildForm($flds);
		$flds = $inner->buildForm($this->config->getFields($module['configuration']."Row"));
		$flds = $spec->buildForm($this->config->getFields($module['configuration']."Row"));
		$data = $this->fetchAll(sprintf("select * from code_lookups where type='%s' order by sort", $this->getOption("spec_table")));
		$tables = array();
		foreach($data as $k=>$v) {
			$tmp = array();
			$s = str_replace("\n", '|', $v["extra"]);
			$grades = explode('|',$s);
			$this->logMessage(__FUNCTION__,sprintf("grades [%s] v [%s]", print_r($grades,true), print_r($s,true)),1);
			$v["rowspan"] = count($grades);
			$v["row"] = -1;
			$inner->addData($v);
			$inner->setData("type",$v);
			foreach($grades as $k1=>$v1) {
				$inner->setData("row",$k1);
				$recs = array();
				$specs = explode("^",$v1);
				foreach($specs as $sk=>$sv) {
					if ($sk > 0)
						$spec->setData("spec",number_format($sv, 0, '.', ','));
					else {
						$spec->setData("spec",$sv);
						$inner->setData("size",$sv);
					}
					$recs[] = $spec->show();
				}
				$inner->setData("specs",implode("",$recs));
				$tmp[] = $inner->show();
			}
			$tables[$v["code"]] = implode("",$tmp);
		}
		$outer->setData("tables",$tables);
		$this->logMessage(__FUNCTION__,sprintf("outer [%s]", print_r($outer,true)),1);
		return $outer->show();
	}

	function webSling() {
		if (!$module = $this->getModule())
			return "";
		$outer = new Forms();
		$outer->setModule($module);
		$outer->init($this->m_dir.$module['outer_html']);
		if (array_key_exists(__FUNCTION__,$_POST)) $outer->addData($_POST);
		$inner = new Forms();
		$inner->setModule($module);
		$inner->init($this->m_dir.$module['inner_html']);
		$spec = new Forms();
		$spec->init($this->m_dir.$module['parm1']);
		$flds = $this->config->getFields($module['configuration']);
		$flds['type']['lookup'] = $this->getOption("type_table");
		$flds['size']['lookup'] = $this->getOption("size_table");
		$flds = $outer->buildForm($flds);
		$flds = $inner->buildForm($this->config->getFields($module['configuration']."Row"));
		$flds = $spec->buildForm($this->config->getFields($module['configuration']."Row"));
		$data = $this->fetchAll(sprintf("select * from code_lookups where type='%s' order by sort", $this->getOption("spec_table")));
		$tables = array();
		foreach($data as $k=>$v) {
			$tmp = array();
			$s = str_replace("\n", '|', $v["extra"]);
			$grades = explode('|',$s);
			$this->logMessage(__FUNCTION__,sprintf("grades [%s] v [%s]", print_r($grades,true), print_r($s,true)),1);
			$v["rowspan"] = count($grades);
			$v["row"] = -1;
			$inner->addData($v);
			$inner->setData("type",$v);
			foreach($grades as $k1=>$v1) {
				$inner->setData("row",$k1);
				$recs = array();
				$specs = explode("^",$v1);
				foreach($specs as $sk=>$sv) {
					if ($sk > 1)
						$spec->setData("spec",number_format($sv, 0, '.', ','));
					else {
						$spec->setData("spec",$sv);
						if ($sk == 0) $inner->setData("size",$sv);
					}
					$spec->setData("col",$sk);
					$recs[] = $spec->show();
				}
				$inner->setData("specs",implode("",$recs));
				$tmp[] = $inner->show();
			}
			$tables[$v["code"]] = implode("",$tmp);
		}
		$outer->setData("tables",$tables);
		return $outer->show();
	}

	function customAddToCart() {
		$module = $this->getModule();
		$this->logMessage(__FUNCTION__,sprintf("module [%s]", print_r($module,true)),1);
		if (!array_key_exists("cart",$_SESSION)) $_SESSION["cart"] = Ecom::initCart();
		$html = "";
		if (array_key_exists(__FUNCTION__,$_POST)) {
			$prod = $this->fetchSingle(sprintf("select * from product where id = %d", $_POST["product_id"]));
			$outer = new Forms();
			$outer->addData($_POST);
			$outer->init($this->m_dir.$module['outer_html']);
			switch($prod["custom_sling_type"]) {
				case "WIRE":
					$outer->buildForm($this->getFields("ropeSling"));
					break;
				case "WEB":
					$outer->buildForm($this->getFields("webSling"));
					break;
				case "ROUND":
					$outer->buildForm($this->getFields("roundSling"));
					break;
				case "CHAIN":
					$outer->buildForm($this->getFields("chainSling"));
					break;
				default:
					break;
			}
			$outer->addData($_POST);
			if ($outer->validate()) {
				$cart = $_SESSION["cart"];
				$prod["custom_data"] = $_POST;
				$key = sprintf("%d|0|0|%d", $prod["id"], count($cart["products"]));
				$e = new Ecom();
				$cart = $e->updateLine($cart, $key, $prod["id"], 0, 0, 0, $_POST["product_quantity"], 0, 0, "");
				$cart["products"][$key]["shipping"] = 0;
				$cart["products"][$key]["regularPrice"] = 0;
				$cart["products"][$key]["price"] = 0;
				$cart["products"][$key] = $e->lineValue($cart["products"][$key]);
				$cart["products"][$key]["custom_fields"] = $_POST;
				$this->logMessage(__FUNCTION__,sprintf("cart [%s]", print_r($cart,true)),1);
				$_SESSION["cart"] = $cart;
				$this->addMessage("Item added to your cart");
			}
			$html = $outer->show();
		}
		return $html;
	}

	function getModuleInfo() {
		return parent::getModuleList(array("manufacturerList","manufacturerSearch","cookieTest","setCookie","ropeSling","roundSling","chainSling","webSling","customAddToCart"));
	}

}

?>
