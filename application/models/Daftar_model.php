<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daftar_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	function mendaftar($data)
	{
		$this->is_kode_sekolah_exist($data['ks']);
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

	private function is_kode_sekolah_exist($ks)
	{
		$this->dbselect = $this->load->database('select', TRUE);
		$this->dbselect->select('id_sekolah');
		$this->dbselect->where('kode_sekolah', $ks);
		$row = $this->dbselect->get('sekolah')->row();
		if (isset($row->id_sekolah)) {
			$this->session->set_flashdata('error', 'Kode Sekolah sudah digunakan, silahkan coba kode sekolah yang lain.');
			redirect('daftar');
		}
	}
}