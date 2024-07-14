<?php

class orders extends Backend {

	private $m_content = 'orders';
	private $m_perrow = 5;

	public function __construct() {
		$this->m_perrow = defined('GLOBAL_PER_PAGE') ? GLOBAL_PER_PAGE : 5;
		$this->M_DIR = 'backend/modules/orders/';
		$this->setTemplates(
			array(
				'main'=>$this->M_DIR.'orders.html',
				'articleList'=>$this->M_DIR.'forms/articleList.html',
				'addContent'=>$this->M_DIR.'forms/addContent.html',
				'showSearchForm'=>$this->M_DIR.'forms/searchForm.html',
				'editLine'=>$this->M_DIR.'forms/editLine.html',
				'orderLine'=>$this->M_DIR.'forms/orderLine.html',
				'addressForm'=>$this->M_DIR.'forms/addressForm.html',
				'addressList'=>$this->M_DIR.'forms/addressList.html',
				'editAddress'=>$this->M_DIR.'forms/editAddress.html',
				'editLineResult'=>$this->M_DIR.'forms/editLineResult.html',
				'addItem'=>$this->M_DIR.'forms/addItem.html',
				'showOrder'=>$this->M_DIR.'forms/showOrder.html',
				'header'=>$this->M_DIR.'forms/heading.html',
				'recurringInfo'=>$this->M_DIR.'forms/recurring.html',
				'recurringInfoRow'=>$this->M_DIR.'forms/recurringRow.html',
				'showRecurring'=>$this->M_DIR.'forms/showRecurring.html'
			)
		);
		$this->setFields(array(
			'header'=>array(),
			'addressForm'=>array(),
			'addressList'=>array(
				'line1'=>array('type'=>'tag','reformatting'=>true),
				'line2'=>array('type'=>'tag','reformatting'=>true),
				'city'=>array('type'=>'tag','reformatting'=>true),
			),
			'editAddress'=>array(
				'options'=>array('method'=>'post','action'=>'/modit/ajax/editAddress/orders'),
				'editAddress'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'addresstype'=>array('type'=>'select','required'=>true,'sql'=>'select id, value from code_lookups where type = "memberAddressTypes"','prettyName'=>'Address Type'),
				'ownertype'=>array('type'=>'hidden','value'=>'order'),
				'ownerid'=>array('type'=>'hidden','id'=>'ownerid'),
				'line1'=>array('type'=>'input','required'=>true,'prettyName'=>'Address Line 1'),
				'line2'=>array('type'=>'input','required'=>false),
				'city'=>array('type'=>'input','required'=>true,'prettyName'=>'City'),
				'country_id'=>array('type'=>'countryselect','required'=>true,'id'=>'country_id','prettyName'=>'Country'),
				'province_id'=>array('type'=>'provinceselect','required'=>true,'id'=>'province_id','prettyName'=>'Province'),
				'postalcode'=>array('type'=>'input','required'=>true,'prettyName'=>'Postal Code'),
				'phone1'=>array('type'=>'input'),
				'phone2'=>array('type'=>'input'),
				'fax'=>array('type'=>'input'),
				'addresses'=>array('type'=>'select','database'=>false,'id'=>'addressSelector'),
				'save'=>array('type'=>'submitbutton','database'=>false,'value'=>'Save Address')
			),
			'editLine'=>array(
				'options'=>array('name'=>'pFormEdit','database'=>false),
				'product_id'=>array('type'=>'select','required'=>true,'sql'=>'select id, concat(code," - ",name) from product where deleted = 0 order by code'),
				'coupon_id'=>array('type'=>'select','required'=>false,'sql'=>'select id, concat(code," - ",name) from coupons where deleted = 0 order by code','id'=>'editCouponId'),
				'quantity'=>array('type'=>'input','required'=>true,'validation'=>'number','prettyName'=>'Ordered'),
				'shipped'=>array('type'=>'input','required'=>false,'validation'=>'number','prettyName'=>'Shipped'),
				'deleted'=>array('type'=>'checkbox','value'=>1),
				'price'=>array('type'=>'input','required'=>true,'validation'=>'number','prettyName'=>'Price'),
				'discount_type'=>array('type'=>'select','required'=>false,'lookup'=>'discountTypes'),
				'discount_rate'=>array('type'=>'input','required'=>false,'validation'=>'number','prettyName'=>'Discount Rate'),
				'discount_value'=>array('type'=>'input','required'=>false,'validation'=>'number','prettyName'=>'Discount Amount'),

				'recurring_period'=>array('type'=>'select','name'=>'recurring_period','required'=>false,'sql'=>'select id,teaser from product_recurring where product_id = %%product_id%%'),
				'recurring_discount_type'=>array('type'=>'select','required'=>false,'lookup'=>'discountTypes'),
				'recurring_discount_rate'=>array('type'=>'input','required'=>false,'validation'=>'number','prettyName'=>'Recurring Discount Rate'),
				'recurring_discount_value'=>array('type'=>'input','required'=>false,'validation'=>'number','prettyName'=>'Recurring Discount Amount'),
				'recurring_qty'=>array('type'=>'input','required'=>false,'validation'=>'number','prettyName'=>'Recurring Quantity'),
				'qty_multiplier'=>array('type'=>'input','required'=>true,'value'=>1,'prettyName'=>'Options Multiplier'),
				'qty_multiplierHidden'=>array('type'=>'hidden','value'=>1,'database'=>false),

				'editLine'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'tempEdit'=>array('type'=>'hidden','value'=>0,'database'=>false),
				'fldName'=>array('type'=>'hidden','value'=>'','database'=>false),
				'order_id'=>array('type'=>'tag'),
				'order_date'=>array('type'=>'hidden','database'=>false),
				'submit'=>array('type'=>'submitbutton','value'=>'Save','database'=>false),
				'deleted'=>array('type'=>'checkbox','value'=>1),
				'shipping'=>array('type'=>'input','validation'=>'number'),
				'shipping_only'=>array('type'=>'hidden'),
				'value'=>array('type'=>'input','disabled'=>'disabled'),
				'inventory_id'=>array('type'=>'select','required'=>false),
				'options_id'=>array('type'=>'select','required'=>false),
				'color'=>array('type'=>'select','required'=>false),
				'size'=>array('type'=>'select','required'=>false)
			),
			'orderLine'=>array(),
			'articleList' => array(
				'id'=>array('type'=>'tag'),
				'order_date'=>array('type'=>'datetimestamp'),
				'amount'=>array('type'=>'tag'),
				'owing'=>array('type'=>'tag'),
				'deleted'=>array('type'=>'booleanIcon'),
				'options_id'=>array('type'=>'tag')
			),
			'addContent'=>array(
				'options'=>array('name'=>'addContent','action'=>'/modit/ajax/addContent/orders','database'=>false),
				'id'=>array('type'=>'tag','database'=>false),
				'member_id'=>array('type'=>'select','required'=>true,'sql'=>'select id, concat(lastname,", ",firstname) from members where id = %%member_id%% order by lastname, firstname'),
				'order_date'=>array('type'=>'datetimepicker','required'=>true,'AMPM'=>'AMPM','validation'=>'datetime','prettyName'=>'Order Date'),
				'coupon_id'=>array('type'=>'select','required'=>false,'sql'=>'select id, concat(code," - ",name) from coupons where deleted = 0 order by code','id'=>'editCouponId'),
				'value'=>array('type'=>'tag'),
				'handling'=>array('type'=>'textfield','required'=>true,'value'=>0.00,'validation'=>'number'),
				'authorization_info'=>array('type'=>'textarea','reformat'=>true,'class'=>'mceNoEditor'),
				'authorization_amount'=>array('type'=>'input','required'=>true,'validation'=>'number','prettyName'=>'Authorization Amount'),
				'authorization_amount_ro'=>array('type'=>'tag','required'=>true,'database'=>false),
				'authorization_code'=>array('type'=>'input','prettyName'=>'Authorization Code'),
				'authorization_transaction'=>array('type'=>'input','required'=>false,'prettyName'=>'Transaction Code'),
				'deleted'=>array('type'=>'checkbox','value'=>1),
				'submit'=>array('type'=>'submitbutton','value'=>'Save Order','database'=>false),
				'recalc'=>array('type'=>'submitbutton','value'=>'Save & Recalculate','database'=>false),
				'tempEdit'=>array('type'=>'hidden','value'=>0,'database'=>false),
				'fldName'=>array('type'=>'hidden','value'=>'','database'=>false),
				'discount_rate'=>array('type'=>'input','required'=>false,'validation'=>'number','readonly'=>'readonly','prettyName'=>'Discount Rate'),
				'discount_type'=>array('type'=>'tag'),
				'addContent'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'orderTotal'=>array('type'=>'tag','database'=>false),
				'ship_via'=>array('type'=>'select','sql'=>'SELECT id, value as name FROM code_lookups WHERE type = "ship_via" and extra like "%|1" ORDER BY sort, value ASC'),
				'ship_date'=>array('type'=>'datepicker'),
				'ship_tracking_code'=>array('type'=>'input'),
				'ship_comments'=>array('type'=>'textarea','class'=>'mceSimple'),
				'order_status'=>array('type'=>'select','multiple'=>true,'lookup'=>'orderStatus','database'=>false,'id'=>'order_status'),
				'currency_id'=>array('type'=>'select','required'=>false,'idlookup'=>'currencies'),
				'submitEmail'=>array('type'=>'checkbox','value'=>1,'name'=>'submitEmail','database'=>false)
			),
			'showSearchForm'=>array(
				'options'=>array('action'=>'showSearchForm','name'=>'searchForm','id'=>'search_form'),
				'order_status'=>array('type'=>'select','name'=>'order_status','lookup'=>'orderStatus'),
				'opt_created'=>array('type'=>'select','name'=>'opt_created','lookup'=>'search_options'),
				'created'=>array('type'=>'datepicker','required'=>false),
				'opt_name'=>array('type'=>'select','name'=>'opt_name','lookup'=>'search_string'),
				'name'=>array('type'=>'input','required'=>false),
				'opt_order_id'=>array('type'=>'select','lookup'=>'search_options'),
				'order_id'=>array('type'=>'input','required'=>false),
				'product'=>array('type'=>'select','required'=>false,'sql'=>'select id, concat(code," - ",name) from product where deleted = 0 order by code'),
				'status'=>array('type'=>'select','required'=>false,'lookup'=>'orderStatus'),
				'deleted'=>array('type'=>'select','lookup'=>'boolean'),
				'showSearchForm'=>array('type'=>'hidden','value'=>1),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'sortby'=>array('type'=>'hidden','value'=>'created'),
				'sortorder'=>array('type'=>'hidden','value'=>'desc'),
				'quicksearch'=>array('type'=>'input','name'=>'quicksearch','required'=>false),
				'opt_quicksearch'=>array('type'=>'hidden','value'=>'like'),
				'opt_owing'=>array('type'=>'select','lookup'=>'search_options'),
				'owing'=>array('type'=>'input','required'=>false,'validation'=>'number','prettyName'=>'Amount Owing'),
				'coupon'=>array('type'=>'select','required'=>false,'sql'=>'select id,concat(code," - ",name) from coupons where deleted = 0'),
				'opt_total'=>array('type'=>'select','lookup'=>'search_options'),
				'total'=>array('type'=>'input','required'=>false,'validation'=>'number','prettyName'=>'Total'),
				'pager'=>array('type'=>'select','required'=>true,'value'=>$this->m_perrow,'lookup'=>'paging','id'=>'pager'),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'perpage'=>array('type'=>'hidden','value'=>$this->m_perrow,'name'=>'pager'),
				'submit'=>array('type'=>'submitbutton','value'=>'Search')
			),
			'articleList' => array(
				'id'=>array('type'=>'tag'),
				'title'=>array('type'=>'tag'),
				'order_date'=>array('type'=>'datetimestamp'),
				'deleted'=>array('type'=>'booleanIcon')
			),
			'recurringInfo'=>array(
				'delayShipping'=>array('type'=>'checkbox','value'=>1,'name'=>'delayShipping'),
				'delayUntil'=>array('type'=>'datepicker','required'=>false),
				'recurringInfo'=>array('type'=>'hidden','value'=>1),
				'submit'=>array('type'=>'submitbutton','value'=>'Put Order On Hold')
			),
			'recurringInfoRow' => array(
				'billing_date'=>array('type'=>'datestamp','mask'=>'d-M-Y'),
				'billed'=>array('type'=>'booleanIcon'),
				'billed_on'=>array('type'=>'datestamp')
			),
			'showRecurring' => array(
				'billing_date'=>array('type'=>'datestamp','mask'=>'d-M-Y'),
				'billed_on'=>array('type'=>'datestamp'),
				'authorization_info'=>array('type'=>'textarea')
			)
		));
	
		parent::__construct ();
	}
	
