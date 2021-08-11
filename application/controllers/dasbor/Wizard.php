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
			$this->wizard_alamat();
		}elseif ($this->session->wizard == 'tahun-pelajaran') {
			$this->wizard_tahun_pelajaran();
		}
	}

	private function wizard_alamat()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			if ($id_kecamatan = $this->input->post('id_kecamatan') && $alamat = $this->input->post('alamat')) {
				$this->load->model('dasbor/pengaturan/akun_model');
				$this->akun_model->update_detail_sekolah($this->session->sesi_login->id_sekolah, [
					'id_kecamatan' => $id_kecamatan,
					'alamat' => $alamat
				]);
				$this->session->set_userdata('wizard', 'tahun-pelajaran');
				redirect('dasbor/wizard');
			}
		}
		$this->load->model('dasbor/pengaturan/akun_model');
		$data = $this->akun_model->get_detail_sekolah($this->session->sesi_login->id_sekolah);
		$this->load->model('alamat_model');
		$data->list_provinsi = $this->alamat_model->get_list_provinsi();
		$data->list_kabupaten = $this->alamat_model->get_list_kabupaten(1);
		$data->list_kecamatan = $this->alamat_model->get_list_kecamatan(1);
		$this->load->view('dasbor/wizard/sekolah', $data);
	}

	private function wizard_tahun_pelajaran()
	{
		echo 'ok';
	}
}