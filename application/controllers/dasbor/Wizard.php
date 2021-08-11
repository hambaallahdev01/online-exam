<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wizard extends Wizard_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		if($this->session->wizard == 'data-sekolah'){
			$this->load->model('dasbor/pengaturan/akun_model');
			$data = $this->akun_model->get_detail_sekolah($this->session->sesi_login->id_sekolah);
			$this->load->model('alamat_model');
			$data->list_provinsi = $this->alamat_model->get_list_provinsi();
			$data->list_kabupaten = $this->alamat_model->get_list_kabupaten(1);
			$data->list_kecamatan = $this->alamat_model->get_list_kecamatan(1);
			$this->load->view('dasbor/wizard/sekolah', $data);
		}
	}
}