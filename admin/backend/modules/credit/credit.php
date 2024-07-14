<?php

class credit extends Backend {

	private $m_content = 'orders';
	private $m_perrow = 5;

	public function __construct() {
		$this->m_perrow = defined('GLOBAL_PER_PAGE') ? GLOBAL_PER_PAGE : 5;
		$this->M_DIR = 'backend/modules/credit/';
		$this->setTemplates(
			array(
				'main'=>$this->M_DIR.'credit.html',
				'articleList'=>$this->M_DIR.'forms/articleList.html',
				'addContent'=>$this->M_DIR.'forms/addContent.html',
				'showSearchForm'=>$this->M_DIR.'forms/searchForm.html',
				'header'=>$this->M_DIR.'forms/heading.html',
				'dailyLog'=>$this->M_DIR.'forms/dailyLog.html',
				'dailyLogRow'=>$this->M_DIR.'forms/dailyLogRow.html',
				'showPageProperties'=>$this->M_DIR.'forms/showPageProperties.html',
				'showPagePropertiesRow'=>$this->M_DIR.'forms/showPagePropertiesRow.html',
				'getRemaining'=>$this->M_DIR.'forms/getRemaining.html',
				'getRemainingRow'=>$this->M_DIR.'forms/getRemainingRow.html',
				'editRecurring'=>$this->M_DIR.'forms/editRecurring.html',
				'dailyDetails'=>$this->M_DIR.'forms/dailyDetails.html',
				'dailyDetailsRow'=>$this->M_DIR.'forms/dailyDetailsRow.html',
				'nightlySchedule'=>$this->M_DIR.'forms/nightlySchedule.html',
				'nightlyScheduleRow'=>$this->M_DIR.'forms/nightlyScheduleRow.html',
				'addSchedule'=>$this->M_DIR.'forms/addSchedule.html',
				'exchange'=>$this->M_DIR.'forms/exchange.html',
				'exchangeRow'=>$this->M_DIR.'forms/exchangeRow.html',
				'addCurrency'=>$this->M_DIR.'forms/addCurrency.html',
				'salesReports'=>$this->M_DIR.'forms/salesReports.html',
				'salesReportsRow'=>$this->M_DIR.'forms/salesReportsRow.html',
				'recurringOrders'=>$this->M_DIR.'forms/recurringOrders.html',
				'recurringOrdersRow'=>$this->M_DIR.'forms/recurringOrdersRow.html',
				'exportSales'=>$this->M_DIR.'forms/exportSales.html',
				'exportSalesRow'=>$this->M_DIR.'forms/exportSalesRow.html',
				'exportDetails'=>$this->M_DIR.'forms/exportDetails.html',
				'exportDetailsRow'=>$this->M_DIR.'forms/exportDetailsRow.html'
			)
		);
		$this->setFields(array(
			'header'=>array(),
			'addContent'=>array(
				'options'=>array('name'=>'addContent','action'=>'/modit/ajax/addContent/credit','database'=>false),
				'id'=>array('type'=>'tag','database'=>false),
				'member_id'=>array('type'=>'select','required'=>true,'sql'=>'select id, concat(lastname,", ",firstname) from members where deleted = 0 order by lastname, firstname'),
				'order_date'=>array('type'=>'datetimepicker','required'=>true,'AMPM'=>'AMPM','validation'=>'datetime','prettyName'=>'Order Date'),
				'coupon_id'=>array('type'=>'select','required'=>false,'sql'=>'select id, concat(code," - ",name) from coupons where deleted = 0 order by code','id'=>'editCouponId'),
				'value'=>array('type'=>'tag'),
				'authorization_info'=>array('type'=>'textarea','reformat'=>true,'class'=>'mceNoEditor'),
				'authorization_amount'=>array('type'=>'input','required'=>true,'validation'=>'number','prettyName'=>'Authorization Amount'),
				'authorization_amount_ro'=>array('type'=>'tag','required'=>true,'database'=>false),
				'authorization_code'=>array('type'=>'input','prettyName'=>'Authorization Code'),
				'authorization_transaction'=>array('type'=>'input','required'=>false,'prettyName'=>'Transaction Code'),
				'deleted'=>array('type'=>'checkbox','value'=>1),
				'submit'=>array('type'=>'submitbutton','value'=>'Save Order','database'=>false),
				'tempEdit'=>array('type'=>'hidden','value'=>0,'database'=>false),
				'fldName'=>array('type'=>'hidden','value'=>'','database'=>false),
				'discount_rate'=>array('type'=>'input','required'=>false,'validation'=>'number','readonly'=>'readonly','prettyName'=>'Discount Rate'),
				'discount_type'=>array('type'=>'tag'),
				'addContent'=>array('type'=>'hidden','value'=>1,'database'=>false),
				'orderTotal'=>array('type'=>'tag','database'=>false),
				'ship_via'=>array('type'=>'select','lookup'=>'shippers'),
				'ship_date'=>array('type'=>'datepicker'),
				'ship_tracking_code'=>array('type'=>'input'),
				'ship_comments'=>array('type'=>'textarea','class'=>'mceSimple','reformat'=>false),
				'order_status'=>array('type'=>'select','multiple'=>true,'lookup'=>'orderStatus','database'=>false,'id'=>'order_status')
			),
			'showSearchForm'=>array(
				'options'=>array('action'=>'showSearchForm','name'=>'searchForm','id'=>'search_form'),
				'order_status'=>array('type'=>'select','name'=>'order_status','lookup'=>'orderStatus'),
				'opt_created'=>array('type'=>'select','name'=>'opt_created','lookup'=>'search_options'),
				'created'=>array('type'=>'datepicker','required'=>false),
				'opt_quantity'=>array('type'=>'select','name'=>'opt_quantity','lookup'=>'search_options'),
				'quantity'=>array('type'=>'textfield','required'=>false,'validation'=>'number'),
				'opt_shipped'=>array('type'=>'select','name'=>'opt_shipped','lookup'=>'search_options'),
				'shipped'=>array('type'=>'textfield','required'=>false,'validation'=>'number'),
				'showSearchForm'=>array('type'=>'hidden','value'=>1),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'sortby'=>array('type'=>'hidden','value'=>'created'),
				'sortorder'=>array('type'=>'hidden','value'=>'desc'),
				'quicksearch'=>array('type'=>'input','name'=>'quicksearch','required'=>false),
				'opt_quicksearch'=>array('type'=>'hidden','value'=>'like'),
				'opt_order_id'=>array('type'=>'select','lookup'=>'search_options'),
				'order_id'=>array('type'=>'input','required'=>false),
				'status'=>array('type'=>'select','required'=>false,'lookup'=>'orderStatus'),
				'opt_name'=>array('type'=>'select','name'=>'opt_name','lookup'=>'search_string'),
				'name'=>array('type'=>'input','required'=>false),
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
			'dailyLog'=>array(
				'from'=>array('type'=>'datepicker','required'=>false),
				'to'=>array('type'=>'datepicker','required'=>false),
				'dailyLog'=>array('type'=>'hidden','value'=>1),
				'pager'=>array('type'=>'select','required'=>true,'value'=>$this->m_perrow,'lookup'=>'paging','id'=>'pager'),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'submit'=>array('type'=>'submitbutton','value'=>'Search')
			),
			'dailyLogRow'=>array(
				'started'=>array('type'=>'datetimestamp'),
				'completed'=>array('type'=>'datetimestamp'),
				'bill_date'=>array('type'=>'datestamp'),
				'start_date'=>array('type'=>'datestamp'),
				'end_date'=>array('type'=>'datestamp')
			),
			'showPagePropertiesRow'=>array(
				'billing_date'=>array('type'=>'datestamp'),
				'billed_on'=>array('type'=>'datetimestamp'),
				'authorization_amount'=>array('type'=>'currency')
			),
			'getRemaining'=>array(
				'pagenum'=>array('type'=>'hidden'),
				'o_id'=>array('type'=>'hidden')
			),
			'getRemainingRow'=>array(
				'billing_date'=>array('type'=>'datestamp')
			),
			'showPageProperties'=>array(
				'order_date'=>array('type'=>'datetimestamp'),
				'order_status'=>array('type'=>'select','multiple'=>true,'lookup'=>'orderStatus','database'=>false,'id'=>'order_status','enabled'=>false),
				'authorization_amount'=>array('type'=>'currency'),
				'value'=>array('type'=>'currency'),
				'discount_value'=>array('type'=>'currency'),
				'line_discount'=>array('type'=>'currency'),
				'shipping'=>array('type'=>'currency'),
				'taxes'=>array('type'=>'currency'),
				'total'=>array('type'=>'currency'),
				'ship_date'=>array('type'=>'datestamp')
			),
			'editRecurring'=>array(
				'billing_date'=>array('type'=>'datepicker','required'=>true),
				'adjustment'=>array('type'=>'checkbox','value'=>1,'checked'=>'checked'),
				'editRecurring'=>array('type'=>'hidden','value'=>1),
				'r_id'=>array('type'=>'hidden'),
				'submit'=>array('type'=>'submitbutton','value'=>'Update Period')
			),
			'dailyDetails'=>array(
				'pager'=>array('type'=>'select','required'=>true,'value'=>$this->m_perrow,'lookup'=>'paging','id'=>'altPager'),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'perpage'=>array('type'=>'hidden','value'=>$this->m_perrow,'name'=>'pager'),
				'r_id'=>array('type'=>'hidden')
			),
			'nightlySchedule'=>array(
				'from'=>array('type'=>'datepicker','required'=>false),
				'to'=>array('type'=>'datepicker','required'=>false),
				'nightlySchedule'=>array('type'=>'hidden','value'=>1),
				'pager'=>array('type'=>'select','required'=>true,'value'=>$this->m_perrow,'lookup'=>'paging','id'=>'pager'),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'submit'=>array('type'=>'submitbutton','value'=>'Search'),
				'add'=>array('type'=>'button','value'=>'Add a Date','class'=>'def_field_submit','onclick'=>'addSchedule()')
			),
			'nightlyScheduleRow'=>array(
				'start_date'=>array('type'=>'datestamp'),
				'end_date'=>array('type'=>'datestamp'),
				'bill_date'=>array('type'=>'datestamp')
			),
			'addSchedule'=>array(
				'bill_date'=>array('type'=>'datepicker','required'=>true),
				'start_date'=>array('type'=>'datepicker','required'=>true),
				'end_date'=>array('type'=>'datepicker','required'=>true),
				'addSchedule'=>array('type'=>'hidden','value'=>1),
				'submit'=>array('type'=>'submitbutton','value'=>'Save'),
				's_id'=>array('type'=>'hidden','value'=>'%%id%%')
			),
			'exchange'=>array(
				'exchange'=>array('type'=>'hidden','value'=>1),
				'currency_id'=>array('type'=>'select','required'=>false,'idlookup'=>'currencies'),
				'submit'=>array('type'=>'submitbutton','value'=>'Search'),
				'from'=>array('type'=>'datepicker','required'=>false),
				'to'=>array('type'=>'datepicker','required'=>false),
				'addCurrency'=>array('type'=>'button','class'=>'def_field_submit','value'=>'Add a Rate','onclick'=>'addExchange(0);return false;')
			),
			'exchangeRow'=>array(
				'effective_date'=>array('type'=>'datestamp')
			),
			'addCurrency'=>array(
				'currency_id'=>array('type'=>'select','idlookup'=>'currencies','required'=>true),
				'effective_date'=>array('type'=>'datepicker','required'=>true),
				'exchange_rate'=>array('type'=>'textfield','validation'=>'number','required'=>true),
				'save'=>array('type'=>'submitButton','database'=>false,'value'=>'Save'),
				'e_id'=>array('type'=>'hidden','value'=>0,'database'=>false),
				'addCurrency'=>array('type'=>'hidden','value'=>1,'database'=>false)
			),
			'salesReports'=>array(
				'from'=>array('type'=>'datepicker','required'=>true),
				'to'=>array('type'=>'datepicker','required'=>true),
				'order_status'=>array('type'=>'select','required'=>true,'multiple'=>true,'lookup'=>'orderStatus'),
				'salesReports'=>array('type'=>'hidden','value'=>1),
				'pager'=>array('type'=>'select','required'=>true,'value'=>$this->m_perrow,'lookup'=>'paging','id'=>'pager'),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'clicked'=>array('type'=>'hidden'),
				'submit'=>array('type'=>'submitButton','value'=>'Search','name'=>'search','onclick'=>'setClicked(this);'),
				'product_id'=>array('type'=>'select','required'=>false,'multiple'=>true,'sql'=>'select id, concat(name," - ",code) as name from product where deleted = 0 order by name'),
				'sortby'=>array('type'=>'hidden','value'=>'id'),
				'sortorder'=>array('type'=>'hidden','value'=>'desc'),
			),
			'salesReportsRow'=>array(
				'order_date'=>array('type'=>'datetimestamp')
			),
			'recurringOrders'=>array(
				'from'=>array('type'=>'datepicker','required'=>true),
				'to'=>array('type'=>'datepicker','required'=>true),
				'order_status'=>array('type'=>'select','required'=>false,'multiple'=>true,'lookup'=>'orderStatus'),
				'recurringOrders'=>array('type'=>'hidden','value'=>1),
				'pager'=>array('type'=>'select','required'=>true,'value'=>$this->m_perrow,'lookup'=>'paging','id'=>'pager'),
				'pagenum'=>array('type'=>'hidden','value'=>1),
				'submit'=>array('type'=>'submitButton','value'=>'Search'),
				'product_id'=>array('type'=>'select','required'=>false,'sql'=>'select id, concat(name," - ",code) as name from product where deleted = 0 order by name'),
				'sortby'=>array('type'=>'hidden','value'=>'b.billing_date'),
				'sortorder'=>array('type'=>'hidden','value'=>'asc'),
			),
			'recurringOrdersRow'=>array(
				'order_date'=>array('type'=>'datetimestamp')
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
				array('url'=>'/modit/ajax/showFolderContent/credit','destination'=>'middleContent'));
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
			if (array_key_exists('formData',$_SESSION) && array_key_exists('creditSearchForm', $_SESSION['formData']))
				$_POST = $_SESSION['formData']['creditSearchForm'];
			else
				$_POST = array('order_status'=>STATUS_PROCESSING,'sortby'=>'created','sortorder'=>'asc','showSearchForm'=>1);
		if (count($_POST) > 0 && array_key_exists('showSearchForm',$_POST)) {
			$form->addData($_POST);
			if ($form->validate()) {
				$_SESSION['formData']['creditSearchForm'] = $form->getAllData();
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
								$srch = array(sprintf('(%s)',implode(' or ',$tmp)));
								continue 2;
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
						case 'name':
							if (array_key_exists('name',$_POST) && strlen($_POST['name']) > 0)
								$srch[] = sprintf("(m.firstname like '%%%s%%' or m.lastname like '%%%s%%')",$_POST['name'],$_POST['name']);
							break;
						case 'created':
							if (array_key_exists('opt_'.$key,$_POST) && $_POST['opt_'.$key] != null && $value = $form->getData($key)) {
								if ($_POST['opt_'.$key] == 'like') {
									$this->addError('Like is not supported for dates');
								}
								else
									$srch[] = sprintf(' o.%s %s "%s"',$key, $_POST['opt_'.$key],$this->escape_string($value));
							}
							break;
						case 'order_status':
							if (($value = $form->getData($key)) > 0) {
								$srch[] = sprintf(' ((o.order_status & %d) = %d)',$value,$value);
							}
							break;
						case 'quantity':
						case 'shipped':
							if (array_key_exists('opt_'.$key,$_POST) && $_POST['opt_'.$key] != null && $value = $form->getData($key)) {
								if ($_POST['opt_'.$key] == 'like') {
									$this->addError('Like is not supported for numeric fields');
								}
								else
									$srch[] = sprintf(' %s %s "%s"',$key, $_POST['opt_'.$key],(int)$value);
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
					if (array_key_exists('pager',$_POST)) $perPage = $_POST['pager'];
					$count = $this->fetchScalar(sprintf('select count(o.id) from %s o,members m where m.id = o.member_id and %s', $this->m_content, implode(' and ',$srch)));
					$pageNum = max(1,min($pageNum, (floor(($count-1)/$perPage)+1)));
					$form->setData('pagenum', $pageNum);
					$pagination = $this->pagination($count, $perPage, $pageNum,
							array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
							'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'),
							array('url'=>'/modit/ajax/showSearchForm/credit','destination'=>'middleContent')
						);
					$start = ($pageNum-1)*$perPage;
					$sortorder = 'desc';
					$sortby = 'created';
					if (array_key_exists('sortby',$_POST)) {
						$sortby = $_POST['sortby'];
						if ($sortby == 'name') $sortby = 'm.lastname';
						$sortorder = $_POST['sortorder'];
					}
					$sql = sprintf('select o.*, m.firstname, m.lastname, concat(m.firstname," ",m.lastname) as name, sum(l.quantity) as quantity, sum(l.shipped) as shipped from %s o, members m, order_lines l where l.order_id = o.id and m.id = o.member_id and %s group by o.id order by %s %s limit %d,%d',
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
						$tmp = array();
						foreach($status as $key=>$value) {
							if ($article['order_status'] & (int)$value['code'])
								$tmp[] = $value['value'];
						}
						$article['order_status'] = implode(', ',$tmp);
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
			$data = array('id'=>0,'published'=>false,'tax_exemptions'=>'||','value'=>0,'authorization_amount'=>0,'coupon_id'=>0,'discount_type'=>'','order_status'=>0); 
			$frmFields['coupon_id']['sql'] = 'select id, concat(code," - ",name) from coupons where deleted = 0 and enabled = 1 and published = 1 order by code';
		} else {
			$frmFields['coupon_id']['sql'] = sprintf('select id, concat(code," - ",name) from coupons where (deleted = 0 and enabled = 1 and published = 1) or id = %d order by code',$data['coupon_id']);
		}
		$lines = $this->fetchAll(sprintf('select o.*, p.code, p.name, c.name as coupon_name from order_lines o left join coupons c on c.id = o.coupon_id, product p where o.order_id = %d and p.id = o.product_id and o.deleted = 0 order by line_id',$data['id']));
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
					$tmp = array();
					$tmp['header'] = $form->getAllData();
					$tmp['products'] = $this->fetchAll(sprintf('select * from order_lines where order_id = %d and deleted = 0',$data['id']));
					$tmp['taxes'] = $this->fetchAll(sprintf('select * from order_taxes ot where ot.order_id = %d and ot.line_id in (select o.line_id from order_lines o where o.order_id = ot.order_id and o.deleted = 0)',$data['id']));
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
					$this->logMessage("accContent",sprintf("calling recalcOrder from save"),2);
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
						if (array_key_exists('submitEmail',$_POST)) {
							$emails = $this->configEmails("ecommerce");
							if (count($emails) == 0)
								$emails = $this->configEmails("contact");
							$mailer = new PHPMailer();
							$mailer->Subject = sprintf("Order Status Update - %s", SITENAME);
							$body = new Forms();
							$html = $this->getHtmlForm('orderStatus');
							//$sql = sprintf('select * from htmlForms where class = %d and type = "orderStatus"',$this->getClassId('product'));
							//$html = $this->fetchSingle($sql);
							$body->setHTML($html);
							$order = $this->fetchSingle(sprintf('select o.*, m.firstname, m.lastname, m.email from orders o, members m where o.id = %d and m.id = o.member_id',$id));
							$body->addData($this->formatOrder($order));
							$mailer->Body = $body->show();
							$mailer->From = $emails[0]['email'];
							$mailer->FromName = $emails[0]['name'];
							$this->logMessage('addContent',sprintf("mailer object [%s]",print_r($mailer,true)),1);
							$mailer->IsSMTP();
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
				return $this->ajaxReturn(array('status' => $status,'html' => $form->show()));
			}
			else {
				$this->addError('Form Validation Failed');
			}
			$form->addTag('errorMessage',$this->showMessages(),false);
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


	function moduleStatus($fromMain = 0) {
		if (array_key_exists('formData',$_SESSION) && array_key_exists('creditSearchForm', $_SESSION['formData'])) {
			$msg = '';
			$_POST = $_SESSION['formData']['creditSearchForm'];
		}
		else {
			$ct = $this->fetchScalar(sprintf('select count(0) from %s where order_status & %d = %d',$this->m_content,STATUS_CREDIT_HOLD,STATUS_CREDIT_HOLD));
			$ct = $this->fetchScalar(sprintf('select count(0) from %s where order_status & %d = %d',$this->m_content,STATUS_EXPIRING,STATUS_EXPIRING));
			if ($ct == 0) {
				$_POST = array('showSearchForm'=>1,'deleted'=>0,'sortby'=>'created','sortorder'=>'desc','pager'=>$this->m_perrow,'order_status'=>STATUS_PROCESSING);
				$msg = "Showing unshipped orders";
			}
			else {
				$_POST = array('showSearchForm'=>1,'order_status'=>STATUS_EXPIRING,'sortby'=>'created','sortorder'=>'asc','pager'=>$this->m_perrow);
				$msg = "Showing expiring credit cards/authorizations";
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

	function showOrder() {
		$form = new Forms();
		$form->init($this->getTemplate('showOrder'));
		$form->addData($_REQUEST);
		return $this->show($form->show());
	}

	function dailyLog($fromMain = false) {
		$outer = new Forms();
		$outer->init($this->getTemplate('dailyLog'));
		$flds = $outer->buildForm($this->getFields('dailyLog'));
		if (count($_POST) > 0 && array_key_exists('dailyLog',$_POST)) {
			$outer->addData($_POST);
			$valid = $outer->validate();
		}
		else $valid = true;
		if (array_key_exists('pagenum',$_REQUEST)) 
			$pageNum = $_REQUEST['pagenum'];
		else
			$pageNum = 1;	// no 0 based calcs
		if ($pageNum <= 0) $pageNum = 1;
		$perPage = $this->m_perrow;
		$where = array('completed != "0000-00-00 00:00:00"');
		if ($valid && strlen($outer->getData('from')) > 0) {
			$where[] = sprintf('start_date >= "%s"',$outer->getData('from'));
		}
		if ($valid && strlen($outer->getData('to')) > 0) {
			$where[] = sprintf('end_date <= "%s"',$outer->getData('to'));
		}
		if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
		$count = $this->fetchScalar(sprintf('select count(0) from order_processing where %s',implode(' and ',$where)));
		$pageNum = max(1,min($pageNum, (floor(($count-1)/$perPage)+1)));
		$outer->setData('pagenum', $pageNum);
		$pagination = $this->pagination($count, $perPage, $pageNum,
				array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
				'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'),
				array('url'=>'/modit/ajax/showSearchForm/credit','destination'=>'middleContent')
		);
		$start = ($pageNum-1)*$perPage;
		$sql = sprintf('select * from order_processing where %s order by bill_date desc limit %d,%d',implode(' and ',$where),$start,$perPage);
		$recs = $this->fetchAll($sql);
		$result = array();
		$inner = new Forms();
		$inner->init($this->getTemplate('dailyLogRow'));
		$flds = $inner->buildForm($this->getFields('dailyLogRow'));
		foreach($recs as $key=>$rec) {
			$inner->addData($rec);
			$result[] = $inner->show();
		}
		$outer->addTag('rows',implode("",$result),false);
		if ($this->isAjax())
			return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
		else
			return $this->show($outer->show());
	}

	function dailyDetails() {
		$r_id = array_key_exists('r_id',$_REQUEST) ? $_REQUEST['r_id'] : 0;
		$outer = new Forms();
		$outer->init($this->getTemplate('dailyDetails'));
		$flds = $outer->buildForm($this->getFields('dailyDetails'));
		if (array_key_exists('pagenum',$_REQUEST)) 
			$pageNum = $_REQUEST['pagenum'];
		else
			$pageNum = 1;	// no 0 based calcs
		if ($pageNum <= 0) $pageNum = 1;
		$perPage = $this->m_perrow;
		if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
		$count = $this->fetchScalar(sprintf('select count(0) from order_processing_details where processing_id = %d',$r_id));
		$pageNum = max(1,min($pageNum, (floor(($count-1)/$perPage)+1)));
		$outer->setData('pagenum', $pageNum);
		$pagination = $this->pagination($count, $perPage, $pageNum,
				array('prev'=>$this->M_DIR.'forms/altPaginationPrev.html','next'=>$this->M_DIR.'forms/altPaginationNext.html',
				'pages'=>$this->M_DIR.'forms/altPaginationPage.html', 'wrapper'=>$this->M_DIR.'forms/altPaginationWrapper.html'),
				array('url'=>'/modit/ajax/showSearchForm/credit','destination'=>'middleContent')
		);
		$start = ($pageNum-1)*$perPage;
		$sql = sprintf('select * from order_processing_details where processing_id = %d order by processing_status desc, id limit %d,%d',$r_id,$start,$perPage);
		$recs = $this->fetchAll($sql);
		$result = array();
		$inner = new Forms();
		$inner->init($this->getTemplate('dailyDetailsRow'));
		$flds = $inner->buildForm($this->getFields('dailyDetailsRow'));
		foreach($recs as $key=>$rec) {
			switch($rec['processing_status']) {
				case 0:
					$rec['processing_status'] = 'Success';
					break;
				case 1:
					$rec['processing_status'] = 'Warning';
					break;
				case 2:
					$rec['processing_status'] = 'Error';
					break;
				default:
			}
			$inner->addData($rec);
			$result[] = $inner->show();
		}
		$this->logMessage(__FUNCTION__,sprintf('rows [%s] from [%d] records sql [%s]',print_r($result,true),count($recs),$sql),1);
		$outer->addTag('rows',implode("",$result),false);
		$outer->addTag('pagination',$pagination,false);
		$outer->addData(array('r_id'=>$r_id,'pagenum'=>$pageNum));
		if ($this->isAjax())
			return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
		else
			return $this->show($outer->show());
	}

	function showPageProperties($fromMain = false) {
		$o_id = array_key_exists('o_id',$_REQUEST) ? $_REQUEST['o_id'] : 0;
		$data = $this->fetchSingle(sprintf('select * from orders where id = %d', $o_id));
		$outer = new Forms();
		$outer->init($this->getTemplate('showPageProperties'));
		$flds = $outer->buildForm($this->getFields('showPageProperties'));
		$tmp = array();
		$status = $this->fetchAll(sprintf('select * from code_lookups where type="orderStatus" order by sort, code'));
		foreach($status as $key=>$value) {
			if ($data['order_status'] & (int)$value['code'])
				$tmp[] = $value['code'];
		}
		$data['order_status'] = $tmp;
		$outer->addData($data);
		$other = $this->fetchAll(sprintf('select b.* from order_billing b where b.billed = 1 and b.original_id = %d order by order_id',$o_id));
		$recurring = array();
		$inner = new Forms();
		$inner->init($this->getTemplate('showPagePropertiesRow'));
		$flds = $inner->buildForm($this->getFields('showPagePropertiesRow'));
		foreach($other as $key=>$order) {
			$inner->reset();
			$inner->addData($order);
			$recurring[] = $inner->show();
		}
		$outer->addTag('recurring',implode('',$recurring),false);
		$outer->addTag('toBeBilled',$this->getRemaining($o_id),false);
		if ($fromMain)
			return $outer->show();
		else
			return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
	}

	function getRemaining($o_id = null) {
		$fromMain = true;
		if (is_null($o_id)) {
			$o_id = array_key_exists('o_id',$_REQUEST) ? $_REQUEST['o_id'] : 0;
			$fromMain = false;
		}
		$outer = new Forms();
		$outer->init($this->getTemplate('getRemaining'));
		$flds = $outer->buildForm($this->getFields('getRemaining'));
		$outer->addData($_REQUEST);
		$inner = new Forms();
		$inner->init($this->getTemplate('getRemainingRow'));
		$flds = $inner->buildForm($this->getFields('getRemainingRow'));
		$recurring = array();
		if (array_key_exists('pagenum',$_REQUEST)) 
			$pageNum = $_REQUEST['pagenum'];
		else
			$pageNum = 1;	// no 0 based calcs
		if ($pageNum <= 0) $pageNum = 1;
		$perPage = $this->m_perrow;
		if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
		$count = $this->fetchScalar(sprintf('select count(id) from order_billing where billed = 0 and original_id = %d', $o_id));
		$pagination = $this->pagination($count, $perPage, $pageNum, 
			array('prev'=>$this->M_DIR.'forms/altPaginationPrev.html','next'=>$this->M_DIR.'forms/altPaginationNext.html',
			'pages'=>$this->M_DIR.'forms/altPaginationPage.html', 'wrapper'=>$this->M_DIR.'forms/altPaginationWrapper.html'),
			array('url'=>'/modit/ajax/showFolderContent/credit','destination'=>'middleContent'));
		$start = ($pageNum-1)*$perPage;
		$other = $this->fetchAll(sprintf('select * from order_billing where original_id = %d and billed = 0 order by billing_date limit %d,%d',$o_id,$start,$perPage));
		foreach($other as $key=>$order) {
			$inner->reset();
			$inner->addData($order);
			$recurring[] = $inner->show();
		}
		$outer->addTag('rows',implode('',$recurring),false);
		$outer->addTag('pagination',$pagination,false);
		if (!$fromMain)
			return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
		else
			return $outer->show();
	}

	function editRecurring() {
		$r_id = array_key_exists('r_id',$_REQUEST) ? $_REQUEST['r_id'] : 0;
		$data = $this->fetchSingle(sprintf('select * from order_billing where id = %d', $r_id));
		$outer = new Forms();
		$outer->init($this->getTemplate('editRecurring'));
		$flds = $outer->buildForm($this->getFields('editRecurring'));
		$outer->addData(array('r_id'=>$r_id));
		$outer->addData($data);
		if (count($_POST) > 0 && array_key_exists('editRecurring',$_POST)) {
			$outer->addData($_POST);
			$valid = $outer->validate();
			if ($valid) {
				$new = $outer->getData('billing_date');
				if ($data['billing_date'] != $new) {
					if ($new <= date('Y-m-d') && DEV==0) {
						$valid = false;
						$this->addError('New Billing Date must be in the future');
					}
					if ($valid) {
						$curr = new DateTime($new);
						$diff = $curr->diff(new DateTime($data['billing_date']));
						$days = $diff->days;	//format('%d');
						$this->logMessage(__FUNCTION__,sprintf('curr [%s] diff [%s] days [%s]',print_r($curr,true),print_r($diff,true),$days),1);
						$this->beginTransaction();
						$valid = $valid && $this->execute(sprintf('update order_billing set billing_date = "%s" where id = %d',
							date('Y-m-d',strtotime($new)), $r_id));
						$ct = 1;
						if (array_key_exists('adjustment', $_POST) && $_POST['adjustment'] == 1) {
							$next = $this->fetchAll(sprintf('select * from order_billing where original_id = %d and period_number > %d and billed = 0', $data['original_id'], $data['period_number']));
							foreach($next as $key=>$rec) {
								$ct += 1;
								$valid = $valid && $this->execute(sprintf('update order_billing set billing_date = "%s" where id = %d',
									date('Y-m-d',strtotime(sprintf('%s %s %s days', $rec['billing_date'], $diff->invert > 0 ? '+' : '-', $days))), $rec['id']));
							}
							if ($valid) $this->addMessage(sprintf('Updated %d records',$ct));
						}
						if (!$valid) {
							$this->addError('An Error Occurred. No changes were made');
						}
						else {
							$this->commitTransaction();
							$this->addMessage('Update Successful');
						}
					}
				}
			}
			if (!$valid) {
				$this->addError('Form validation failed');
			}
		}
		return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
	}

	function nightlySchedule($fromMain = false) {
		$outer = new Forms();
		$outer->init($this->getTemplate(__FUNCTION__));
		$flds = $outer->buildForm($this->getFields(__FUNCTION__));
		if (count($_POST) > 0 && array_key_exists(__FUNCTION__,$_POST)) {
			$outer->addData($_POST);
			$valid = $outer->validate();
			$where = array(' 1=1 ');
		}
		else {
			$valid = true;
			$where = array(' completed = "0000-00-00 00:00:00"');
		}
		if ($valid && strlen($outer->getData('from')) > 0) {
			$where[] = sprintf('start_date >= "%s"',$outer->getData('from'));
		}
		if ($valid && strlen($outer->getData('to')) > 0) {
			$where[] = sprintf('end_date <= "%s"',$outer->getData('to'));
		}
		if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
		$count = $this->fetchScalar(sprintf('select count(0) from order_processing where %s',implode(' and ',$where)));
		if (array_key_exists('pagenum',$_REQUEST)) 
			$pageNum = $_REQUEST['pagenum'];
		else
			$pageNum = 1;	// no 0 based calcs
		if ($pageNum <= 0) $pageNum = 1;
		$perPage = array_key_exists('pager',$_REQUEST) ? $_REQUEST['pager'] : $this->m_perrow;
		$pageNum = max(1,min($pageNum, (floor(($count-1)/$perPage)+1)));
		$outer->setData('pagenum', $pageNum);
		$pagination = $this->pagination($count, $perPage, $pageNum,
				array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
				'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'),
				array('url'=>'/modit/ajax/showSearchForm/credit','destination'=>'middleContent')
		);
		$start = ($pageNum-1)*$perPage;
		$sql = sprintf('select * from order_processing where %s order by bill_date asc limit %d,%d',implode(' and ',$where),$start,$perPage);
		$recs = $this->fetchAll($sql);
		$result = array();
		$inner = new Forms();
		$inner->init($this->getTemplate('nightlyScheduleRow'));
		$flds = $inner->buildForm($this->getFields('nightlyScheduleRow'));
		foreach($recs as $key=>$rec) {
			$inner->reset();
			$inner->addData($rec);
			$result[] = $inner->show();
		}
		$outer->addTag('rows',implode('',$result),false);
		$outer->addTag('pagination',$pagination,false);
		if ($this->isAjax())
			return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
		else
			return $this->show($outer->show());	
	}

	function addSchedule() {
		$s_id = array_key_exists('s_id',$_REQUEST) ? $_REQUEST['s_id'] : 0;
		if (!$data = $this->fetchSingle(sprintf('select * from order_processing where id = %d', $s_id)))
			$data = array('id'=>0);
		$outer = new Forms();
		$outer->init($this->getTemplate(__FUNCTION__));
		$outer->addData($data);
		$flds = $outer->buildForm($this->getFields(__FUNCTION__));
		$this->logMessage(__FUNCTION__,sprintf('outer [%s]',print_r($outer,true)),1);
		if (count($_POST) > 0 && array_key_exists(__FUNCTION__,$_POST)) {
			$outer->addData($_POST);
			$valid = $outer->validate();
			if ($valid) {
				if ($outer->getData('start_date') > $outer->getData('bill_date')) {
					$valid = false;
					$this->addError('Start Date cannot be after Billing Date');
				}
				if ($outer->getData('end_date') < $outer->getData('bill_date')) {
					$valid = false;
					$this->addError('End Date cannot be before Billing Date');
				}
				if ($outer->getData('start_date') > $outer->getData('end_date')) {
					$valid = false;
					$this->addError('Start Date cannot be after Ending Date');
				}
				if ($ct = $this->fetchScalar(sprintf('select count(0) from order_processing where bill_date >= "%s" and bill_date <= "%s"', $outer->getData('start_date'), $outer->getData('end_date')))) {
					$this->addError('This date has already been scheduled');
					$valid = false;
				}
			}
			if ($valid) {
				$tmp = array(
					'bill_date'=>$outer->getData('bill_date'),
					'start_date'=>$outer->getData('start_date'),
					'end_date'=>$outer->getData('end_date')
				);
				if ($s_id == 0) {
					$stmt = $this->prepare(sprintf('insert into order_processing(%s) values(%s?)', implode(', ',array_keys($tmp)), str_repeat('?, ',count($tmp)-1)));
				}
				else {
					$stmt = $this->prepare(sprintf('update order_processing set %s=? where id = %d', implode('=?, ',array_keys($tmp)), $s_id));
				}
				$stmt->bindParams(array_merge(array(str_repeat('s',count($tmp))),array_values($tmp)));
				$valid = $valid && $stmt->execute();
				if ($valid) {
					$this->addMessage('The date was scheduled');
					$last = $this->fetchScalar(sprintf('select max(end_date) from order_processing where started = "0000-00-00 00:00:00"'));
					if ($last == "") 
						$last = date("Y-m-d");
					else
						$last = date("Y-m-d",strtotime(sprintf("%s + 1 day",$last)));
					$data = array('bill_date'=>$last, 'start_date'=>$last,'end_date'=>$last);
					$outer->addData($data);
				}
				else $this->addError('An error occurred');
			}
		} else {
			if ($data['id'] == 0) {
				$last = $this->fetchScalar(sprintf('select max(end_date) from order_processing where started = "0000-00-00 00:00:00"'));
				if ($last == "") 
					$last = date("Y-m-d");
				else
					$last = date("Y-m-d",strtotime(sprintf("%s + 1 day",$last)));
				$data = array('bill_date'=>$last, 'start_date'=>$last,'end_date'=>$last);
				$outer->addData($data);
			}
		}
		return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
	}

	function exchange() {
		$outer = new Forms();
		$outer->init($this->getTemplate(__FUNCTION__));
		$flds = $outer->buildForm($this->getFields(__FUNCTION__));

		if (count($_POST) > 0 && array_key_exists(__FUNCTION__,$_POST)) {
			$outer->addData($_POST);
			$valid = $outer->validate();
		}
		else $valid = true;
		$where = array('l.id = e.currency_id');
		if (array_key_exists('currency_id',$_REQUEST) && $_REQUEST['currency_id'] > 0) {
			$where[] = sprintf('currency_id = %d',$_REQUEST['currency_id']);
		}
		if (array_key_exists('from',$_REQUEST) && strlen($outer->getData('from')) > 0) {
			$where[] = sprintf('effective_date >= "%s"',$outer->getData('from'));
		}
		if (array_key_exists('to',$_REQUEST) && strlen($outer->getData('to')) > 0) {
			$where[] = sprintf('effective_date <= "%s"',$outer->getData('to'));
		}
		if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
		$count = $this->fetchScalar(sprintf('select count(0) from exchange_rate e, code_lookups l where %s',implode(' and ',$where)));
		if (array_key_exists('pagenum',$_REQUEST)) 
			$pageNum = $_REQUEST['pagenum'];
		else
			$pageNum = 1;	// no 0 based calcs
		if ($pageNum <= 0) $pageNum = 1;
		$perPage = array_key_exists('pager',$_REQUEST) ? $_REQUEST['pager'] : $this->m_perrow;
		$pageNum = max(1,min($pageNum, (floor(($count-1)/$perPage)+1)));
		$outer->setData('pagenum', $pageNum);
		$pagination = $this->pagination($count, $perPage, $pageNum,
				array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
				'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'),
				array('url'=>'/modit/ajax/showSearchForm/credit','destination'=>'middleContent')
		);
		$start = ($pageNum-1)*$perPage;
		$sql = sprintf('select e.*, l.value as currency, l.extra from exchange_rate e, code_lookups l where %s order by effective_date desc limit %d,%d',implode(' and ',$where),$start,$perPage);
		$recs = $this->fetchAll($sql);
		$result = array();
		
		$inner = new Forms();
		$inner->init($this->getTemplate(__FUNCTION__.'Row'));
		$flds = $inner->buildForm($this->getFields(__FUNCTION__.'Row'));

		foreach($recs as $key=>$value) {
			//setlocale(LC_MONETARY,$value['extra']);
			$value['exchange_rate'] = number_format($value['exchange_rate'],4);
			$inner->addData($value);
			$result[] = $inner->show();
		}
		//setlocale(LC_MONETARY,CURRENCY);
		$outer->addTag('rows',implode('',$result),false);
		$outer->addTag('pagination',$pagination,false);

		if ($this->isAjax()) {
			return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
		}
		else
			return $this->show($outer->show());
	}

	function addCurrency() {
		$e_id = array_key_exists('e_id',$_REQUEST) ? $_REQUEST['e_id'] : 0;
		if (!$data = $this->fetchSingle(sprintf('select * from exchange_rate where id = %d', $e_id)))
			$data = array('id'=>0);
		$outer = new Forms();
		$outer->init($this->getTemplate(__FUNCTION__));
		$outer->addData($data);
		$flds = $outer->buildForm($this->getFields(__FUNCTION__));
		if (count($_POST) > 0 && array_key_exists(__FUNCTION__,$_POST)) {
			$outer->addData($_POST);
			$valid = $outer->validate();
			if ($valid) {
				$tmp = array();
				foreach($flds as $key=>$fld) {
					if (!array_key_exists('database',$fld) || $fld['database'] != false) {
						$tmp[$key] = $outer->getData($key);
					}
				}
				if ($e_id == 0) {
					$stmt = $this->prepare(sprintf('insert into exchange_rate(%s) values(%s?)', implode(', ',array_keys($tmp)), str_repeat('?, ',count($tmp)-1)));
				}
				else {
					$stmt = $this->prepare(sprintf('update exchange_rate set %s=? where id = %d', implode('=?, ',array_keys($tmp)), $e_id));
				}
				$stmt->bindParams(array_merge(array(str_repeat('s',count($tmp))),array_values($tmp)));
				$valid = $valid && $stmt->execute();
				if ($valid) {
					$this->addMessage('The exchange rate was added');
				}
				else $this->addError('An error occurred');
			}
		}
		return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
	}

	function salesReports() {
		$outer = new Forms();
		$outer->init($this->getTemplate(__FUNCTION__));
		$flds = $outer->buildForm($this->getFields(__FUNCTION__));
		$totalTotal = 0;
		$totalGoods = 0;
		$totalShipping = 0;
		$recs = array();
		if (count($_POST) > 0 && array_key_exists(__FUNCTION__,$_POST)) {
			$outer->addData($_POST);
			$valid = $outer->validate();
			if ($valid) {
				$where = array();
				if (array_key_exists('from',$_REQUEST) && strlen($outer->getData('from')) > 0) {
					$where[] = sprintf('o.order_date >= "%s 00:00:00"',$outer->getData('from'));
				}
				if (array_key_exists('to',$_REQUEST) && strlen($outer->getData('to')) > 0) {
					$where[] = sprintf('o.order_date <= "%s 23:59:59"',$outer->getData('to'));
				}
				if (array_key_exists('order_status',$_REQUEST) && count($_REQUEST['order_status']) > 0) {
					$status = 0;
					foreach($_REQUEST['order_status'] as $key=>$value) {
						$status |= $value;
					}
					$where[] = sprintf('o.order_status & %d = %d',$status,$status);
					if (($status & STATUS_RECURRING) == 0)
						$where[] = sprintf('o.order_status & %d = 0',STATUS_RECURRING);
				}
				if (array_key_exists('product_id',$_REQUEST) && count($_REQUEST['product_id']) > 0 && implode(",",$_REQUEST["product_id"]) != "") {
					$where[] = sprintf('o.id in (select order_id from order_lines where product_id in (%s))',implode(",",$_REQUEST['product_id']));
				}
				if (array_key_exists('clicked',$_POST) && strpos($_POST['clicked'],"Export") !== false) {
					switch($_POST['clicked']) {
					case "Export Orders":
						$this->exportSales($where);
						break;
					case "Export Details":
						$this->exportDetails($where);
						break;
					default:
						break;
					}
					exit();
				}
				if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
				$count = $this->fetchScalar(sprintf('select count(0) from orders o where %s',implode(' and ',$where)));
				$outer->addTag('count',$count);
				if (array_key_exists('product_id',$_REQUEST) && count($_REQUEST['product_id']) > 0 && implode(",",$_REQUEST["product_id"]) != "") {
					$t = $this->fetchSingle(sprintf("select sum(ol.value) as sumValue, sum(o.total) as totalValue, sum(o.shipping) as sumShipping from orders o, order_lines ol where %s and ol.product_id in (%s) and ol.deleted = 0 and ol.order_id = o.id group by ol.product_id, ol.options_id",implode(' and ',$where),implode(",",$_REQUEST['product_id'])));
					$totalShipping = $t['sumShipping'];
					$totalGoods = $t['sumValue'];
					$totalTotal = $t['totalValue'];
				}
				else {
					if ($t = $this->fetchSingle(sprintf("select sum(value) as sumValue, sum(shipping) as sumShipping, sum(total) as sumTotal from orders o where %s",implode(' and ',$where)))) {
						$totalShipping = $t['sumShipping'];
						$totalGoods = $t['sumValue'];
						$totalTotal = $t['sumTotal'];
					}
				}
				if (array_key_exists('pagenum',$_REQUEST)) 
					$pageNum = $_REQUEST['pagenum'];
				else
					$pageNum = 1;	// no 0 based calcs
				if ($pageNum <= 0) $pageNum = 1;
				$perPage = array_key_exists('pager',$_REQUEST) ? $_REQUEST['pager'] : $this->m_perrow;
				$pageNum = max(1,min($pageNum, (floor(($count-1)/$perPage)+1)));
				$outer->setData('pagenum', $pageNum);
				$pagination = $this->pagination($count, $perPage, $pageNum,
						array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
						'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'),
						array('url'=>'/modit/ajax/showSearchForm/credit','destination'=>'middleContent')
				);
				$start = ($pageNum-1)*$perPage;
				if (array_key_exists('product_id',$_REQUEST) && count($_REQUEST['product_id']) > 0 && implode(",",$_REQUEST["product_id"]) != "") {
					$sql = sprintf('select o.*, m.firstname, m.lastname, ol.value from orders o left join members m on o.member_id = m.id, order_lines ol where %s and ol.product_id in (%s) and ol.deleted = 0 and ol.order_id = o.id order by %s %s limit %d,%d',implode(' and ',$where),implode(",",$_REQUEST['product_id']),$_REQUEST['sortby'], $_REQUEST['sortorder'], $start,$perPage);
				}
				else {
					$sql = sprintf('select o.*, m.firstname, m.lastname from orders o left join members m on o.member_id = m.id where %s order by %s %s limit %d,%d',implode(' and ',$where),$_REQUEST['sortby'], $_REQUEST['sortorder'], $start,$perPage);
				}
				$recs = $this->fetchAll($sql);
				$outer->addTag('pagination',$pagination,false);
			}
		}
		$result = array();
		$inner = new Forms();
		$inner->init($this->getTemplate(__FUNCTION__.'Row'));
		$flds = $inner->buildForm($this->getFields(__FUNCTION__.'Row'));
		$goods = 0;
		$shipping = 0;
		$total = 0;
		foreach($recs as $key=>$value) {
			$order = $this->formatOrder($value);
			$inner->addData($order);
			$result[] = $inner->show();
			$goods += $value["value"];
			$shipping += $value["shipping"];
			$total += $value["total"];
		}
		$outer->addTag('rows',implode('',$result),false);
		$outer->addTag("pageGoods",money_format("%(.2n",$goods));
		$outer->addTag("pageShipping",money_format("%(.2n",$shipping));
		$outer->addTag("pageTotal",money_format("%(.2n",$total));
		$outer->addTag("totalGoods",money_format("%(.2n",$totalGoods));
		$outer->addTag("totalShipping",money_format("%(.2n",$totalShipping));
		$outer->addTag("totalTotal",money_format("%(.2n",$totalTotal));
		if ($this->isAjax()) {
			return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
		}
		else
			return $this->show($outer->show());
	}

	function exportSales($where) {
		if (array_key_exists('product_id',$_REQUEST) && count($_REQUEST['product_id']) > 0) {
			$sql = sprintf('select o.* from orders o, order_lines ol where %s and ol.product_id in (%s) and ol.deleted = 0 and ol.order_id = o.id',implode(' and ',$where),implode(",",$_REQUEST['product_id']));
		}
		else {
			$sql = sprintf('select o.* from orders o where %s',implode(' and ',$where));
		}
		$orders = $this->fetchAll($sql);
		$outer = new Forms();
		$outer->init($this->getTemplate(__FUNCTION__));
		$inner = new Forms();
		$inner->init($this->getTemplate(__FUNCTION__.'Row'));
		$rows = array();
		foreach($orders as $key=>$order) {
			$inner->reset();
			$order = $this->formatOrder($order);
			if ($rec = $this->fetchSingle(sprintf("select * from addresses where ownertype='order' and ownerid = %d and tax_address=1",$order['id'])))
				$order['shippingAddress'] = Address::formatData($rec);
			if ($rec = $this->fetchSingle(sprintf("select * from addresses where ownertype='order' and ownerid = %d and tax_address=0",$order['id'])))
				$order['billingAddress'] = Address::formatData($rec);
			$taxes = $this->fetchAll(sprintf("select ot.*, t.* from order_taxes ot, taxes t where t.id = ot.tax_id and ot.order_id = %d and line_id = 0",$order["id"]));
			$order["line_taxes"] = $taxes;
			$order['member'] = $this->fetchSingle(sprintf("select * from members where id = %d",$order['member_id']));
			$inner->addData($order);
			$rows[] = $inner->show();
		}
		$outer->addTag('rows',implode('',$rows),false);
		$tmp = $outer->show();
		header('Content-Type: application/csv');
		header(sprintf('Content-Disposition: attachment; filename=sales-%s-%s.csv',$_REQUEST['from'],$_REQUEST['to']));
		header("Content-Length: ".strlen($tmp));
		header('Pragma: no-cache');
		echo $tmp;
		exit();
	}

	function exportDetails($where) {
		if (array_key_exists('product_id',$_REQUEST) && count($_REQUEST['product_id']) > 0) {
			$sql = sprintf('select o.* from orders o, order_lines ol where %s and ol.product_id in (%s) and ol.deleted = 0 and ol.order_id = o.id',implode(' and ',$where),implode(",",$_REQUEST['product_id']));
		}
		else {
			$sql = sprintf('select o.* from orders o where %s',implode(' and ',$where));
		}
		$orders = $this->fetchAll($sql);
		$outer = new Forms();
		$outer->init($this->getTemplate(__FUNCTION__));
		$inner = new Forms();
		$inner->init($this->getTemplate(__FUNCTION__.'Row'));
		$rows = array();
		foreach($orders as $key=>$order) {
			$inner->reset();
			$order = $this->formatOrder($order);
			if ($rec = $this->fetchSingle(sprintf("select * from addresses where ownertype='order' and ownerid = %d and tax_address=1",$order['id'])))
				$order['shippingAddress'] = Address::formatData($rec);
			if ($rec = $this->fetchSingle(sprintf("select * from addresses where ownertype='order' and ownerid = %d and tax_address=0",$order['id'])))
				$order['billingAddress'] = Address::formatData($rec);
			$order['member'] = $this->fetchSingle(sprintf("select * from members where id = %d",$order['member_id']));
			if (array_key_exists('product_id',$_REQUEST) && count($_REQUEST['product_id']) > 0) {
				$lines = $this->fetchAll(sprintf("select ol.*, p.code, p.name, po.teaser as opt_teaser, pr.teaser as recur_teaser from order_lines ol left join product_options po on po.id = ol.options_id left join product_recurring pr on pr.id = ol.recurring_period, product p where ol.order_id = %d and ol.product_id in (%s) and p.id = ol.product_id",$order["id"],implode(",",$_REQUEST['product_id'])));
			}
			else {
				$lines = $this->fetchAll(sprintf("select ol.*, p.code, p.name, po.teaser as opt_teaser, pr.teaser as recur_teaser from order_lines ol left join product_options po on po.id = ol.options_id left join product_recurring pr on pr.id = ol.recurring_period, product p where ol.order_id = %d and p.id = ol.product_id",$order["id"]));
			}
			$taxes = $this->fetchAll(sprintf("select ot.*, t.* from order_taxes ot, taxes t where t.id = ot.tax_id and ot.order_id = %d and line_id = 0",$order["id"]));
			$order["line_taxes"] = $taxes;
			foreach($lines as $subkey=>$line) {
				$order['line'] = $this->formatOrderLine($line);
				$inner->addData($order);
				$rows[] = $inner->show();
			}
		}
		$outer->addTag('rows',implode('',$rows),false);
		$tmp = $outer->show();
		header('Content-Type: application/csv');
		header(sprintf('Content-Disposition: attachment; filename=sales-details-%s-%s.csv',$_REQUEST['from'],$_REQUEST['to']));
		header("Content-Length: ".strlen($tmp));
		header('Pragma: no-cache');
		echo $tmp;
		exit();
	}

	function recurringOrders() {
		$outer = new Forms();
		$outer->init($this->getTemplate(__FUNCTION__));
		$flds = $outer->buildForm($this->getFields(__FUNCTION__));
		$totalTotal = 0;
		$totalGoods = 0;
		$totalShipping = 0;
		$recs = array();
		if (count($_POST) > 0 && array_key_exists(__FUNCTION__,$_POST)) {
			$outer->addData($_POST);
			$valid = $outer->validate();
			if ($valid) {
				$where = array("b.original_id = o.id");
				if (array_key_exists('from',$_REQUEST) && strlen($outer->getData('from')) > 0) {
					$where[] = sprintf('b.billing_date >= "%s"',$outer->getData('from'));
				}
				if (array_key_exists('to',$_REQUEST) && strlen($outer->getData('to')) > 0) {
					$where[] = sprintf('b.billing_date <= "%s"',$outer->getData('to'));
				}
				if (array_key_exists('order_status',$_REQUEST) && count($_REQUEST['order_status']) > 0) {
					$status = 0;
					foreach($_REQUEST['order_status'] as $key=>$value) {
						$status |= $value;
					}
					$where[] = sprintf('o.order_status & %d = %d',$status,$status);
				}
				if (array_key_exists('product_id',$_REQUEST) && $_REQUEST['product_id'] > 0) {
					$where[] = sprintf('o.id in (select order_id from order_lines where product_id = %d)',$_REQUEST['product_id']);
				}
				if (array_key_exists('pager',$_REQUEST)) $perPage = $_REQUEST['pager'];
				$count = $this->fetchScalar(sprintf('select count(0) from orders o, order_billing b where %s',implode(' and ',$where)));
				$outer->addTag('count',$count);
				if (array_key_exists('product_id',$_REQUEST) && $_REQUEST['product_id'] > 0) {
					$t = $this->fetchSingle(sprintf("select sum(ol.value) as sumValue, sum(o.total) as totalValue, sum(o.shipping) as sumShipping from orders o, order_lines ol, order_billing b where %s and ol.product_id = %d and ol.deleted = 0 and ol.order_id = o.id group by ol.product_id, ol.options_id",implode(' and ',$where),$_REQUEST['product_id']));
					$totalShipping = $t['sumShipping'];
					$totalGoods = $t['sumValue'];
					$totalTotal = $t['totalValue'];
				}
				else {
					if ($t = $this->fetchSingle(sprintf("select sum(value) as sumValue, sum(shipping) as sumShipping, sum(total) as sumTotal from orders o, order_billing b where %s",implode(' and ',$where)))) {
						$totalShipping = $t['sumShipping'];
						$totalGoods = $t['sumValue'];
						$totalTotal = $t['sumTotal'];
					}
				}
				if (array_key_exists('pagenum',$_REQUEST)) 
					$pageNum = $_REQUEST['pagenum'];
				else
					$pageNum = 1;	// no 0 based calcs
				if ($pageNum <= 0) $pageNum = 1;
				$perPage = array_key_exists('pager',$_REQUEST) ? $_REQUEST['pager'] : $this->m_perrow;
				$pageNum = max(1,min($pageNum, (floor(($count-1)/$perPage)+1)));
				$outer->setData('pagenum', $pageNum);
				$pagination = $this->pagination($count, $perPage, $pageNum,
						array('prev'=>$this->M_DIR.'forms/paginationPrev.html','next'=>$this->M_DIR.'forms/paginationNext.html',
						'pages'=>$this->M_DIR.'forms/paginationPage.html', 'wrapper'=>$this->M_DIR.'forms/paginationWrapper.html'),
						array('url'=>'/modit/ajax/showSearchForm/credit','destination'=>'middleContent')
				);
				$start = ($pageNum-1)*$perPage;
				if (array_key_exists('product_id',$_REQUEST) && $_REQUEST['product_id'] > 0) {
					$sql = sprintf('select o.*, m.firstname, m.lastname, ol.value, b.billing_date from orders o left join members m on o.member_id = m.id, order_lines ol, order_billing b where %s and ol.product_id = %d and ol.deleted = 0 and ol.order_id = o.id order by %s %s limit %d,%d',implode(' and ',$where),$_REQUEST['product_id'],$_REQUEST['sortby'], $_REQUEST['sortorder'], $start,$perPage);
				}
				else {
					$sql = sprintf('select o.*, m.firstname, m.lastname, b.billing_date from orders o left join members m on o.member_id = m.id, order_billing b where %s order by %s %s limit %d,%d',implode(' and ',$where),$_REQUEST['sortby'], $_REQUEST['sortorder'], $start,$perPage);
				}
				$recs = $this->fetchAll($sql);
				$outer->addTag('pagination',$pagination,false);
			}
		}
		$result = array();
		$inner = new Forms();
		$inner->init($this->getTemplate(__FUNCTION__.'Row'));
		$flds = $inner->buildForm($this->getFields(__FUNCTION__.'Row'));
		$goods = 0;
		$shipping = 0;
		$total = 0;
		foreach($recs as $key=>$value) {
			$order = $this->formatOrder($value);
			$inner->addData($order);
			$result[] = $inner->show();
			$goods += $value["value"];
			$shipping += $value["shipping"];
			$total += $value["total"];
		}
		$outer->addTag('rows',implode('',$result),false);
		$outer->addTag("pageGoods",money_format("%(.2n",$goods));
		$outer->addTag("pageShipping",money_format("%(.2n",$shipping));
		$outer->addTag("pageTotal",money_format("%(.2n",$total));
		$outer->addTag("totalGoods",money_format("%(.2n",$totalGoods));
		$outer->addTag("totalShipping",money_format("%(.2n",$totalShipping));
		$outer->addTag("totalTotal",money_format("%(.2n",$totalTotal));
		if ($this->isAjax()) {
			return $this->ajaxReturn(array('status'=>true,'html'=>$outer->show()));
		}
		else
			return $this->show($outer->show());
	}

	function getMembers() {
		$query = $_REQUEST['s'];
		$member = $this->fetchScalar(sprintf("select id from members where id = %d",$_REQUEST['m']));
		$select = new select();
		$select->addAttributes(array("sql"=>sprintf("select id, concat(company,', ',lastname,', ',firstname,if(id=%d,'*',''),' ',email) from members where (firstname like '%%%s%%' or lastname = '%%%s%%' or company like '%%%s%%' or id = %d) and deleted = 0 and enabled = 1 order by company, lastname, firstname", $member, $query, $query, $query, $member ),"name"=>"member_id"));
		return $this->ajaxReturn(array('status'=>true,'html'=>$select->show()));
	}

}

?>