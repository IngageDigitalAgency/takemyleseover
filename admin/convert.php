<?php

$st = microtime();
$source = "conversion/";
require_once("config.php");
require_once("classes/globals.php");
require_once("classes/common.php");
require_once("classes/mailer.php");
require_once("classes/smtp.php");
require_once("classes/Forms.php");
require_once("classes/HtmlElement.php");
require_once("classes/mptt.php");
require_once("classes/conversion.php");
date_default_timezone_set(TZ);
setlocale(LC_MONETARY,CURRENCY);
error_reporting(E_ALL);
$dest = new Common(true);
//$dest->beginTransaction();

$pages = $dest->fetchAll(sprintf('select * from content'));
foreach($pages as $page) {
	if ($page['alternate_title'] != '')
		$page['search_title'] = preg_replace('#[^a-z0-9_]#i', '-', strtolower($page['title'].' '.$page['alternate_title']));
	else
		$page['search_title'] = preg_replace('#[^a-z0-9_]#i', '-', strtolower($page['title']));
	$stmt = $dest->prepare(sprintf('update content set search_title = ? where id = ?'));
	$stmt->bindParams(array('sd',$page['search_title'],$page['id']));
	$stmt->execute();
}
//$dest->commitTransaction();

clearDatabase($dest);
$file = 'users.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf("%s%s",$source,$file),'r')) {
	$stmt = $dest->prepare(sprintf('insert into users(email,password,fname,lname,enabled,created) values(?,?,?,?,?,?)'));
	while($data = fgetcsv($fh,0,"~",'"','\\')) {
		$stmt->bindParams(array('ssssss',$data[9],$data[2],$data[3],$data[4],$data[5] == 0 ? 1 : 0,$data[6]));
		$stmt->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}

