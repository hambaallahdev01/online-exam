<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	public function insert_siswa($data)
	{
		$this->load->database();
		$data['password'] = md5($data['password']);
		$this->db->insert('siswa', $data);
		return $this->db->insert_id();
	}
}