	function __destruct() {
	
	}

	function show($injector = null) {
		$this->logMessage('show',sprintf('injector [%s]',$injector),2);
		$form = new Forms();
		$form->init($this->getTemplate('main'),array('name'=>'adminMenu'));
		$frmFields = $form->buildForm($this->getFields('main'));
		if ($injector == null || strlen($injector) == 0) {
			$injector = $this->moduleStatus(true);
		}
		$form->addTag('injector', $injector, false);
		return $form->show();
	}

	function showContentTree() {
		return "";
	}

	function showPageContent($fromMain = false) {
		$o_id = array_key_exists('o_id',$_REQUEST) ? $_REQUEST['o_id'] : 0;
		$form = new Forms();
		if ($o_id > 0 && $data = $this->fetchSingle(sprintf('select * from %s where id = %d',$this->m_content,$o_id))) {
			$form->init($this->getTemplate('showFolderContent'),array('name'=>'showFolderContent'));
			$frmFields = $form->buildForm($this->getFields('showFolderContent'));
			if (array_key_exists('pagenum',$_REQUEST)) 
				$pageNum = $_REQUEST['pagenum'];
			else
				$pageNum = 1;	// no 0 based calcs
			if ($pageNum <= 0) $pageNum = 1;
			$perPage = $this->m_perrow;
			if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
			$count = $this->fetchScalar(sprintf('select count(n.id) from %s n where deleted = 0', $this->m_content));
			$pagination = $this->pagination($count, $perPage, $pageNum, 
				array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
				'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'),
				array('url'=>'/modit/ajax/showFolderContent/orders','destination'=>'middleContent'));
			$start = ($pageNum-1)*$perPage;
			$sortby = 'id';
			$sortorder = 'desc';
			if (count($_POST) > 0 && array_key_exists('showFolderContent',$_POST)) {
				$sortby = $_POST['sortby'];
				if ($sortby == 'name') $sortby = 'm.lastname';
				$sortorder = $_POST['sortorder'];
				$form->addData($_POST);
			}
			$sql = sprintf('select a.*,m.firstname,m.lastname from %s a where a.id = %d order by %s %s limit %d,%d',  $this->m_content, $_REQUEST['o_id'],$sortby, $sortorder, $start,$perPage);
			$orders = $this->fetchAll($sql);
			$this->logMessage('showPageContent', sprintf('sql [%s], records [%d]',$sql, count($orders)), 2);
			$articles = array();
			$frm = new Forms();
			$frm->init($this->getTemplate('articleList'),array());
			$tmp = $frm->buildForm($this->getFields('articleList'));
			$status = $this->fetchAll(sprintf('select * from code_lookups where type="orderStatus" order by sort, code'));
			foreach($orders as $order) {
				$order['owing'] = money_format('%(#n',$order['total']-$order['authorization_amount']);
				$order['value'] = money_format('%(#n',$order['value']);
				$order['total'] = '['.money_format('%(#n',$order['total']).']';
				$order['taxes'] = money_format('%(#n',$order['taxes']);
				$order['discount_value'] = money_format('%(#n',$order['discount_value']);
				$order['line_discounts'] = money_format('%(#n',$order['line_discounts']);
				$this->logMessage("showSearchForm",sprintf("detail line form [%s]",print_r($frm,true)),2);
				$tmp = array();
				foreach($status as $key=>$value) {
					if ($order['order_status'] & (int)$value['code'])
						$tmp[] = $value['value'];
				}
				$order['order_status'] = implode(', ',$tmp);
				$frm->addData($order);
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
			if (array_key_exists('formData',$_SESSION) && array_key_exists('orderSearchForm', $_SESSION['formData']))
				$_POST = $_SESSION['formData']['orderSearchForm'];
			else
				$_POST = array('deleted'=>0,'sortby'=>'created','sortorder'=>'desc','showSearchForm'=>1);
		if (count($_POST) > 0 && array_key_exists('showSearchForm',$_POST)) {
			$form->addData($_POST);
			if ($form->validate()) {
				$_SESSION['formData']['orderSearchForm'] = $form->getAllData();
				$srch = array();
				$quick = false;
				foreach($frmFields as $key=>$value) {
					if ($quick)
						break;
					switch($key) {
						case 'quicksearch':
							if (array_key_exists('opt_quicksearch',$_POST) && $_POST['opt_quicksearch'] != null && $value = $form->getData($key)) {
								if ($_POST['opt_quicksearch'] == 'like' && strpos($value,'%',0) === false) {
									$value = '%'.$value.'%';
								}
								$srch = array();
								$tmp = array();
								$quick = true;
								$tmp[] = sprintf(' concat(firstname," ",lastname) %s "%s"',$_POST['opt_quicksearch'],$this->escape_string($value));
								$tmp[] = sprintf(' o.id %s "%s"',$_POST['opt_quicksearch'],$this->escape_string($value));
								$srch[] = sprintf('(%s)',implode(' or ',$tmp));
								$frmFields = array();
							}
							break;
						case 'order_id':
							//
							//	normally it would be just id but the form has a hidden field id as well for paging
							//
							if (array_key_exists('opt_'.$key,$_POST) && $_POST['opt_'.$key] != null && !is_null($value = $form->getData($key))) {
								if ($_POST['opt_'.$key] == 'like' && strpos($value,'%',0) === false) {
									$value = '%'.$value.'%';
								}
								$srch[] = sprintf(' o.id %s %s',$_POST['opt_'.$key],$this->escape_string($value));
							}
							break;
						case 'total':
							if (array_key_exists('opt_'.$key,$_POST) && $_POST['opt_'.$key] != null && !is_null($value = $form->getData($key))) {
								if ($_POST['opt_'.$key] == 'like' && strpos($value,'%',0) === false) {
									$value = '%'.$value.'%';
								}
								$srch[] = sprintf(' o.%s %s %s',$key,$_POST['opt_'.$key],$this->escape_string($value));
							}
							break;
						case 'owing':
							if (array_key_exists('opt_'.$key,$_POST) && $_POST['opt_'.$key] != null && !is_null($value = $form->getData($key))) {
								if ($_POST['opt_'.$key] == 'like' && strpos($value,'%',0) === false) {
									$value = '%'.$value.'%';
								}
								$srch[] = sprintf(' (total - authorization_amount) %s %s',$_POST['opt_'.$key],$this->escape_string($value));
							}
							break;
						case 'name':
							if (array_key_exists('name',$_POST) && strlen($_POST['name']) > 0)
								$srch[] = sprintf("(m.firstname like '%%%s%%' or m.lastname like '%%%s%%')",$_POST['name'],$_POST['name']);
							break;
						case 'product':
							if (array_key_exists('product',$_POST) && $_POST['name'] > 0)
								$srch[] = sprintf("o.id in (select order_id from order_lines where product_id = %d)",$_POST['product']);
							break;
						case 'coupon':
							if (array_key_exists('coupon',$_POST) && $_POST['coupon'] > 0)
								$srch[] = sprintf("o.coupon_id = %d",$_POST['coupon']);
							break;
						case 'status':
							if (array_key_exists('status',$_POST) && $_POST['status'] > 0)
								$srch[] = sprintf("o.status = %d",$_POST['status'],$_POST['status']);
							break;
						case 'created':
							if (array_key_exists('opt_'.$key,$_POST) && $_POST['opt_'.$key] != null && $value = $form->getData($key)) {
								if ($_POST['opt_'.$key] == 'like') {
									$this->addError('Like is not supported for dates');
								}
								else
									$srch[] = sprintf(' date_format(o.%s,"%%Y-%%m-%%d") %s "%s"',$key, $_POST['opt_'.$key],date("Y-m-d",strtotime($this->escape_string($value))));
							}
							break;
						case 'deleted':
							if (!is_null($value = $form->getData($key)))
								if (strlen($value) > 0)
									$srch[] = sprintf(' o.%s = %s',$key,$this->escape_string($value));
							break;
						case 'order_status':
						$this->logMessage('showSearchForm',sprintf('value is [%s] post [%s]',print_r($value,true),print_r($frmFields,true)),1);

							if (($value = $form->getData($key)) > 0) {
								$srch[] = sprintf(' ((o.order_status & %d) = %d)',$value,$value);
							}
							break;
						default:
							break;
					}
				}
				$this->logMessage("showSearchForm",sprintf("srch [%s]",print_r($srch,true)),3);
				if (count($srch) > 0) {
					if (array_key_exists('pagenum',$_REQUEST))
						$pageNum = $_REQUEST['pagenum'];
					else
						$pageNum = 1;	// no 0 based calcs
					$perPage = $this->m_perrow;
					if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
					$count = $this->fetchScalar(sprintf('select count(o.id) from %s o,members m where m.id = o.member_id and %s', $this->m_content, implode(' and ',$srch)));
					$pageNum = max(1,min($pageNum, (floor(($count-1)/$perPage)+1)));
					$this->logMessage("showSearchForm",sprintf("pagenum is [$pageNum]"),2);
					$form->setData('pagenum', $pageNum);
					$pagination = $this->pagination($count, $perPage, $pageNum,
							array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
							'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'),
							array('url'=>'/modit/ajax/showSearchForm/orders','destination'=>'middleContent')
						);
					$start = ($pageNum-1)*$perPage;
					$sortorder = 'desc';
					$sortby = 'created';
					if (array_key_exists('sortby',$_POST)) {
						$sortby = $_POST['sortby'];
						if ($sortby == 'name') $sortby = 'm.lastname';
						$sortorder = $_POST['sortorder'];
					}
					$this->logMessage("showSearchForm",sprintf("post [%s] srch [%s]",print_r($_POST,true),print_r($srch,true)),2);
					$sql = sprintf('select o.*, m.firstname, m.lastname from %s o, members m where m.id = o.member_id and %s order by %s %s limit %d,%d',
						 $this->m_content, implode(' and ',$srch),$sortby, $sortorder, $start,$perPage);
					$recs = $this->fetchAll($sql);
					$this->logMessage('showSearchForm', sprintf('sql [%s] records [%d]',$sql,count($recs)), 2);
					$articles = array();
					$frm = new Forms();
					$frm->init($this->getTemplate('articleList'),array());
					$tmp = $frm->buildForm($this->getFields('articleList'));
					$status = $this->fetchAll(sprintf('select * from code_lookups where type="orderStatus" order by sort, code'));
					foreach($recs as $article) {
						$article['owing'] = money_format('%(#10n',$article['total']-$article['authorization_amount']);
						$article['value'] = money_format('%(#10n',$article['value']);
						$article['total'] = money_format('%(#10n',$article['total']);
						$article['taxes'] = money_format('%(#10n',$article['taxes']);
						$article['discount_value'] = money_format('%(#10n',$article['discount_value']);
						$article['line_discounts'] = money_format('%(#10n',$article['line_discounts']);
						$this->logMessage("showSearchForm",sprintf("detail line form [%s]",print_r($frm,true)),2);
						$tmp = array();
						foreach($status as $key=>$value) {
							if ($article['order_status'] & (int)$value['code'])
								$tmp[] = $value['value'];
						}
						$article['order_status'] = implode(', ',$tmp);
						$this->logMessage("showSearchForm",sprintf("detail line form [%s]",print_r($frm,true)),2);
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


	function addContent($fromMain = false) {
		$form = new Forms();
		$form->init($this->getTemplate('addContent'));
		$frmFields = $this->getFields('addContent');
		if (!(array_key_exists('o_id',$_REQUEST) && $_REQUEST['o_id'] > 0 && $data = $this->fetchSingle(sprintf('select * from %s where id = %d', $this->m_content, $_REQUEST['o_id'])))) {
			$data = array('id'=>0,'published'=>false,'tax_exemptions'=>'||','value'=>0,'authorization_amount'=>0,'coupon_id'=>0,'discount_type'=>'','order_status'=>0,'currency_id'=>0); 
			$frmFields['coupon_id']['sql'] = 'select id, concat(code," - ",name) from coupons where deleted = 0 and enabled = 1 and published = 1 order by code';
		} else {
			$frmFields['coupon_id']['sql'] = sprintf('select id, concat(code," - ",name) from coupons where (deleted = 0 and enabled = 1 and published = 1) or id = %d order by code',$data['coupon_id']);
		}
		$data['currency_code'] = $this->fetchScalar(sprintf("select value from code_lookups where id = %d",$data['currency_id']));
		$lines = $this->fetchAll(sprintf('select o.*, p.code, p.name, c.name as coupon_name, po.teaser from order_lines o left join coupons c on c.id = o.coupon_id left join product_options po on o.options_id = po.id, product p where o.order_id = %d and p.id = o.product_id and o.deleted = 0 order by line_id',$data['id']));
		$details = array();
		$dtlForm = new Forms();
		$dtlForm->init($this->getTemplate('orderLine'));
		$dtlFields = $dtlForm->buildForm($this->getFields('orderLine'));
		foreach($lines as $line) {
			$line['disc_dollar'] = $line['discount_type'] == 'D' ? '$':'';
			$line['disc_percent'] = $line['discount_type'] == 'P' ? '%':'';
			$dtlForm->addData($line);
			$details[] = $dtlForm->show();
		}
		$data['products'] = implode('',$details);
		$data['discount_dollar'] = '';
		$data['discount_percent'] = '';
		switch ($data['discount_type']) {
			case 'P':
				$data['discount_percent'] = '%';
				break;
			case 'D';
				$data['discount_dollar'] = '$';
				break;
			default:
			break;
		}
		$data['authorization_amount_ro'] = $data['authorization_amount'];
		$status = $this->fetchAll(sprintf('select * from code_lookups where type="orderStatus" order by sort, code'));
		$tmp = array();
		$form->addTag('hasRecurring',$data['order_status'] & STATUS_RECURRING);
		foreach($status as $key=>$value) {
			if ($data['order_status'] & (int)$value['code'])
				$tmp[] = $value['code'];
		}
		$data['order_status'] = $tmp;
		$form->addData($data);
		$form->addTag('addressForm',$this->loadAddresses($data['id']),false);
		$form->addTag('recurringInfo',$this->getRecurring($data['id']),false);
		$customFields = new custom();
		if (method_exists($customFields,'orderDisplay')) {
			$custom = $customFields->orderDisplay();
			$form->addTag('customTab',sprintf('<li><a href="#tabs-custom">%s</a></li>',$custom['description']),false);
			$html = $form->getHTML();
			$html = str_replace('%%customInfo%%',$custom['form'],$html);
			$form->setHTML($html);
			$frmFields = array_merge($frmFields,$custom['fields']);
		}
		$frmFields = $form->buildForm($frmFields);

		$status = 'false';	//assume it failed
		if (count($_POST) > 0 && array_key_exists('addContent',$_POST)) {
			$form->addData($_POST);
			$status = $form->validate();
			if ($status) {
				if ($_POST['tempEdit'] == 1) {
					switch($_POST['fldName']) {
						case 'recurring_period':
							if ($rp = $this->fetchSingle(sprintf('select * from product_recurring where id = %d',$_POST['recurring_period']))) {
								$form->setData('recurring_discount_rate',$rp['amount']);
								$form->setData('recurring_discount_type',$rp['percent_or_dollar']);
							}
							else {
								$form->setData('recurring_discount_rate',0);
								$form->setData('recurring_discount_type','');
							}
							$form->setData('recurring_discount_dollar','');
							$form->setData('recurring_discount_percent','');
							switch ($rp['percent_or_dollar']) {
								case 'P':
									$form->setData('recurring_discount_percent','%');
									break;
								case 'D';
									$form->setData('recurring_discount_dollar','$');
									break;
								default:
								break;
							}
							$this->logMessage(__FUNCTION__,sprintf('updated recurring from [%s]',print_r($rp,true)),1);
							break;
						case 'coupon_id':
							if ($cp = $this->fetchSingle(sprintf('select * from coupons where id = %d',$_POST['coupon_id']))) {
								$form->setData('discount_rate',$cp['amount']);
								$form->setData('discount_type',$cp['percent_or_dollar']);
							}
							else {
								$form->setData('discount_rate',0);
								$form->setData('discount_type','');
							}
							$form->setData('discount_dollar','');
							$form->setData('discount_percent','');
							switch ($cp['percent_or_dollar']) {
								case 'P':
									$form->setData('discount_percent','%');
									break;
								case 'D';
									$form->setData('discount_dollar','$');
									break;
								default:
								break;
							}
							break;
						default:
							break;
					}
					$this->logMessage("accContent",sprintf("calling recalcOrder from tempEdit"),2);
					//$tmp = $this->recalcOrder($data['id'],false);
					$tmp = Ecom::initCart();
$this->logMessage(__FUNCTION__,sprintf("cart pre get [%s}", print_r($tmp,true)),1);
					$tmp['header'] = array_merge($tmp['header'],$form->getAllData());
$this->logMessage(__FUNCTION__,sprintf("cart post get [%s}", print_r($tmp,true)),1);
					$tmp['products'] = $this->fetchAll(sprintf('select * from order_lines where order_id = %d and deleted = 0',$data['id']));
					$tmp['taxes'] = $this->fetchAll(sprintf('select * from order_taxes ot where ot.order_id = %d and ot.line_id in (select o.line_id from order_lines o where o.order_id = ot.order_id and o.deleted = 0)',$data['id']));

		if (!$address = $this->fetchSingle(sprintf('select * from addresses where ownerid = %d and ownertype = "order" and tax_address=1',$data['id'])))
			$address = array('id'=>0,'province_id'=>0,'country_id'=>0);
		$tmp['addresses']['shipping'] = $address;
		if (!$address = $this->fetchSingle(sprintf('select * from addresses where ownerid = %d and ownertype = "order" and tax_address=0',$data['id'])))
			$address = array('id'=>0,'province_id'=>0,'country_id'=>0);
		$tmp['addresses']['billing'] = $address;

					$tmp = Ecom::recalcOrder($tmp);
					$form->addData($tmp['header']);
					return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
				}
				$id = $_POST['o_id'];
				unset($frmFields['o_id']);
				unset($frmFields['options']);
				foreach($frmFields as $key=>$fld) {
					if (!(array_key_exists('database',$fld) && $fld['database'] == false)) {
						$flds[$key] = $form->getData($fld['name']);
					}
				}
				$tmp = 0;
				foreach($_REQUEST['order_status'] as $key=>$value) {
					$tmp |= $value;
				}
				if (($tmp & STATUS_RECURRING) == STATUS_RECURRING) {
					$this->logMessage(__FUNCTION__,sprintf("setting auth amount to [%s]",$form->getData('total')),1);
					$flds['authorization_amount'] = $form->getData('total');
				}
				$flds['order_status'] = $tmp;
				if ($data['id'] == 0) {
					$flds['created'] = date(DATE_ATOM);
					$stmt = $this->prepare(sprintf('insert into %s(%s) values(%s)',$this->m_content,implode(',',array_keys($flds)),str_repeat('?, ',count($flds)-1).'?'));
				}
				else 
					$stmt = $this->prepare(sprintf('update %s set %s where id = %d',$this->m_content,implode('=?, ',array_keys($flds)).'=?',$data['id']));
				$this->logMessage("addContent",sprintf("data array before update [%s]",print_r($data,true)),2);
				$stmt->bindParams(array_merge(array(str_repeat('s', count($flds))),array_values($flds)));
				$this->beginTransaction();
				if ($status = $stmt->execute()) {
					if ($data['id'] == 0) {
						$id = $this->insertId();
						$addresses = $this->fetchAll(sprintf('select * from addresses where ownertype="member" and ownerid = %d and deleted = 0',$form->getData('member_id')));
						foreach($addresses as $key=>$address) {
							$address['ownertype'] = 'order';
							$address['ownerid'] = $id;
							unset($address['id']);
							$stmt = $this->prepare(sprintf('insert into addresses(%s) values(%s)',implode(',',array_keys($address)),str_repeat('?,',count($address)-1).'?'));
							$stmt->bindParams(array_merge(array(str_repeat('s', count($address))),array_values($address)));
							$status = $status && $stmt->execute();
						}
					}
					else $id = $data['id'];
					$this->logMessage(__FUNCTION__,sprintf("calling recalcOrder from save"),2);
					$tmp = $this->recalcOrder($id,true);
					$form->addData($tmp['header']);
					//
					//	copy any addresses associated with this customer as well
					//
					$this->execute(sprintf('delete from order_taxes where order_id = %d and line_id = 0',$id));
					foreach($tmp['taxes'] as $key=>$tax) {
						$stmt = $this->prepare('insert into order_taxes(order_id,line_id,tax_id,tax_amount) values(?,?,?,?)');
						$stmt->bindParams(array('iiid',$id,0,$key,$tax['tax_amount']));
						$status = $status && $stmt->execute();
					}
					if ($status) {
						$this->commitTransaction();
						if ($data['id'] == 0) 
							$form->addTag('id',$id);
						$this->addMessage('Record Updated');
						$data = $this->fetchSingle(sprintf('select * from %s where id = %d', $this->m_content, $id));
						$data['discount_dollar'] = '';
						$data['discount_percent'] = '';
						switch ($data['discount_type']) {
							case 'P':
								$data['discount_percent'] = '%';
								break;
							case 'D';
								$data['discount_dollar'] = '$';
								break;
							default:
							break;
						}
						$data['authorization_amount_ro'] = $data['authorization_amount'];
						$status = $this->fetchAll(sprintf('select * from code_lookups where type="orderStatus" order by sort, code'));
						$tmp = array();
						foreach($status as $key=>$value) {
							if ($data['order_status'] & (int)$value['code'])
								$tmp[] = $value['code'];
						}
						$data['order_status'] = $tmp;
						$form->addData($data);
						if (array_key_exists('submitEmail',$_POST) && $_POST['submitEmail'] == 1) {
							$emails = $this->configEmails("ecommerce");
							if (count($emails) == 0)
								$emails = $this->configEmails("contact");
							$mailer = new MyMailer();
							$mailer->Subject = sprintf("Order Status Update - %s", SITENAME);
							$body = new Forms();
							$body->setOption('formDelimiter','{{|}}');
							$html = $this->getHtmlForm('orderStatus');
							$o_fields = $body->buildForm($this->getFields('orderStatus'));
							$body->setHTML($html);
							$order = $this->fetchSingle(sprintf('select o.*, m.firstname, m.lastname, m.email from orders o, members m where o.id = %d and m.id = o.member_id',$id));
							$body->addData($this->formatOrder($order));
							$mailer->Body = $body->show();
							$mailer->From = $emails[0]['email'];
							$mailer->FromName = $emails[0]['name'];
							$this->logMessage('addContent',sprintf("mailer object [%s]",print_r($mailer,true)),1);
							$mailer->IsHTML(true);	
							$mailer->addAddress($order['email'],$order['firstname'].' '.$order['lastname']);
							if (!$mailer->Send()) {
								$this->addMessage('There was an error sending the email');
								$this->logMessage('addContent',sprintf("Email send failed [%s]",print_r($mailer,true)),1,true);
							}
							else
								$this->addMessage('Email has been sent');
						}
					}
					else {
						$this->rollbackTransaction();
						$this->addError('An Error occurred');
					}
				} else {
					$this->rollbackTransaction();
					$this->addError('An Error occurred');
				}
				$form->addTag('ecomErrors',$this->showEcomMessages(),false);
				return $this->ajaxReturn(array('status' => $status,'html' => $form->show()));
			}
			else {
				$this->addError('Form Validation Failed');
			}
			$form->addTag('errorMessage',$this->showMessages(),false);
			$form->addTag('ecomErrors',$this->showEcomMessages(),false);
		}
		if ($this->isAjax()) {
			$tmp = $form->show();
			$this->logMessage("addContent",sprintf("return form [%s]",print_r($form,true)),2);
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
	
	function deleteArticle() {
		if (array_key_exists('j_id',$_REQUEST)) {
			$id = $_REQUEST['j_id'];
			$curr = $this->fetchScalar(sprintf('select * from %s where id = %d',$this->m_content,$id));
			$this->logMessage('deleteArticle', sprintf('deleting order %d',$id), 2);
			$this->beginTransaction();
			$this->execute(sprintf('update %s set deleted = 1 where id = %d',$this->m_content,$curr));
			$this->commitTransaction();
			return $this->ajaxReturn(array('status'=>'true'));
		}
	}

	function edit() {
		if (array_key_exists('e_id',$_REQUEST) && $data = $this->fetchSingle(sprintf('select * from %s where id = %d',$this->m_content,$_REQUEST['e_id']))) {
			$f = new Forms();
			$f->init($this->getTemplate('editItem'));
			$f->addData($data);
			return $this->show($f->show());
		}
		else
			return $this->show();
	}

	function editLine() {
		$flds = $this->getFields('editLine');
		if (!(array_key_exists('l_id',$_REQUEST) && $data = $this->fetchSingle(sprintf('select * from order_lines where id = %d',$_REQUEST['l_id'])))) {
			$data = array('id'=>0,'product_id'=>0,'coupon_id'=>0,'value'=>0,'quantity'=>0,'order_id'=>$_REQUEST['order_id'],'options_id'=>0,'recurring_shipping_only'=>0);
			$flds['coupon_id']['sql'] = 'select id, concat(code," - ",name) from coupons where deleted = 0 and enabled = 1 and published = 1 order by code';
		} else {
			$flds['coupon_id']['sql'] = sprintf('select id, concat(code," - ",name) from coupons where (deleted = 0 and enabled = 1 and published = 1) or id = %d order by code',$data['coupon_id']);
		}
		$order = $this->fetchSingle(sprintf('select * from orders where id = %d',$_REQUEST['order_id']));
		$data['order_date'] = $order['order_date'];
		$form = new Forms();
		$form->init($this->getTemplate('editLine'));
		$flds['inventory_id']['sql'] = sprintf('select id, concat(start_date," - ",end_date," : ",quantity) from product_inventory where product_id = %d and (end_date >= curdate() or end_date = "0000-00-00") order by start_date',$data['product_id']);

		$customFields = new custom();
		if (method_exists($customFields,'orderLineDisplay')) {
			$custom = $customFields->orderLineDisplay();
			$form->addTag('customTab',sprintf('<li><a href="#tabs-custom">%s</a></li>',$custom['description']),false);
			$html = $form->getHTML();
			$html = str_replace('%%customInfo%%',$custom['form'],$html);
			$form->setHTML($html);
			$flds = array_merge($flds,$custom['fields']);
		}

		$flds = $form->buildForm($flds);
		$this->logMessage('editLine',sprintf('load options for product [%s]',$data['product_id']),1);
		$form->getField('options_id')->addAttribute("sql",sprintf('select id,teaser from product_options where product_id = %d and deleted = 0',$data['product_id']));
		$form->getField('color')->addAttribute("sql",sprintf('select l.id, l.value from product_options_info o, code_lookups l where o.options_id = %d and l.id = o.type_id and o.options_type="color" order by l.code',$data['options_id']));
		$form->getField('size')->addAttribute("sql",sprintf('select l.id, l.value from product_options_info o, code_lookups l where o.options_id = %d and l.id = o.type_id and o.options_type="size" order by l.code',$data['options_id']));
		if ($form->getField('options_id')->hasOptions()) $form->getField('options_id')->addAttribute('required',true);
		$form->addData($data);
		if (array_key_exists('editLine',$_REQUEST) && count($_POST) > 0) {
			$form->addData($_POST);
			$form->getField('options_id')->addAttribute("sql",sprintf('select id,teaser from product_options where product_id = %d and deleted = 0',$_POST['product_id']));
			$form->getField('color')->addAttribute("sql",sprintf('select l.id, l.value from product_options_info o, code_lookups l where o.options_id = %d and l.id = o.type_id and o.options_type="color" order by l.code',$_POST['options_id']));
			$form->getField('size')->addAttribute("sql",sprintf('select l.id, l.value from product_options_info o, code_lookups l where o.options_id = %d and l.id = o.type_id and o.options_type="size" order by l.code',$_POST['options_id']));
			if ($form->getField('options_id')->hasOptions()) $form->getField('options_id')->addAttribute('required',true);
			$form->getField('inventory_id')->addAttribute('sql',sprintf('select id, concat(start_date," - ",end_date," : ",quantity) from product_inventory where product_id = %d and (end_date >= curdate() or end_date = "0000-00-00") order by start_date',$_POST['product_id']));
			if ($_POST['tempEdit']) {
				//
				//	something changed
				//
				$status = true;
				switch($_POST['fldName']) {
					case 'recurring_period':
						if ($rp = $this->fetchSingle(sprintf('select * from product_recurring where id = %d',$_POST['recurring_period']))) {
							$_POST['recurring_discount_rate'] = $rp['discount_rate'];
							$_POST['recurring_discount_type'] = $rp['percent_or_dollar'];
						}
						else {
							$_POST['recurring_discount_rate'] = 0;
							$_POST['recurring_discount_type'] = '';
						}
						$this->logMessage(__FUNCTION__,sprintf('updated recurring from [%s]',print_r($rp,true)),1);
						break;
					case 'options_id':
						$option = $this->fetchSingle(sprintf('select * from product_options where id = %d',$_POST['options_id']));
						if ($p = Ecom::getPricing($_POST['product_id'],$_POST['quantity'],$_POST['order_date'])) {
							$_POST['price'] = $p['price'];
							$_POST['shipping'] = $p['shipping'];
						}
						$_POST['price'] += $option['price'];
						$_POST['shipping'] += $option['shipping'];
						$_POST['qty_multiplier'] = $option['qty_multiplier'];
						break;
					case 'coupon_id':
						$sql = sprintf('select * from coupons where id = %d',$_POST['coupon_id']);
						$c = $this->fetchSingle($sql);
						$_POST['discount_type'] = $c['percent_or_dollar'];
						$_POST['discount_rate'] = $c['amount'];
						$_POST['shipping_only'] = $c['shipping_only'];
						$this->logMessage("editLine",sprintf("coupon override sql [$sql] [%s]",print_r($c,true)),2);
						break;
					case 'product_id':
						$_POST['options_id'] = 0;
						if ($p = Ecom::getPricing($_POST['product_id'],$_POST['quantity'],$_POST['order_date'])) {
							$_POST['price'] = $p['price'];
							$_POST['shipping'] = $p['shipping'];
							//
							//	user hasn't had chance to select option yet - default to the 1st one
							//
							if ($form->getField('options_id')->hasOptions()) {
								$opt = $form->getField('options_id')->getOptions();
								$tmp = array_keys($opt);
								$_POST['options_id'] = $tmp[0];
								$this->logMessage('editLine',sprintf('options returned [%s]',print_r($opt,true)),1);
								if ($option = $this->fetchSingle(sprintf('select * from product_options where id = %d',$_POST['options_id']))) {
									$_POST['price'] += $option['price'];
									$_POST['shipping'] += $option['shipping'];
								}
							}
						} else {
							$this->addMessage('No pricing found for this quantity');
							$status = false;
						}
						$this->logMessage("editLine",sprintf("product override"),2);
					case 'quantity':
						//$sql = sprintf('select * from product_pricing where min_quantity <= %d and max_quantity >= %d and product_id = %d',$_POST['quantity'],$_POST['quantity'],$_POST['product_id']);
						//if ($p = $this->fetchSingle($sql)) {
						if ($p = Ecom::getPricing($_POST['product_id'],$_POST['quantity'],$_POST['order_date'])) {
							$_POST['price'] = $p['price'];
							$_POST['shipping'] = $p['shipping'];	//*$_POST['quantity'];
							if ($option = $this->fetchSingle(sprintf('select * from product_options where id = %d',$_POST['options_id']))) {
								$_POST['price'] += $option['price'];
								$_POST['shipping'] += $option['shipping'];
							}
						} else {
							$this->addMessage('No pricing found for this quantity');
							$status = false;
						}
						$this->logMessage("editLine",sprintf("price override [%s]",print_r($p,true)),2);
						break;
					default:
						break;
				}
				$form->addData($_POST);
				$tmp = Ecom::lineValue($form->getAllData());
				$form->addData($tmp);
				if (!$status) {
					return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
				}
			}
			else {
				if ($form->validate()) {
					$values = array();
					$tmp = Ecom::lineValue($form->getAllData());
					$form->addData($tmp);
					foreach($flds as $key=>$fld) {
						if (!(array_key_exists('database',$fld) && $fld['database'] == false))
							$values[$key] = $form->getData($fld['name']);
					}
					$values['total'] = $tmp['total'];
					$values['taxes'] = $tmp['taxes'];
					$prod = $this->fetchSingle(sprintf('select * from product where id = %d',$values['product_id']));
					$values['tax_exemptions'] = $prod['tax_exemptions'];
					if ($data['id'] != 0) {
						$stmt = $this->prepare(sprintf('update order_lines set %s where id = %d',implode('=?,',array_keys($values)).'=?',$data['id']));
					}
					else {
						$seq = $this->fetchScalar(sprintf('select max(line_id) from order_lines where order_id = %d',$values['order_id']));
						$values['line_id'] = $seq+1;
						$data['line_id'] = $values['line_id']; 
						$stmt = $this->prepare(sprintf('insert into order_lines(%s) values(%s)',implode(',',array_keys($values)),str_repeat('?,',count($values)-1).'?'));
					}
					$stmt->bindParams(array_merge(array(str_repeat('s', count($values))),array_values($values)));
					$this->logMessage("editLine",sprintf("values [%s] prod [%s]",print_r($values,true),print_r($prod,true)),2);
					$this->beginTransaction();
					$status = true;
					if ($data['id'] > 0) {
						//
						//	check for product/qty changes & adjust as appropriate
						//
						$oline = $this->fetchSingle(sprintf('select * from order_lines where id = %d',$data['id']));
						if ($values['product_id'] == $oline['product_id'] && $values['inventory_id'] == $oline['inventory_id'] && $values['quantity'] != $oline['quantity']) {
							//
							//	just a qty change
							//
							$status = $status && Inventory::updateInventory($values['inventory_id'],$data['quantity'] - $values['quantity'],$data['order_id'],'Line edited');
						} else {
							if ($values['product_id'] != $oline['product_id'] || $values['inventory_id'] != $oline['inventory_id'] || $values['quantity'] != $oline['quantity']) {
								$status = $status && Inventory::updateInventory($data['inventory_id'],$data['quantity'],$data['order_id'],'Product removed');
								$status = $status && Inventory::updateInventory($values['inventory_id'],-$values['quantity'],$data['order_id'],'Product added');
							}
						}
					}
					else {
						$status = $status && Inventory::updateInventory($values['inventory_id'],-$values['quantity'],$data['order_id'],'Product added');
					}
					$status = $status && $stmt->execute();
					if ($status) {
						if ($data['id'] == 0) {
							$data['id'] = $this->insertId();
						}
						$sql = sprintf('delete from order_taxes where order_id = %d and line_id = %d',$data['order_id'],$data['line_id']);
						$this->execute($sql);
						$this->logMessage("editLine",sprintf("order_taxes sql [$sql] data [%s]",print_r($data,true)),2);
						foreach($tmp['taxdata'] as $key=>$tax) {
							$flds = array('order_id'=>$data['order_id'],'line_id'=>$data['line_id'],'tax_id'=>$key,'tax_amount'=>$tax['tax_amount'],'taxable_amount'=>$tax['taxable_amount']);
							$stmt = $this->prepare(sprintf('insert into order_taxes(order_id,line_id,tax_id,tax_amount,taxable_amount) values(?,?,?,?,?)'));
							$stmt->bindParams(array_merge(array('iiidd'),array_values($flds)));
							$status = $status && $stmt->execute();
						}
						$this->logMessage("accContent",sprintf("calling recalcOrder from editLine"),2);
						$this->recalcOrder($data['order_id']);
						$this->commitTransaction();
						$form->init($this->getTemplate('editLineResult'));
						$form->addData($data);
						$this->logMessage("editLine",sprintf("result form [%s]",print_r($form,true)),2);
						$status = "true";
					}
					else {
						$this->rollbackTransaction();
						$form->addError("An error occurred");
						$status = "false";
					}
					return $this->ajaxReturn(array('status'=>$status,'html'=>$form->show()));
				}
				else
					$form->addError('Form Validation Failed');
			}
		}
		return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
	}

	function loadAddresses($passed_id = null) {
		if ($passed_id == null) {
			if (array_key_exists('o_id',$_REQUEST))
				$o_id = $_REQUEST['o_id'];
			else
				$o_id = 0;
		}
		else
			$o_id = $passed_id;
		$addresses = $this->fetchAll(sprintf('select a.*, c.value as addressType from addresses a, code_lookups c where ownertype = "order" and ownerid = %d and deleted = 0 and c.id = a.addressType',$o_id));
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
		$this->logMessage("loadAddresses",sprintf("addresses [%s] addresslist [%s] form [%s]",print_r($addresses,true),print_r($addressList,true),print_r($addrForm,true)),4);
		$addressForm->addTag('addresses',implode('',$addressList),false);
		$addressForm->addTag('o_id',$o_id);
		if (!is_null($passed_id)) {
			$this->logMessage("loadAddresses",sprintf("returning normal show pass_id [%s] isAjax [%s]",$passed_id,$this->isAjax()),3);
			return $addressForm->show();
		}
		else {
			$this->logMessage("loadAddresses",sprintf("returning ajax result show pass_id [%s] isAjax [%s]",$passed_id,$this->isAjax()),3);
			return $this->ajaxReturn(array('status'=>'true','html'=>$addressForm->show()));
		}
	}

	function editAddress() {
		if (!array_key_exists('a_id',$_REQUEST))
			return $this->ajaxReturn(array('status'=>'false','html'=>'No id passed'));
		$a_id = $_REQUEST['a_id'];
		if (array_key_exists("o_id",$_REQUEST))
			$o_id =  $_REQUEST["o_id"];
		else if (array_key_exists("ownerid",$_REQUEST)) 
			$o_id = $_REQUEST['ownerid'];
		else $o_id = 0;
		if (!($data = $this->fetchSingle(sprintf('select a.* from addresses a where a.id = %d and a.ownertype = "order" and a.ownerid = %d',$a_id,$o_id)))) {
			$data = array('id'=>0,'ownertype'=>'order','ownerid'=>$o_id);
			$addresses = array();
		}
		else 
			$addresses = $this->fetchAll(sprintf('select * from addresses where ownertype = "order" and ownerid = %d and deleted = 0',$o_id));
		$form = new Forms();
		$form->init($this->getTemplate('editAddress'),array('name'=>'editAddress'));
		$frmFields = $this->getFields('editAddress');
		if (count($addresses) > 0) {
			$frmFields['delete'] = array('type'=>'button','value'=>'Delete Address','database'=>false,'onclick'=>sprintf('deleteAddress(%d,%d);return false;',$a_id,$o_id));
		}
		$frmFields = $form->buildForm($frmFields);
		$form->addData($data);
		$this->logMessage('editAddress',sprintf('form [%s]',print_r($form,true)),1);
		if (count($_POST) > 0 && array_key_exists('editAddress',$_POST)) {
			$form->addData($_POST);
			if ($form->validate()) {
				unset($frmFields['options']);
				foreach($frmFields as $key=>$fld) {
					if (!(array_key_exists('database',$fld) && $fld['database'] == false)) {
						if ($data['id'] > 0)
							$flds[sprintf('%s = ?',$fld['name'])] = $form->getData($fld['name']);//$_REQUEST[$fld['name']];
						else
							$flds[$fld['name']] = $form->getData($fld['name']);//$_REQUEST[$fld['name']];
					}
				}
				if ($data['id'] == 0) {
					$flds['tax_address'] = (int)$this->fetchScalar(sprintf('select extra from code_lookups where id = %d',$form->getData('addresstype')));
					$stmt = $this->prepare(sprintf('insert into addresses(%s) values(%s)', implode(',',array_keys($flds)), str_repeat('?,', count($flds)-1).'?'));
					$stmt->bindParams(array_merge(array(str_repeat('s', count($flds))),array_values($flds)));
				}
				else {
					$flds['tax_address = ?'] = (int)$this->fetchScalar(sprintf('select extra from code_lookups where id = %d',$form->getData('addresstype')));
					$stmt = $this->prepare(sprintf('update addresses set %s where id = %d', implode(',',array_keys($flds)),$data['id']));
					$stmt->bindParams(array_merge(array(str_repeat('s', count($flds))),array_values($flds)));
				}
				$this->beginTransaction();
				if ($stmt->execute())
					$this->commitTransaction();
				else {
					$this->rollbackTransaction();
					$this->addError('An error occurred updating the database');
				}
			}
			else $this->addError('Form validation Failed');
		}
		return $this->ajaxReturn(array('status'=>'true','html'=>$form->show()));
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

	function moduleStatus($fromMain = 0) {
		if (array_key_exists('formData',$_SESSION) && array_key_exists('orderSearchForm', $_SESSION['formData'])) {
			$_POST = $_SESSION['formData']['orderSearchForm'];
			$msg = "";
		}
		else {
			$ct = $this->fetchScalar(sprintf('select count(0) from %s where order_status & 2 = 2 and (total - authorization_amount) != 0',$this->m_content));
			if ($ct == 0) {
				$_POST = array('showSearchForm'=>1,'order_status'=>2,'deleted'=>0,'sortby'=>'created','sortorder'=>'desc','pager'=>$this->m_perrow);
				$msg = "Showing latest orders added";
			}
			else {
				$_POST = array('showSearchForm'=>1,'order_status'=>2,'opt_owing'=>'!=','owing'=>0,'sortby'=>'created','sortorder'=>'desc','pager'=>$this->m_perrow);
				$msg = "Showing unpaid orders";
			}
		}
		$result = $this->showSearchForm($fromMain,$msg);
		return $result;
	}

	function getHeader() {
		$form = new Forms();
		$form->init($this->getTemplate('header'));
		$flds = $this->getFields('showSearchForm');
		$flds = $form->buildForm($flds);
		if (count($_POST) > 0 && array_key_exists('showSearchForm',$_POST))
			$form->addData($_POST);
		return $form->show();
	}

	function recalcOrder($order_id,$update = true) {
		//
		//	now recalc order value & taxes etc
		//
		$order = array();
		$order['header'] = $this->fetchSingle(sprintf('select * from orders where id = %d',$order_id));
		$order['products'] = $this->fetchAll(sprintf('select * from order_lines where order_id = %d and deleted = 0',$order_id));
		$order['taxes'] = $this->fetchAll(sprintf('select * from order_taxes ot where ot.order_id = %d and ot.line_id in (select o.line_id from order_lines o where o.order_id = ot.order_id and o.deleted = 0)',$order_id));
		if (!$address = $this->fetchSingle(sprintf('select * from addresses where ownerid = %d and ownertype = "order" and tax_address=1',$order_id)))
			$address = array('id'=>0,'province_id'=>0,'country_id'=>0);
		$order['addresses']['shipping'] = $address;
		if (!$address = $this->fetchSingle(sprintf('select * from addresses where ownerid = %d and ownertype = "order" and tax_address=0',$order_id)))
			$address = array('id'=>0,'province_id'=>0,'country_id'=>0);
		$order['addresses']['billing'] = $address;
		$order = Ecom::recalcOrder($order);
		if ($update) {
			$sql = sprintf('update orders set discount_value = %f, value = %f, shipping = %f, taxes = %f, line_discounts = %f, total = %f, net = %f where id = %d',
				$order['header']['discount_value'],
				$order['header']['value'],
				$order['header']['shipping'],
				$order['header']['taxes'],
				$order['header']['line_discounts'],
				$order['header']['total'],
				$order['header']['net'],
				$order_id);
			$this->logMessage("recalcOrder",sprintf("header update [$sql]"),2);
			$this->execute($sql);
			$this->execute(sprintf('delete from order_taxes where order_id = %d and line_id = 0',$order_id));
			$this->execute(sprintf('delete from order_taxes where order_id = %d and line_id = 0',$order_id));
			foreach($order['taxes'] as $key=>$tax) {
				$stmt = $this->prepare('insert into order_taxes(order_id,line_id,tax_id,tax_amount) values(?,?,?,?)');
				$stmt->bindParams(array('iiid',$order_id,0,$key,$tax['tax_amount']));
				$stmt->execute();
			}
		}
		return $order;
	}

	function addItem($fromMain = false) {
		$form = new Forms();
		$form->init($this->getTemplate('addItem'));
		if ($fromMain)
			return $form->show();
		else
			return $this->show($form->show());
	}

	function showOrder() {
		$form = new Forms();
		$form->init($this->getTemplate('showOrder'));
		$form->addData($_REQUEST);
		return $this->show($form->show());
	}

	function formatOrder($data) {
		$data['itemCount'] = $this->fetchScalar(sprintf('select sum(quantity) from order_lines where order_id = %d and deleted = 0',$data['id']));
		$data['formattedTotal'] = money_format('%(.2n',$data['total']);
		$data['formattedNet'] = money_format('%(.2n',$data['net']);
		$data['formattedShipping'] = money_format('%(.2n',$data['shipping']);
		$data['formattedDiscountValue'] = money_format('%(.2n',$data['discount_value']);
		$data['formattedValue'] = money_format('%(.2n',$data['value']);
		$data['formattedTaxes'] = money_format('%(.2n',$data['taxes']);
		$data['formattedAuthorizationAmount'] = money_format('%(.2n',$data['authorization_amount']);
		if ($data['discount_type'] == 'D')
			$data['formattedDiscountRate'] = money_format('%(.2n',$data['discount_rate']);
		else
			$data['formattedDiscountRate'] = sprintf('%.2f%%',$data['discount_rate']);
		$data['formattedCreated'] = date(GLOBAL_DEFAULT_DATETIME_FORMAT,strtotime($data['created']));
		$data['formattedShipped'] = date(GLOBAL_DEFAULT_DATETIME_FORMAT,strtotime($data['ship_date']));
		$status = $this->fetchAll(sprintf('select * from code_lookups where type="orderStatus" order by sort'));
		$tmp = array();
		foreach($status as $key=>$stat) {
			if (($data['order_status'] & (int)$stat['code']) == (int)$stat['code'])
				$tmp[] = $stat['value'];
		}
		$data['formattedStatus'] = implode(", ",$tmp);
		$data['formattedCreated'] = date(GLOBAL_DEFAULT_DATETIME_FORMAT,strtotime($data['created']));
		$this->logMessage('formatOrder',sprintf('return [%s]',print_r($data,true)),4);
		return $data;
	}

	function getRecurring($o_id = null) {
		$byAjax = false;
		if (is_null($o_id)) {
			$o_id = $_REQUEST['o_id'];
			$byAjax = true;
		}
		$result = array();
		$count = $this->fetchScalar(sprintf('select count(0) from order_billing where original_id = %s order by billing_date',$o_id));
		if (array_key_exists('pagenum',$_REQUEST)) 
			$pageNum = $_REQUEST['pagenum'];
		else
			$pageNum = 1;	// no 0 based calcs
		if ($pageNum <= 0) $pageNum = 1;
		$perPage = $this->m_perrow;
		if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
		$pagination = $this->pagination($count, $perPage, $pageNum, 
			array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
			'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'),
			array('url'=>'/modit/ajax/getRecurring/orders','destination'=>'tabs-6'));
		$start = ($pageNum-1)*$perPage;
		$sql = sprintf('select * from order_billing where original_id = %s order by billing_date limit %d,%d',$o_id,$start,$perPage);
		$recs = $this->fetchAll($sql);
		$this->logMessage(__FUNCTION__,sprintf('records [%s] count [%d]',$sql,count($recs)),3);
		$form = new Forms();
		$form->init($this->getTemplate('recurringInfoRow'));
		$flds = $form->buildForm($this->getFields('recurringInfoRow'));
		foreach($recs as $key=>$rec) {
			$form->reset();
			$form->addData($rec);
			$result[] = $form->show();
		}
		$outer = new Forms();
		$outer->init($this->getTemplate('recurringInfo'));
		$flds = $outer->buildForm($this->getFields('recurringInfo'));
		$outer->addTag('pagination',$pagination,false);
		$outer->addTag('data',implode('',$result),false);
		$outer->addTag('o_id',$o_id);
		$outer->addTag('pagenum',$pageNum);
		if ($byAjax) {
			return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
		}
		else
			return $outer->show();
	}

	function showRecurring() {
		$r_id = array_key_exists('r_id',$_REQUEST) ? $_REQUEST['r_id'] : 0;
		$rec = $this->fetchSingle(sprintf('select * from order_billing where id = %d',$r_id));
		$form = new Forms();
		$form->init($this->getTemplate('showRecurring'));
		$flds = $form->buildForm($this->getFields('showRecurring'));
		$form->addData($rec);
		return $this->ajaxReturn(array('status'=>true,'html'=>$form->show()));
	}
	
	function getNames() {
		$query = $_REQUEST['s'];
		$member = $this->fetchScalar(sprintf("select member_id from orders where id = %d",$_REQUEST['m']));
		$select = new select();
		$select->addAttributes(array("sql"=>sprintf("select id, concat(lastname,' ',firstname,if(id=%d,'*','')) from members where (firstname like '%%%s%%' or lastname = '%%%s%%' or id = %d) and deleted = 0 and enabled = 1 order by lastname, firstname", $member, $query, $query, $member ),"name"=>"member_id"));
		return $this->ajaxReturn(array('status'=>true,'html'=>$select->show()));
	}
}

?>