$file = 'templates.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$mptt = new mptt('template_folders');
	$fldr_cat = array();
	$template_cat = array(0=>0);
	$fldr_cat[0] = $mptt->add(0,0,array('title'=>'Templates'));
	$stmt = $dest->prepare(sprintf('insert into templates(template_id,title,special_processing,html,version,enabled,deleted) values(?,?,?,?,1,1,0)'));
	$fldr = $dest->prepare(sprintf('insert into templates_by_folder(template_id,folder_id) values(?,?)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$html = $data[$flds['content']];
		if (strpos($html,"%%cmscss%%") === false)
			$html = str_replace("%%cmsjs%%", "%%cmsjs%% %%cmscss%%", $html);
		$stmt->bindParams(array('ssss',$data[$flds['id']],$data[$flds['name']],$data[$flds['special']],$html));
		$stmt->execute();
		$id = $dest->insertId();
		$template_cat[$data[$flds['id']]] = $id;
		$dest->execute(sprintf('update templates set template_id = id where id = %d',$id));
		$fldr->bindParams(array('ss',$id,$fldr_cat[0]));
		$fldr->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}


$file = 'advert_cat.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$fldr_cat = array();
	$stmt = $dest->prepare(sprintf('insert into advert_folders(left_id,right_id,level,title,enabled) values(?,?,?,?,1)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {	//,'"','\\')) {
		$stmt->bindParams(array('ssss',$data[2],$data[3],$data[4],$data[1]));
		$stmt->execute();
		$fldr_cat[$data[0]] = $dest->insertId();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$file = 'advert.csv';
	$fh = fopen(sprintf("%s%s",$source,$file),'r');
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$stmt = $dest->prepare(sprintf('insert into advert(title,start_date,end_date,url,views,clicks,image1,width,height,new_window,clickable,published,enabled) values(?,?,?,?,?,?,?,?,?,?,?,1,1)'));
	$fldr = $dest->prepare(sprintf('insert into advert_by_folder(advert_id,folder_id) values(?,?)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$data[8] = normalize($data[8]);
		$stmt->bindParams(array('ssssddsdddd',$data[7],$data[2],$data[3],$data[4],$data[5],$data[6],$data[8],$data[12],$data[13],$data[17],$data[16]));
		$stmt->execute();
		$ad = $dest->insertId();
		$fldr->bindParams(array('dd',$ad,$fldr_cat[$data[14]]));
		$fldr->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}

$file = 'membergroups.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$member_cat = array();
	$stmt = $dest->prepare(sprintf('insert into members_folders(left_id,right_id,level,title,enabled,description,template_id) values(?,?,?,?,?,?,?)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$data[$flds['templates_id']] = $data[$flds['templates_id']]=='NULL'?0:$data[$flds['templates_id']];
		$stmt->bindParams(array('ssssssd',$data[$flds['left_id']],$data[$flds['right_id']],$data[$flds['level']],$data[$flds['name']],$data[$flds['enabled']],$data[$flds['description']],$template_cat[$data[$flds['templates_id']]]));
		$stmt->execute();
		$member_cat[$data[0]] = $dest->insertId();
	}
	$mptt = new mptt('members_folders');
	$member_cat[0] = $mptt->add(0,0,array('title'=>'Unallocated Events'));
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$file = 'cal_events.csv';
	$fh = fopen(sprintf("%s%s",$source,$file),'r');
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$stmt = $dest->prepare(sprintf('insert into events(name,description,enabled,featured,start_date,end_date,start_time,end_time,image1,image2,deleted,published,created,website) values(?,?,?,?,?,?,?,?,?,?,?,1,?)'));
	$fldr = $dest->prepare(sprintf('insert into events_by_folder(event_id,folder_id) values(?,?)'));
	$evt_dates = $dest->prepare(sprintf('insert into event_dates(event_date,event_id) values(?,?)'));
	$addr_type = $dest->fetchScalar(sprintf('select id from code_lookups where type="eventAddressTypes" and code = "location"'));
	$address = $dest->prepare('insert into addresses(ownertype,ownerid,addresstype,addressname,line1,city,postalcode,phone1,email) values(?,?,?,?,?,?,?,?,?)');
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$data[24] = normalize($data[24]);
		$data[25] = normalize($data[25]);
		if ($data[8] == 'PM' && $data[7] != 'N/A') {
			$tmp = explode(':',$data[7]);
			if ($data[7] != '12:00')
				$data[7] = sprintf('%2d:%02d',$tmp[0]+12,$tmp[1]);
		}
		$st = $data[7];
		if ($data[11] == 'PM' && $data[10] != 'N/A') {
			$tmp = explode(':',$data[10]);
			$data[10] = sprintf('%2d:%02d',$tmp[0]+12,$tmp[1]);
		}
		$et = $data[10];
		$created = $data[6] <= date('Y-m-d') ? $data[6]:date(DATE_ATOM) ;
		$stmt->bindParams(array('ssddssssssdss',$data[3],$data[17],1,$data[1],$data[6],$data[9],$st,$et,$data[24],$data[25],$data[32],$created,$data[$flds['event_website']]));
		$stmt->execute();
		$ad = $dest->insertId();
		$fldr->bindParams(array('dd',$ad,$member_cat[$data[27]]));
		$fldr->execute();
		$evt_dates->bindParams(array('ss',$data[6],$ad));
		$evt_dates->execute();
		if ($data[9] == '1969-12-31') $data[9] = '0000-00-00';
		$tmp = $data[6];
		if ($data[9] != '0000-00-00') {
			$tmp = $data[6];
			while($data[6] < $data[9]) {
				$tmp = $data[6];
				$data[6] = date('Y-m-d',strtotime(sprintf('%s + %d days',$data[6],$data[12]>0?$data[12]:1)));
				$evt_dates->bindParams(array('ss',$data[6],$ad));
				$evt_dates->execute();
			}
			if ($data[12] > 0)
				$dest->execute(sprintf('update events set recurring_event=1, recurring_frequency = %d, recurring_type = "Daily" where id = %d',$data[9],$ad));
		}
		$address->bindParams(array('sssssssss','event',$ad,$addr_type,$data[4],$data[13],$data[14],$data[16],$data[20],$data[18]));
		$address->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$file = 'members.csv';
	$fh = fopen(sprintf("%s%s",$source,$file),'r');
	$stmt = $dest->prepare(sprintf('insert into members(firstname,lastname,email,username,password,biography,expires,image1,deleted,created,enabled) values(?,?,?,?,?,?,?,?,?,?,1)'));
	$addr = $dest->prepare(sprintf('insert into addresses(ownertype,ownerid,addresstype,tax_address,line1,line2,city,postalcode,province_id,country_id,phone1,fax,email) values(?,?,?,?,?,?,?,?,?,?,?,?,?)'));
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$members = array();
	$addrType = $dest->fetchScalar('select id from code_lookups where type="memberAddressTypes" and extra = "1"');
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$data[$flds['image']] = normalize($data[$flds['image']]);
		$stmt->bindParams(array('ssssssssss',$data[$flds['firstname']],$data[$flds['lastname']],$data[$flds['email']],$data[$flds['username']],$data[$flds['password']],$data[$flds['player_bio']],$data[$flds['expiry']],$data[$flds['image']],$data[$flds['deleted']],date(DATE_ATOM)));
		$stmt->execute();
		$members[$data[$flds['id']]] = $dest->insertId();
		$addr->bindParams(array('sssssssssssss','member',$members[$data[$flds['id']]],$addrType,1,$data[$flds['address1']],$data[$flds['address2']],$data[$flds['city']],$data[$flds['zippost']],$data[$flds['provstate_id']],$data[$flds['country_id']],$data[$flds['phone']],$data[$flds['fax']],$data[$flds['email']]));
		$addr->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$file = 'membersgroup_member.csv';
	$fh = fopen(sprintf("%s%s",$source,$file),'r');
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$fldr = $dest->prepare(sprintf('insert into members_by_folder(member_id,folder_id) values(?,?)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$fldr->bindParams(array('dd',$members[$data[$flds['memberid']]],$member_dat[$data[$flds['mgroupid']]]));
		$fldr->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}

$file = 'coupons.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$fldr_cat = array();
	$mptt = new mptt('coupon_folders');
	$fldr_cat[0] = $mptt->add(0,0,array('title'=>'Unallocated Coupons','enabled'=>1));
	$stmt = $dest->prepare(sprintf('insert into coupons(code,name,description,start_date,end_date,enabled,amount,percent_or_dollar,shipping_only,min_amount,image1,image2) values(?,?,?,?,?,?,?,?,?,?,?,?)'));
	$fldr = $dest->prepare(sprintf('insert into coupons_by_folder(coupon_id,folder_id) values(?,?)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$data[19] = normalize($data[19]);
		$data[20] = normalize($data[20]);
		$stmt->bindParams(array('ssssssssssss',$data[2],$data[1],$data[4],$data[10].' 00:00:00',$data[11].' 00:00:00',$data[12],$data[7],'P',$data[17],$data[18],$data[19],$data[20]));
		$stmt->execute();
		$ad = $dest->insertId();
		$fldr->bindParams(array('dd',$ad,$fldr_cat[0]));
		$fldr->execute();
	}
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}

$file = 'ecom_cat.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$fldr_cat = array();
	$prod_ids = array();
	$stmt = $dest->prepare(sprintf('insert into product_folders(title,left_id,right_id,level,enabled,image,rollover_image,template_id) values(?,?,?,?,?,?,?,?)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$data[7] = normalize($data[7]);
		$data[8] = normalize($data[8]);
		$stmt->bindParams(array('sddddssd',$data[1],$data[2],$data[3],$data[4],$data[6],$data[7],$data[8],$template_cat[$data[$flds['templates_id']]]));
		$stmt->execute();
		$fldr_cat[$data[0]] = $dest->insertId();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$mptt = new mptt('product_folders');
	$fldr_cat[0] = $mptt->add(0,0,array('title'=>'Unallocated Products'));
	$file = 'ecom_product.csv';
	$fh = fopen(sprintf("%s%s",$source,$file),'r');
	$stmt = $dest->prepare(sprintf('insert into product(name,code,description,sale_startdate,sale_enddate,image1,featured,deleted,tax_exemptions,image2,image3,image4,created,published,enabled) values(?,?,?,?,?,?,?,?,?,?,?,?,?,1,1)'));
	$fldr = $dest->prepare(sprintf('insert into product_by_folder(product_id,folder_id) values(?,?)'));
	$pricing = $dest->prepare(sprintf('insert into product_pricing(product_id,min_quantity,max_quantity,price,sale_price,shipping,shipping_type) values(?,?,?,?,?,?,"E")'));
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$data[$flds['image']] = normalize($data[$flds['image']]);
		$data[$flds['image2']] = normalize($data[$flds['image2']]);
		$data[$flds['image3']] = normalize($data[$flds['image3']]);
		$data[$flds['image4']] = normalize($data[$flds['image4']]);
		$stmt->bindParams(array('sssssssssssss',$data[$flds['name']],$data[$flds['itemcode']],$data[$flds['description']],$data[$flds['sale_start']],$data[$flds['sale_end']],$data[$flds['image']],$data[$flds['featured']],
				$data[$flds['deleted']],$data[$flds['taxexempt']],$data[$flds['image2']],$data[$flds['image3']],$data[$flds['image4']],date(DATE_ATOM)));
		$stmt->execute();
		$prod_ids[$data[0]] = $dest->insertId();
		if (array_key_exists($data[$flds['cat_id']],$fldr_cat))
			$fldr->bindParams(array('dd',$prod_ids[$data[0]],$fldr_cat[$data[$flds['cat_id']]]));
		else
			$fldr->bindParams(array('dd',$prod_ids[$data[0]],$fldr_cat[0]));
		$fldr->execute();
		$pricing->bindParams(array('ssssss',$prod_ids[$data[0]],1,9999,$data[$flds['retailprice']],$data[$flds['saleprice']],$data[$flds['ship_cost']]));
		$pricing->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));	
	$file = 'ecom_prodopts.csv';
	$fh = fopen(sprintf("%s%s",$source,$file),'r');
	$stmt = $dest->prepare(sprintf('insert into product_options(product_id,price,teaser,shipping,sequence) values(?,?,?,?,?)'));
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$seq = 0;
	$prev = 0;
	$options = array(0=>0);
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		if ($prev > 0 && $prev != $data[$flds['prod_id']]) $seq = 0;
		$seq += 10;
		if (array_key_exists($data[$flds['prod_id']],$prod_ids)) {
			$stmt->bindParams(array('ddsdd',$prod_ids[$data[$flds['prod_id']]],$data[$flds['price']],$data[$flds['name']],0,$seq));
			$stmt->execute();
			$options[$data[$flds['id']]] = $dest->insertId();
		}
		else {
			if ($data[$flds['prod_id']] > 0)
				$dest->logMessage('conversion',sprintf("Missing product for prodops table [%s]",$data[$flds['prod_id']]),1);
		}
		$prev = $data[$flds['prod_id']];
	}	
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}
$file = 'ecom_countries.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	$stmt = $dest->prepare(sprintf('insert into countries(id,country,deleted) values(?,?,?)'));
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$seq = 0;
	$prev = 0;
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$stmt->bindParams(array('sss',$data[$flds['id']],$data[$flds['name']],$data[$flds['deleted']]));
		$stmt->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}
$file = 'ecom_provstate.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	$stmt = $dest->prepare(sprintf('insert into provinces(id,name,country_id,code,sort,deleted) values(?,?,?,?,?,?)'));
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$seq = 0;
	$prev = 0;
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$stmt->bindParams(array('ssssss',$data[$flds['id']],$data[$flds['name']],$data[$flds['country_id']],$data[$flds['code']],$data[$flds['sort']],$data[$flds['deleted']]));
		$stmt->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}

$file = 'ecom_orders.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	//$dest->execute('delete from orders');
	$stmt = $dest->prepare(sprintf('insert into orders(member_id,order_status,deleted,order_date,created,value,authorization_type,authorization_amount,authorization_code,authorization_info,shipping,total,discount_value,discount_rate) values(?,?,?,?,?,?,?,?,?,?,?,?,0,0)'));
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$seq = 0;
	$prev = 0;
	$orders = array();
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		if (array_key_exists($data[$flds['member_id']],$members)) {
			$stmt->bindParams(array('ssssssssssss',$members[$data[$flds['member_id']]],$data[$flds['status']],0,$data[$flds['created']],$data[$flds['created']],$data[$flds['order_total']],$data[$flds['paid_gateway']],$data[$flds['paid']],$data[$flds['paid_authcode']],$data[$flds['trans_dump']],$data[$flds['ship_total']],$data[$flds['order_total']]));
			$stmt->execute();
			$orders[$data[$flds['id']]] = $dest->insertId();
		}
		else 
			$dest->logMessage('conversion',sprintf('invalid member id [%d] on order [%d]<br/>',$data[$flds['member_id']],$data[$flds['id']]),1);
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$file = 'ecom_orderline.csv';
	$fh = fopen(sprintf('%s%s',$source,$file),'r');
	//$dest->execute('delete from orders');
	$stmt = $dest->prepare(sprintf('insert into order_lines(order_id,line_id,product_id,quantity,price,options_id) values(?,?,?,?,?,?)'));
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$orderline = array();
	$seq = 0;
	$prev = '';
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		if (array_key_exists($data[$flds['order_id']],$orders)) {
			if ($prev != '' && $prev != $data[$flds['order_id']]) $seq = 0;
			$seq += 1;
			if (!array_key_exists($data[$flds['prodopts_id']],$options)) {
				$dest->logMessage('conversion',sprintf("invalid options id [%d] on order line [%d]",$data[$flds['prodopts_id']],$data[$flds['id']]),1);
				$opt = 0;
			}
			else $opt = $options[$data[$flds['prodopts_id']]];
			$stmt->bindParams(array('dddddd',$orders[$data[$flds['order_id']]],$seq,$prod_ids[$data[$flds['product_id']]],$data[$flds['qty']],$data[$flds['product_price']],$opt));
			$stmt->execute();
			$orderline[$data[$flds['id']]] = $dest->insertId();
			$prev = $data[$flds['order_id']];
		}
		else 
			$dest->logMessage('conversion',sprintf('invalid order id [%d] on order line [%d]',$data[$flds['order_id']],$data[$flds['id']]),1);
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$file = 'ecom_shipaddr.csv';
	$fh = fopen(sprintf('%s%s',$source,$file),'r');
	$stmt = $dest->prepare(sprintf('insert into addresses(ownertype,ownerid,addresstype,tax_address,line1,line2,city,postalcode,province_id,country_id,deleted) values(?,?,?,?,?,?,?,?,?,?,?)'));
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$orderline = array();
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		if ($data[$flds['order_id']] > 0) {
			$stmt->bindParams(array('sssssssssss','order',$orders[$data[$flds['order_id']]],$addrType,1,$data[$flds['address1']],$data[$flds['address2']],$data[$flds['city']],$data[$flds['zippost']],$data[$flds['provstate_id']],$data[$flds['country_id']],0));
			$stmt->execute();
		}
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}

$file = 'news_cat.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$fldr_cat = array();
	$stmt = $dest->prepare(sprintf('insert into news_folders(left_id,right_id,level,title,template_id,enabled) values(?,?,?,?,?,?)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {	//,'"','\\')) {
		$stmt->bindParams(array('ssssdd',$data[$flds['left_id']],$data[$flds['right_id']],$data[$flds['level']],$data[$flds['title']],$template_cat[$data[$flds['templates_id']]],$data[$flds['enabled']]));
		$stmt->execute();
		$fldr_cat[$data[0]] = $dest->insertId();
	}
	$mptt = new mptt('news_folders');
	$fldr_cat[0] = $mptt->add(0,0,array('title'=>'Unallocated Articles','enabled'=>1));
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$file = 'news.csv';
	$fh = fopen(sprintf("%s%s",$source,$file),'r');
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$stmt = $dest->prepare(sprintf('insert into news(title,teaser,created,expires,image1,body,published,enabled) values(?,?,?,?,?,?,1,1)'));
	$fldr = $dest->prepare(sprintf('insert into news_by_folder(article_id,folder_id) values(?,?)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$data[$flds['image']] = normalize($data[$flds['image']]);
		$stmt->bindParams(array('ssssss',$data[$flds['title']],$data[$flds['short']],$data[$flds['created']],$data[$flds['expires']],$data[$flds['image']],$data[$flds['story']]));
		$stmt->execute();
		$ad = $dest->insertId();
		$fldr->bindParams(array('dd',$ad,$fldr_cat[$data[$flds['news_cat_id']]]));
		$fldr->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}

$file = 'store_cat.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$fldr_cat = array();
	$stmt = $dest->prepare(sprintf('insert into store_folders(left_id,right_id,level,title,enabled,image,description,notes,template_id) values(?,?,?,?,?,?,?,?,?)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {	//,'"','\\')) {
		$data[$flds['image']] = normalize($data[$flds['image']]);
		$stmt->bindParams(array('ssssssssd',$data[$flds['left_id']],$data[$flds['right_id']],$data[$flds['level']],$data[$flds['title']],$data[$flds['enabled']],$data[$flds['image']],$data[$flds['content']],$data[$flds['google_maps_route']],$template_cat[$data[$flds['page']]]));
		$stmt->execute();
		$fldr_cat[$data[0]] = $dest->insertId();
	}
	$mptt = new mptt('store_folders');
	$fldr_cat[0] = $mptt->add(0,0,array('title'=>'Unallocated Stores'));
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$file = 'stores.csv';
	$fh = fopen(sprintf("%s%s",$source,$file),'r');
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$stmt = $dest->prepare(sprintf('insert into stores(name,description,latitude,longitude,image1,image2,image3,image4,mapmarker,website,published,enabled) values(?,?,?,?,?,?,?,?,?,?,1,1)'));
	$address = $dest->prepare('insert into addresses(ownerid,addresstype,line1,line2,city,postalcode,province_id,country_id,phone1,fax,email,deleted,tax_address,ownertype) values(?,?,?,?,?,?,?,?,?,?,?,0,0,"store")');
	$stores = array();
	$addrType = $dest->fetchScalar('select id from code_lookups where type="storeAddressTypes" limit 1');
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		$data[$flds['image']] = normalize($data[$flds['image']]);
		$data[$flds['image2']] = normalize($data[$flds['image2']]);
		$data[$flds['image3']] = normalize($data[$flds['image3']]);
		$data[$flds['image4']] = normalize($data[$flds['image4']]);
		$data[$flds['mapimage']] = normalize($data[$flds['mapimage']]);
		$stmt->bindParams(array('ssssssssss',$data[$flds['name']],$data[$flds['content']],$data[$flds['latitude']],$data[$flds['longitude']],$data[$flds['image']],$data[$flds['image2']],$data[$flds['image3']],$data[$flds['image4']],$data[$flds['mapimage']],$data[$flds['url']]));
		$stmt->execute();
		$stores[$data[$flds['id']]] = $dest->insertId();
		$address->bindParams(array('sssssssssss',$stores[$data[$flds['id']]],$addrType,$data[$flds['address1']],$data[$flds['address2']],$data[$flds['city']],$data[$flds['zippost']],$data[$flds['provstate']],$data[$flds['country']],$data[$flds['phone1']],$data[$flds['fax1']],$data[$flds['email']]));
		$address->execute();
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$file = 'stores_cat.csv';
	$fh = fopen(sprintf("%s%s",$source,$file),'r');
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$fldr = $dest->prepare(sprintf('insert into stores_by_folder(store_id,folder_id) values(?,?)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		if (array_key_exists($data[$flds['store_id']],$stores)) {
			$fldr->bindParams(array('ss',$stores[$data[$flds['store_id']]],$fldr_cat[$data[$flds['cat_id']]]));
			$fldr->execute();
		}
		else 
			$dest->logMessage('conversion',sprintf('invalid store id [%d] found on stores_cat [%d]',$data[$flds['store_id']],$data[$flds['id']]),1);
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}


$file = 'cat.csv';
if (file_exists(sprintf('%s%s',$source,$file)) && $fh = fopen(sprintf('%s%s',$source,$file),'r')) {
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$fldr_cat = array();
	$stmt = $dest->prepare(sprintf('insert into content(title,alternate_title,left_id,right_id,type,level,image,rollover_image,search_title,internal_link,external_link,new_window,published,enabled) values(?,?,?,?,?,?,?,?,?,?,?,?,1,1)'));
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		if (strlen($data[$flds['search_title']]) == 0) {
			if (($alt = $data[$flds['alt_title']]) != '')
				$data[$flds['search_title']] = preg_replace('#[^a-z0-9_]#i', '-', strtolower($data[$flds['title']].' '.$data[$flds['alt_title']]));
			else
				$data[$flds['search_title']] = preg_replace('#[^a-z0-9_]#i', '-', strtolower($data[$flds['title']]));
		}
		$data[$flds['image']] = normalize($data[$flds['image']]);
		$data[$flds['roimage']] = normalize($data[$flds['roimage']]);
		switch($data[$flds['type']]) {
			case 'folder':
				break;
			case 'content':
				$data[$flds['type']] = 'page';
				break;
			case 'hardlink':
				$data[$flds['type']] = 'externallink';
				break;
			case 'softlink':
				$data[$flds['type']] = 'internallink';
				break;
			default:
				break;
		} 
		$stmt->bindParams(array('ssssssssssss',$data[$flds['title']],$data[$flds['alt_title']],$data[$flds['left_id']],$data[$flds['right_id']],$data[$flds['type']],$data[$flds['level']],
				$data[$flds['image']],$data[$flds['roimage']],$data[$flds['search_title']],$data[$flds['softlink']],$data[$flds['hardlink']],$data[$flds['newwindow']]));
		$stmt->execute();
		$fldr_cat[$data[$flds['id']]] = $dest->insertId();
	}
	$recs = $dest->fetchAll('select * from content where internal_link > 0');
	foreach($recs as $rec) {
		$dest->execute(sprintf('update content set internal_link = %d where id = %d',$fldr_cat[$rec['internal_link']],$rec['id']));
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
	$file = 'page.csv';
	$fh = fopen(sprintf('%s%s',$source,$file),'r');
	$tmp = fgetcsv($fh,0,'~','"','\\');
	$flds = array();
	foreach($tmp as $key=>$value) {
		$flds[$value] = $key;
	}
	$stmt = $dest->prepare(sprintf('insert into pages(template_id,content_id,version,deleted,content) values(?,?,?,?,?)'));
	$upd = $dest->prepare('update pages set content = ?, template_id = ? where id = ?');
	while($data = fgetcsv($fh,0,'~','"','\\')) {
		if (array_key_exists($data[$flds['cat_id']],$fldr_cat)) {
			if (!$p = $dest->fetchSingle(sprintf('select * from pages where content_id = %d',$fldr_cat[$data[$flds['cat_id']]]))) {
				$stmt->bindParams(array('sssss',$template_cat[$data[$flds['templates_id']]],$fldr_cat[$data[$flds['cat_id']]],1,0,$data[$flds['content']]));
				$stmt->execute();
				$p = $dest->fetchSingle(sprintf('select * from pages where content_id = %d',$fldr_cat[$data[$flds['cat_id']]]));
			}
			$upd->bindParams(array('sdd',$data[$flds['content']],$template_cat[$data[$flds['templates_id']]],$p['id']));
			$upd->execute();
		}
		else
			$dest->logMessage('conversion',sprintf('deleted content [%d] for page [%d]?',$data[$flds['cat_id']],$data[$flds['id']]),1);
	}
	fclose($fh);
	rename(sprintf('%s%s',$source,$file),sprintf('%sconverted/%s',$source,$file));
}

$et = microtime();
//$dest->commitTransaction();
echo sprintf('<!-- render time is %f seconds -->',$et - $st).PHP_EOL;

function clearDatabase($dest) {
}

function normalize($img) {
	if (strpos($img,'images') !== false && substr($img,0,1) != '/') {
		$img = '/'.$img;
	}
	if ($img == '/images/unknown.jpg' || $img == '/images/unknown.gif') $img = '';
	return $img;
}
?>
