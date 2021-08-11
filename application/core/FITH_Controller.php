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
		$this->wizard_data_sekolah();
		$this->data = array();
		$this->method = $_SERVER['REQUEST_METHOD'];
	}

	private function wizard_data_sekolah()
	{
		$this->dbselect = $this->load->database('select', TRUE);
		$this->dbselect->select('id_kecamatan');
		$this->dbselect->where('id_sekolah', $this->session->sesi_login->id_sekolah);
		$row = $this->dbselect->get('sekolah')->row();
		if (is_null($row->id_kecamatan)) {
			$this->session->set_userdata('wizard', 'data-sekolah');
			redirect('dasbor/wizard');
		}
	}
}

class Wizard_Controller extends FITH_Controller {

	function __construct(){
		parent::__construct();
		if (!$this->session->sesi_login) {
			redirect('welcome');
		}
		$this->data = array();
		$this->method = $_SERVER['REQUEST_METHOD'];
	}
}