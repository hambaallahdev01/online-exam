<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Akun extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			redirect('dasbor/pengaturan/akun');
		}
		$this->load->model('dasbor/pengaturan/akun_model');
		$data = $this->akun_model->get_detail_sekolah($this->session->sesi_login->id_sekolah);
		$this->load->view('dasbor/pengaturan/akun', $data);
	}
}