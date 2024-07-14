<?php

class custom extends Backend {

	public function __construct() {
		$this->M_DIR = 'backend/modules/custom/';
		parent::__construct ();
	}

	public function memberDisplay() {
		return array(
			'form'=>file_get_contents($this->M_DIR.'forms/member.html'),
			'description'=>'Custom Info',
			'fields'=>array(
				'haveTicket'=>array('type'=>'select','lookup'=>'boolean','value'=>'0','required'=>true),
				'attendTour'=>array('type'=>'select','lookup'=>'boolean','value'=>'0','required'=>true),
				'attendSunLife'=>array('type'=>'select','lookup'=>'boolean','value'=>'0','required'=>true)
			)
		);
	}

	public function xxadvertDisplay() {
		return array(
			'form'=>file_get_contents($this->M_DIR.'forms/advert.html'),
			'description'=>'Background Color',
			'fields'=>array(
				'custom_bg_color'=>array('type'=>'textfield','required'=>false,'value'=>'')
			)
		);
	}

	function xxnewsFolderDisplay() {
		return array(
			'form'=>file_get_contents($this->M_DIR.'forms/newsFolder.html'),
			'description'=>'Custom',
			'fields'=>array(
				'custom_class_name'=>array('type'=>'textfield','required'=>false)
			)
		);
	}

	function xxmenuFolderDisplay() {
		return array(
			'form'=>file_get_contents($this->M_DIR.'forms/menuFolder.html'),
			'description'=>'Custom',
			'fields'=>array(
				'custom_isotope_type'=>array('type'=>'textfield','required'=>false)
			)
		);
	}

	public function postRecalc($cart) {
		return $cart;
	}

	public function preRecalc($cart) {
		$cart['header']['freeShipping'] = 0;
		return $cart;
	}

	function calcShipping($cart) {
		return $cart;
	}

	function initCart($cart) {
		$cart['header']['freeShipping'] = 0;
		return $cart;
	}

}

?>