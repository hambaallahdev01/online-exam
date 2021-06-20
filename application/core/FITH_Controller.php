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
		if (!$this->session->logged_in) {
			show_404();
		}
		$this->data = array();
		$this->data['active_link'] = beuty_url(base_url($this->router->class));
		$this->method = $_SERVER['REQUEST_METHOD'];
	}
}