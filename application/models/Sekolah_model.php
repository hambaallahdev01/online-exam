<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sekolah_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	private function is_kode_sekolah_exist($ks)
	{
		$this->dbselect = $this->load->database('select', TRUE);
		$this->dbselect->select('id_sekolah');
		$this->dbselect->where('kode_sekolah', $ks);
		$row = $this->dbselect->get('sekolah')->row();
		if (isset($row->id_sekolah)) {
			return true;
		}else{
			return false;
		}
	}

	function mendaftar($data)
	{
		$is_kode_sekolah_exist = $this->is_kode_sekolah_exist($data['ks']);
		if ($is_kode_sekolah_exist) {
			$this->session->set_flashdata('error', 'Kode Sekolah sudah digunakan, silahkan coba kode sekolah yang lain.');
			redirect('daftar');
		}
		$this->load->database();
		$this->db->insert('sekolah', [
			'kode_sekolah' 	=> $data['ks'],
			'email' 		=> $data['email'],
			'nama_sekolah'	=> $data['ns'],
			'password'		=> md5($data['password'])
		]);
		if ($this->db->insert_id()) {
			$this->session->set_flashdata('nama_sekolah', $data['ns']);
			redirect('daftar/sukses');
		}
		redirect('daftar/gagal');
	}

	function masuk_dasbor($data)
	{
		$this->dbselect = $this->load->database('select', true);
		$this->dbselect->select('id_sekolah, nama_sekolah');
		$this->dbselect->from('sekolah');
		$this->dbselect->where([
			'password' 		=> md5($data['password']),
			'email'			=> $data['email'],
			'kode_sekolah'	=> $data['ks']
		]);
		$row = $this->dbselect->get()->row();
		if (isset($row->id_sekolah)) {
			$this->session->set_userdata('sesi_login', $row);
			redirect('dasbor/utama');
		}
		$this->session->set_flashdata('error', 'Kode Sekolah salah. Silahkan mendaftarkan diri terlebih dahulu.');
		redirect('masuk');
	}
}