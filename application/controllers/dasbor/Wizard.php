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
		}elseif ($this->session->wizard == 'tahun-ajaran') {
			$this->wizard_tahun_ajaran();
		}elseif ($this->session->wizard == 'kelas') {
			$this->wizard_kelas();
		}elseif ($this->session->wizard == 'mata-pelajaran') {
			$this->wizard_mata_pelajaran();
		}else{
			show_404();
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
				$this->session->set_userdata('wizard', 'tahun-ajaran');
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

	private function wizard_tahun_ajaran()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			$tanggal_awal_ganjil = $this->input->post('tanggal_awal_ganjil');
			$tanggal_akhir_ganjil = $this->input->post('tanggal_akhir_ganjil');
			$tanggal_awal_genap = $this->input->post('tanggal_awal_genap');
			$tanggal_akhir_genap = $this->input->post('tanggal_akhir_genap');
			if ($tanggal_awal_ganjil < $tanggal_akhir_ganjil && $tanggal_akhir_ganjil < $tanggal_awal_genap && $tanggal_awal_genap < $tanggal_akhir_genap) {
				$this->load->model('dasbor/pengaturan/tahun_ajaran_model');
				$this->tahun_ajaran_model->insert_tahun_ajaran([
					'id_sekolah'			=> $this->session->sesi_login->id_sekolah,
					'tanggal_awal_ganjil' 	=> $tanggal_awal_ganjil,
					'tanggal_akhir_ganjil' 	=> $tanggal_akhir_ganjil,
					'tanggal_awal_genap' 	=> $tanggal_awal_genap,
					'tanggal_akhir_genap' 	=> $tanggal_akhir_genap,
					'is_active' 			=> 1
				]);
				$this->session->set_userdata('wizard', 'kelas');
				redirect('dasbor/wizard');
			}
		}
		$this->load->view('dasbor/wizard/tahun_ajaran');
	}

	private function wizard_kelas()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			if ($jenjang = $this->input->post('jenjang') && $nama_kelas = $this->input->post('nama_kelas')) {
				$this->load->model('dasbor/pengaturan/kelas_model');
				$this->kelas_model->insert_kelas([
					'id_sekolah'	=> $this->session->sesi_login->id_sekolah,
					'jenjang' 		=> $jenjang,
					'nama_kelas' 	=> $nama_kelas
				]);
				$this->session->set_userdata('wizard', 'mata-pelajaran');
				redirect('dasbor/wizard');
			}
		}
		$this->load->view('dasbor/wizard/kelas');
	}

	private function wizard_mata_pelajaran()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
		}
		$this->load->view('dasbor/wizard/mata_pelajaran');
	}
}