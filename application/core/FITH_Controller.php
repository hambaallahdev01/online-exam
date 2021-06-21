<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FITH_Controller extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	public function json($val)
	{
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode($val));
	}
}

class Admin_Controller extends FITH_Controller {

	function __construct(){
		parent::__construct();
		if (!$this->session->sesi_login) {
			redirect('welcome');
		}
		$this->data = array();
		$this->method = $_SERVER['REQUEST_METHOD'];
	}
}