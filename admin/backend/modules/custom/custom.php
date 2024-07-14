<?php

class custom extends Backend {

	public function __construct() {
		$this->M_DIR = 'backend/modules/custom/';
		parent::__construct ();
	}

	public function galleryFolder() {
		return array(
			'form'=>file_get_contents($this->M_DIR.'forms/galleryFolder.html'),
			'description'=>'Custom Data',
			'fields'=>array(
				'custom_grid_size'=>array('type'=>'number','required'=>true,'value'=>'',"min"=>"0","max"=>"12")
			)
		);
	}

	function memberDisplay() {
		return array(
			'form'=>file_get_contents($this->M_DIR.'forms/member.html'),
			'description'=>'Custom',
			'fields'=>array(
				'custom_sales_admin'=>array('type'=>'checkbox','required'=>false,'value'=>1)
			)
		);
	}

	function menuFolderDisplay() {
		return array(
			'form'=>file_get_contents($this->M_DIR.'forms/menuFolder.html'),
			'description'=>'Custom',
			'fields'=>array(
				'custom_slider_data_1'=>array('type'=>'textarea','required'=>false),
				'custom_slider_data_2'=>array('type'=>'textarea','required'=>false),
				'custom_slider_main_1'=>array('type'=>'textfield','required'=>false),
				'custom_slider_main_2'=>array('type'=>'textfield','required'=>false),
				'custom_slider_sub_1'=>array('type'=>'textfield','required'=>false),
				'custom_slider_sub_2'=>array('type'=>'textfield','required'=>false),
				'custom_slider_button_text'=>array('type'=>'textfield','required'=>false),
			)
		);
	}

	function orderDisplay() {
		return array(
			'form'=>file_get_contents($this->M_DIR.'forms/order.html'),
			'description'=>'Custom',
			'fields'=>array(
				'custom_note'=>array('type'=>'textarea'),
				'custom_image_1'=>array('type'=>'tag','required'=>false),
				'custom_image_2'=>array('type'=>'tag','required'=>false),
				'custom_image_3'=>array('type'=>'tag','required'=>false),
				'custom_image_4'=>array('type'=>'tag','required'=>false)
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