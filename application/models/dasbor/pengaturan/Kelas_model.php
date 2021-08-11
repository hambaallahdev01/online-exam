<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	public function insert_kelas($data)
	{
		$this->load->database();
		$this->db->insert('kelas', $data);
		return $this->db->insert_id();
	}
}