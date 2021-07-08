<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Files extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function _remap($method){
		switch ($method) {
			default :
			$this->index();
			break;
		}
	}
	
	function index(){
		$slug = str_replace('files/', '', $this->uri->uri_string());
		$this->load->helper('s3_helper');
		s3_getObject($slug);
	}
}