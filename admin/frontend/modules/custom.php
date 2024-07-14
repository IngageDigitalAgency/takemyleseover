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
			'forgotPassword'=>array(
				'forgotPassword'=>array('type'=>'hidden','value'=>1),
				'email'=>array('type'=>'textfield','validation'=>'email','required'=>true,'placeholder'=>'Email you registered with','class'=>'form-control input-large'),
				'submit'=>array('type'=>'submitButton','value'=>'Send me the Password')
			),
			'login'=>array(
				'email'=>array('type'=>'textfield','required'=>true,'prettyName'=>'Email','placeholder'=>'Email','class'=>'form-control input-large'),
				'password'=>array('type'=>'password','required'=>true,'prettyName'=>'Password','placeholder'=>'Password','class'=>'form-control input-large'),
				'loginForm'=>array('type'=>'hidden','value'=>1)
			),
			'signup'=>array(
                                'r_id'=>array('type'=>'hidden','value'=>rand()),
                                'captcha'=>array('type'=>'captcha','required'=>true,'prettyName'=>'Captcha Field','validation'=>'captcha'),
                                'r_secret'=>array('type'=>'tag','value'=>0,'persist'=>true),
				'password'=>array('type'=>'password','required'=>true,'prettyName'=>'Password','class'=>'def_field_input','validation'=>'password','class'=>'form-control input-large'),
				'password_confirm'=>array('type'=>'password','required'=>true,'prettyName'=>'Password Confirm','class'=>'def_field_input','database'=>false,'class'=>'form-control input-large'),
				'firstname'=>array('type'=>'textfield','required'=>true,'prettyName'=>'First Name','class'=>'def_field_input','class'=>'form-control input-large'),
				'lastname'=>array('type'=>'textfield','required'=>true,'prettyName'=>'Last Name','class'=>'def_field_input','class'=>'form-control input-large'),
				'company'=>array('type'=>'textfield','required'=>false,'prettyName'=>'Company','class'=>'form-control input-large'),
				'country_id'=>array('name'=>'address[country_id]','type'=>'select','required'=>true,'sql'=>'select id,country from countries where deleted = 0 order by sort','id'=>'country_id','class'=>'form-control input-large'),
				'province_id'=>array('name'=>'address[province_id]','type'=>'provinceSelect','required'=>true,'sql'=>'select p.id,p.province from provinces p, countries c where p.deleted = 0 and p.country_id = c.id and c.sort = (select min(c1.sort)  from countries c1 where c1.deleted = 0 order by c1.sort) order by p.sort','id'=>'province_id','prettyName'=>'Province/State','class'=>'form-control input-large'),
				'city'=>array('name'=>'address[city]','type'=>'textfield','required'=>true,'prettyName'=>'City','class'=>'form-control input-large'),
				'line1'=>array('name'=>'address[line1]','type'=>'textfield','required'=>true,'prettyName'=>'Address Line 1','class'=>'form-control input-large'),
				'line2'=>array('name'=>'address[line2]','type'=>'textfield','required'=>false,'class'=>'form-control input-large'),
				'postalcode'=>array('name'=>'address[postalcode]','type'=>'textfield','required'=>true,'prettyName'=>'Postal/Zip','validation'=>'postalcode','class'=>'form-control input-large'),
				'phone1'=>array('type'=>'textfield','required'=>true,'prettyName'=>'Phone','name'=>'address[phone1]','class'=>'form-control input-large'),
				'phone2'=>array('type'=>'textfield','required'=>false,'prettyName'=>'Cellular','name'=>'address[phone2]','class'=>'form-control input-large'),
				'fax'=>array('type'=>'textfield','required'=>false,'name'=>'address[fax]','class'=>'form-control input-large'),
				'email'=>array('type'=>'textfield','required'=>true,'validation'=>'email','prettyName'=>'E-Mail','class'=>'def_field_input','class'=>'form-control input-large'),
				'email_confirm'=>array('type'=>'textfield','required'=>true,'validation'=>'email','prettyName'=>'E-Mail Confirm','database'=>false,'class'=>'def_field_input','class'=>'form-control input-large'),
				'signupForm'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'm_id'=>array('type'=>'hidden','database'=>false),
				'a_id'=>array('type'=>'hidden','database'=>false),
				'saveButton'=>array('type'=>'submitbutton','value'=>'Create Your Account','database'=>false,'onclick'=>'formSubmit(this)','class'=>'button fontsize10'),
				'options'=>array('name'=>'signup','method'=>'post','action'=>'')
			),
			'accountInfo'=>array(
				'addressId'=>array('type'=>'hidden','database'=>false),
				'editBtn'=>array('type'=>'link','href'=>'#','onclick'=>'fnSubmit(this,0);return false;','value'=>'Edit Account Details','database'=>false),
				'options'=>array('name'=>'accountInfo','method'=>'post','action'=>''),
				'firstname'=>array('type'=>'textfield','prettyName'=>'First Name','disabled'=>true,'class'=>'form-control input-large'),
				'lastname'=>array('type'=>'textfield','prettyName'=>'Last Name','disabled'=>true,'class'=>'form-control input-large'),
				'phone1'=>array('type'=>'textfield','prettyName'=>'Phone','name'=>'address[phone1]','disabled'=>true,'class'=>'form-control input-large'),
				'line1'=>array('name'=>'address[line1]','type'=>'textfield','prettyName'=>'Address Line 1','disabled'=>true,'class'=>'form-control input-large'),
				'country_id'=>array('name'=>'address[country_id]','type'=>'countrySelect','disabled'=>true,'class'=>'form-control input-large'),
				'province_id'=>array('name'=>'address[province_id]','type'=>'provinceSelect','id'=>'province_id','disabled'=>true,'class'=>'form-control input-large'),
				'city'=>array('name'=>'address[city]','type'=>'textfield','prettyName'=>'City','disabled'=>true,'class'=>'form-control input-large'),
				'postalcode'=>array('name'=>'address[postalcode]','type'=>'textfield','prettyName'=>'Postal/Zip','disabled'=>true,'class'=>'form-control input-large'),
				'email'=>array('type'=>'textfield','validation'=>'email','prettyName'=>'Email','disabled'=>true,'class'=>'form-control input-large'),
				'emailConfirm'=>array('type'=>'textfield','validation'=>'email','prettyName'=>'E-Mail','database'=>false,'disabled'=>true,'class'=>'form-control input-large'),
				'password'=>array('type'=>'password','validation'=>'password','prettyName'=>'Password','database'=>false,'disabled'=>true,'class'=>'form-control input-large'),
				'passwordConfirm'=>array('type'=>'password','validation'=>'password','prettyName'=>'Password','database'=>false,'disabled'=>true,'class'=>'form-control input-large'),
				'saveInfo'=>array('type'=>'hidden','value'=>'0','database'=>false),
				'edit'=>array('type'=>'button','value'=>'Edit Information','class'=>'button button-red fontsize10','onclick'=>'eAccount(this);return false;'),
				'accountInfoForm'=>array('type'=>'hidden','value'=>'1','database'=>false)
			),
			'accountInfoEdit'=>array(
				'firstname'=>array('type'=>'textfield','required'=>true,'prettyName'=>'First Name','placeholder'=>'First Name','class'=>'form-control input-large'),
				'lastname'=>array('type'=>'textfield','required'=>true,'prettyName'=>'Last Name','placeholder'=>'Last Name','class'=>'form-control input-large'),
				'country_id'=>array('name'=>'address[country_id]','type'=>'countrySelect','required'=>true,'sql'=>'select id,country from countries where deleted = 0 order by sort','id'=>'country_id','class'=>'form-control input-large'),
				'province_id'=>array('name'=>'address[province_id]','type'=>'provinceSelect','required'=>true,'sql'=>'select p.id,p.province from provinces p, countries c where p.deleted = 0 and p.country_id = c.id and c.sort = (select min(c1.sort)  from countries c1 where c1.deleted = 0 order by c1.sort) order by p.sort','id'=>'province_id','prettyName'=>'Province/State','class'=>'form-control input-large'),
				'city'=>array('name'=>'address[city]','type'=>'textfield','required'=>true,'prettyName'=>'City','placeholder'=>'City','class'=>'form-control input-large'),
				'line1'=>array('name'=>'address[line1]','type'=>'textfield','required'=>true,'prettyName'=>'Address Line 1','placeholder'=>'Line 1','class'=>'form-control input-large'),
				'line2'=>array('name'=>'address[line2]','type'=>'textfield','required'=>false,'placeholder'=>'Line 2','class'=>'form-control input-large'),
				'postalcode'=>array('name'=>'address[postalcode]','type'=>'textfield','required'=>true,'prettyName'=>'Postal/Zip','placeholder'=>'Postal Code','validation'=>'postalcode','class'=>'form-control input-large'),
				'phone1'=>array('type'=>'textfield','required'=>true,'prettyName'=>'Phone','name'=>'address[phone1]','placeholder'=>'Phone','class'=>'form-control input-large'),
				'fax'=>array('type'=>'textfield','required'=>false,'name'=>'address[fax]','placeholder'=>'Fax','class'=>'form-control input-large'),
				'email'=>array('type'=>'textfield','required'=>true,'validation'=>'email','prettyName'=>'Email','placeholder'=>'Email','class'=>'form-control input-large'),
				'emailConfirm'=>array('type'=>'textfield','required'=>false,'validation'=>'email','prettyName'=>'E-Mail','database'=>false,'placeholder'=>'Email Confirm','class'=>'form-control input-large'),
				'password'=>array('type'=>'password','required'=>false,'validation'=>'password','prettyName'=>'Password','database'=>false,'class'=>'form-control input-large'),
				'passwordConfirm'=>array('type'=>'password','required'=>false,'validation'=>'password','prettyName'=>'Password','database'=>false,'class'=>'form-control input-large'),
				'accountInfoForm'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'addressId'=>array('type'=>'hidden','database'=>false),
				'saveBtn'=>array('type'=>'submitbutton','value'=>'Save Information','database'=>false,'class'=>'button button-red fontsize10','onclick'=>'eAccount(this);return false;'),
				'saveInfo'=>array('type'=>'hidden','value'=>'1','database'=>false),
				'options'=>array('name'=>'accountInfo','method'=>'post','action'=>'')
			),
			'searchForm'=>array(
				'search'=>array('type'=>'hidden','value'=>1),
				'body_style'=>array('name'=>'body_style[]','type'=>'select','idlookup'=>'body_style','class'=>'form-control','multiple'=>'multiple','defaultValue'=>0),
				'domestic'=>array('name'=>'domestic[]','type'=>'select','sql'=>'select id, name from lease_make where enabled = 1 and domestic = 1 order by name','class'=>'form-control','multiple'=>'multiple','defaultValue'=>0),
				'import'=>array('name'=>'import[]','type'=>'select','sql'=>'select id, name from lease_make where enabled = 1 and domestic = 0 order by name','class'=>'form-control','multiple'=>'multiple','defaultValue'=>0),
				'province_id'=>array('name'=>'province_id[]','type'=>'select','sql'=>'select id, province from provinces where deleted = 0 order by sort','class'=>'form-control','multiple'=>'multiple','defaultValue'=>0),
				'lease_id'=>array('type'=>'textfield','required'=>false,'validation'=>'number','class'=>'form-control','placeholder'=>'Listing ID Number'),
				'submit'=>array('type'=>'submitbutton','value'=>'SEARCH NOW!')
			),
			'searchResultsForm'=>array(
				'search'=>array('type'=>'hidden','value'=>1),
				'body_style'=>array('name'=>'body_style[]','type'=>'select','idlookup'=>'body_style','class'=>'form-control','multiple'=>'multiple','class'=>'hidden'),
				'domestic'=>array('name'=>'domestic[]','type'=>'select','sql'=>'select id, name from lease_make where enabled = 1 and domestic = 1 order by name','class'=>'form-control','multiple'=>'multiple','class'=>'hidden','defaultValue'=>0),
				'import'=>array('name'=>'import[]','type'=>'select','sql'=>'select id, name from lease_make where enabled = 1 and domestic = 0 order by name','class'=>'form-control','multiple'=>'multiple','class'=>'hidden','defaultValue'=>0),
				'province_id'=>array('name'=>'province_id[]','type'=>'select','sql'=>'select id, province from provinces where deleted = 0 order by sort','class'=>'form-control','multiple'=>'multiple','class'=>'hidden','defaultValue'=>0),
				'lease_id'=>array('type'=>'textfield','required'=>false,'validation'=>'number','class'=>'form-control','placeholder'=>'Listing ID Number','class'=>'hidden'),
				'submit'=>array('type'=>'submitbutton','value'=>'SEARCH NOW!')
			),
			'searchRefinement'=>array(
				'search'=>array('type'=>'hidden','value'=>1),
				'body_style'=>array('name'=>'body_style[]','type'=>'select','idlookup'=>'body_style','class'=>'form-control','multiple'=>'multiple','class'=>'hidden'),
				'domestic'=>array('name'=>'domestic[]','type'=>'select','sql'=>'select id, name from lease_make where enabled = 1 and domestic = 1 order by name','class'=>'form-control','multiple'=>'multiple','class'=>'hidden'),
				'import'=>array('name'=>'import[]','type'=>'select','sql'=>'select id, name from lease_make where enabled = 1 and domestic = 0 order by name','class'=>'form-control','multiple'=>'multiple','class'=>'hidden'),
				'province_id'=>array('name'=>'province_id[]','type'=>'select','sql'=>'select id, province from provinces where deleted = 0 order by sort','class'=>'form-control','multiple'=>'multiple','class'=>'hidden'),
				'lease_id'=>array('type'=>'textfield','required'=>false,'validation'=>'number','class'=>'form-control','placeholder'=>'Listing ID Number','class'=>'hidden'),
				'amt_from'=>array('type'=>'textfield','required'=>false,'validation'=>'number','class'=>'form-control','placeholder'=>'From'),
				'amt_to'=>array('type'=>'textfield','required'=>false,'validation'=>'number','class'=>'form-control','placeholder'=>'To'),
				'months_from'=>array('type'=>'textfield','required'=>false,'validation'=>'number','class'=>'form-control','placeholder'=>'From'),
				'months_to'=>array('type'=>'textfield','required'=>false,'validation'=>'number','class'=>'form-control','placeholder'=>'To'),
				'km_from'=>array('type'=>'textfield','required'=>false,'validation'=>'number','class'=>'form-control','placeholder'=>'From'),
				'km_to'=>array('type'=>'textfield','required'=>false,'validation'=>'number','class'=>'form-control','placeholder'=>'To'),
				'exterior_color'=>array('type'=>'select','required'=>false,'validation'=>'number','multiple'=>'multiple','idlookup'=>'exterior_color'),
				'transmission'=>array('type'=>'select','idlookup'=>'transmission','class'=>'form-control'),
				'sort_by'=>array('type'=>'select','lookup'=>'search_sorting','class'=>'form-control','database'=>false),
				'submit'=>array('type'=>'submitbutton','value'=>'REFINE SEARCH!','class'=>'button')
			),
			'vehicleInfo'=>array(
				'make'=>array('type'=>'select','sql'=>'select id,name from lease_make where enabled=1 order by name','required'=>true,'class'=>'form-control','onchange'=>'getModels($(this).val());return false;'),
				'model'=>array('type'=>'select','required'=>true,'class'=>'form-control','onchange'=>'getYears(this);return false;'),
				'year'=>array('type'=>'select','required'=>true,'class'=>'form-control'),
				'engine_size'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'cylinders'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'body_style'=>array('type'=>'select','name'=>'tbl[body_style]','idlookup'=>'body_style','required'=>true,'database'=>false,'class'=>'form-control','prettyName'=>'Body Style'),
				'transmission'=>array('type'=>'select','name'=>'tbl[transmission]','idlookup'=>'transmission','required'=>true,'database'=>false,'class'=>'form-control'),
				'exterior_color'=>array('type'=>'select','name'=>'tbl[exterior_color]','idlookup'=>'exterior_color','required'=>true,'database'=>false,'class'=>'form-control','prettyName'=>'Exterior Color'),
				'interior_color'=>array('type'=>'select','name'=>'tbl[interior_color]','idlookup'=>'interior_color','required'=>true,'database'=>false,'class'=>'form-control','prettyName'=>'Interior Color'),
				'mileage'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control','prettyName'=>'Current Km'),
				'heating___air'=>array('type'=>'select','idlookup'=>'heating___air','required'=>true,'name'=>'tbl[heating___air]','database'=>false,'class'=>'form-control','prettyName'=>'Heating & Air'),
				'power_features'=>array('type'=>'select','idlookup'=>'power_features','required'=>false,'name'=>'tbl[power_features][]','multiple'=>'multiple','database'=>false,'class'=>'form-control','prettyName'=>'Power Features'),
				'seating'=>array('type'=>'select','idlookup'=>'seating','required'=>true,'name'=>'tbl[seating]','database'=>false,'class'=>'form-control'),
				'fabric'=>array('type'=>'select','idlookup'=>'fabric','required'=>true,'name'=>'tbl[fabric]','database'=>false,'class'=>'form-control'),
				'tires___wheels'=>array('type'=>'select','idlookup'=>'tires___wheels','required'=>true,'name'=>'tbl[tires___wheels]','database'=>false,'class'=>'form-control','prettyName'=>'Tires & Wheels'),
				'winter_wheels'=>array('type'=>'checkbox','value'=>1),
				'winter_tires'=>array('type'=>'checkbox','value'=>1),
				'safety___security'=>array('type'=>'select','idlookup'=>'safety___security','required'=>false,'name'=>'tbl[safety___security][]','multiple'=>'multiple','database'=>false,'class'=>'form-control','prettyName'=>'Safety & Security'),
				'air_bags'=>array('type'=>'select','idlookup'=>'air_bags','required'=>false,'name'=>'tbl[air_bags][]','database'=>false,'class'=>'form-control','prettyName'=>'Air Bags','nonename'=>'-Air Bags-'),
				'audio___video'=>array('type'=>'select','idlookup'=>'audio___video','required'=>false,'name'=>'tbl[audio___video][]','multiple'=>'multiple','database'=>false,'class'=>'form-control','prettyName'=>'Audio/Video'),
				'warranty'=>array('type'=>'select','idlookup'=>'warranties','required'=>true,'name'=>'warranty','class'=>'form-control','prettyName'=>'Warranty'),
				'other_warranties'=>array('type'=>'select','idlookup'=>'other_warranties','required'=>false,'name'=>'tbl[other_warranties][]','class'=>'form-control','prettyName'=>'Other Warranties','multiple'=>'multiple','database'=>false),
				'convenience_features'=>array('type'=>'select','idlookup'=>'convenience_features','required'=>false,'name'=>'tbl[convenience_features][]','multiple'=>'multiple','database'=>false,'class'=>'form-control','prettyName'=>'Convenience Features'),
				'pu_van_suv_accessories'=>array('type'=>'select','idlookup'=>'pu_van_suv_accessories','required'=>false,'name'=>'tbl[pu_van_suv_accessories][]','multiple'=>'multiple','database'=>false,'class'=>'form-control','prettyName'=>'Accessories'),
				'aftermarket'=>array('type'=>'select','idlookup'=>'aftermarket','required'=>false,'name'=>'tbl[aftermarket][]','multiple'=>'multiple','database'=>false,'class'=>'form-control','prettyName'=>'Aftermarket'),
				'vehicleInfo'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'submit'=>array('type'=>'submitbutton','value'=>'CONTINUE TO STAGE 3','database'=>false,'class'=>'button','onclick'=>'vehicleSubmit(this);return false;')
			),
			'leaseInfo'=>array(
				'dealership'=>array('type'=>'textfield','required'=>true,'class'=>'form-control'),
				'dealership_contact'=>array('type'=>'textfield','required'=>true,'class'=>'form-control'),
				'lease_company'=>array('type'=>'textfield','required'=>true,'class'=>'form-control'),
				'lease_pre_tax'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'lease_taxes_inc'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'downpayment'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'buyout'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'km_allowance'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'km_overage'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'lease_term'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'lease_expiry'=>array('type'=>'datepicker','required'=>true,'validation'=>'date','class'=>'form-control def_field_datepicker'),
				'cash_incentive'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control','value'=>'0.00'),
				'recoup'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control','value'=>'0.00'),
				'downpayment'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'deposit'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'transfer_costs'=>array('type'=>'textfield','required'=>true,'validation'=>'number','class'=>'form-control'),
				'leaseInfo'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'image1'=>array('type'=>'fileupload','required'=>true,'prettyName'=>'Image 1'),
				'image2'=>array('type'=>'fileupload','required'=>false,'prettyName'=>'Image 2'),
				'image3'=>array('type'=>'fileupload','required'=>false,'prettyName'=>'Image 3'),
				'image4'=>array('type'=>'fileupload','required'=>false,'prettyName'=>'Image 4'),
				'image5'=>array('type'=>'fileupload','required'=>false,'prettyName'=>'Image 5'),
				'image6'=>array('type'=>'fileupload','required'=>false,'prettyName'=>'Image 6'),
				'image7'=>array('type'=>'fileupload','required'=>false,'prettyName'=>'Image 7'),
				'image8'=>array('type'=>'fileupload','required'=>false,'prettyName'=>'Image 8'),
				'image9'=>array('type'=>'fileupload','required'=>false,'prettyName'=>'Image 9'),
				'image10'=>array('type'=>'fileupload','required'=>false,'prettyName'=>'Image 10'),
				'teaser'=>array('type'=>'textarea','required'=>true,'prettyName'=>'Vehicle Description','class'=>'mceSimple'),
				'submit'=>array('type'=>'submitbutton','value'=>'SUBMIT FOR AN EVALUATION','database'=>false,'class'=>'button','onclick'=>'financialSubmit(this);return false;')
			),
			'getModels'=>array(
				'model'=>array('type'=>'select','required'=>true,'class'=>'form-control','onchange'=>'getYears(this);return false;'),
			),
			'getYears'=>array(
				'year'=>array('type'=>'select','required'=>true,'class'=>'form-control'),
			),
			'contactUs'=>array(
				'firstname'=>array('type'=>'textfield','required'=>true,'prettyName'=>'Name','class'=>'form-control input-large'),
				'email'=>array('type'=>'textfield','required'=>true,'validation'=>'email','class'=>'form-control input-large'),
				'phone'=>array('type'=>'textfield','required'=>false,'class'=>'form-control input-large'),
				'message'=>array('type'=>'textarea','required'=>true,'wrap'=>'virtual','class'=>'form-control','rows'=>8),
				'contactUs'=>array('type'=>'hidden','value'=>1),
				'r_id'=>array('type'=>'hidden','value'=>rand()),
				'captcha'=>array('type'=>'captcha','required'=>true,'prettyName'=>'Captcha Field','validation'=>'captcha'),
				'r_secret'=>array('type'=>'tag','value'=>0,'persist'=>true)
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
		$mailer->From = $emails[0]['email'];	//$order['email'];
		$mailer->FromName = $emails[0]['name'];	//$order['firstname'].' '.$order['lastname'];
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

	function postNewsletterSignup($memberId,$obj,$module) {
		$obj->logMessage(__FUNCTION__,sprintf('member id [%d]',$memberId),1);
	}

	function rssParse($data) {
		$this->logMessage(__FUNCTION__,sprintf('ct = [%s], results [%s] data [%s]',$ct,print_r($results,true),print_r($data,true)),1);
		return $data;
	}

	function initHook() {
		if (!array_key_exists('cart',$_SESSION))
			$cart = array('header'=>array());
		else
			$cart = $_SESSION['cart'];
	}

	function preRecalc($cart) {
		if (!$this->isLoggedIn()) return $cart;
		$this->logMessage(__FUNCTION__,sprintf('returned cart [%s]',print_r($cart,true)),2);
		return $cart;
	}

	function calcShipping($cart) {
		return $cart;
	}

	function initCart($cart) {
		return $cart;
	}

	function formatCart($cart) {
		return $cart;
	}

	function getModuleInfo() {
		return parent::getModuleList(array());
	}

}

?>
