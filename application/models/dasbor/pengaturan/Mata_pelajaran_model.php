<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mata_pelajaran_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	public function insert_mapel($data)
	{
		$this->load->database();
		$this->db->insert('mata_pelajaran', $data);
		return $this->db->insert_id();
	}
}