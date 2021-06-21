<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Masuk_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	function masuk_dasbor($data)
	{
		$this->is_kode_sekolah_exist_masuk($data['ks']);
		$this->load->database();
		$this->db->select('id_sekolah, nama_sekolah');
		$this->db->from('sekolah');
		$this->db->where([
			'password' 		=> md5($data['password']),
			'email'			=> $data['email'],
			'kode_sekolah'	=> $data['ks']
		]);
		$row = $this->db->get()->row();
		if (isset($row->id_sekolah)) {
			$this->session->set_userdata('sesi_login', $row);
			redirect('dasbor/utama');
		}
		$this->session->set_flashdata('error', 'Anda memasukkan data yang salah ketika proses masuk.');
		redirect('masuk');
	}

	private function is_kode_sekolah_exist_masuk($ks)
	{
		$this->dbfast = $this->load->database('fast', TRUE);
		$this->dbfast->select('id_sekolah');
		$this->dbfast->where('kode_sekolah', $ks);
		$row = $this->dbfast->get('sekolah')->row();
		if (!isset($row->id_sekolah)) {
			$this->session->set_flashdata('error', 'Kode Sekolah salah. Silahkan mendaftarkan diri dahulu');
			redirect('masuk');
		}
	}
}