<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_fee_model extends CI_Model{

	function get_admin_fee($id_admin_fee = false, $select = null)
	{
		if ($id_admin_fee) {
			$this->db->select($select);
			$this->db->where('id_admin_fee', $id_admin_fee);
			$query = $this->db->get('admin_fee');
			return $query->row();
		}
		$this->_get_admin_fee($select);
		if($this->input->post('length') != -1){
			$this->db->limit($this->input->post('length'), $this->input->post('start'));
		}
		$query = $this->db->get();
		$return = new stdClass();
		$return->data = $query->result();
		$return->recordsTotal = $this->recordsTotal_admin_fee();
		$return->recordsFiltered = $this->recordsFiltered_admin_fee($select);
		return $return;
	}
	private function _get_admin_fee($select)
	{
		$column_order = array(null, 'ap.label', 'ap.operator', 'ap.nominal', 'af.fee', null);
		$column_search = array('af.id_product', 'fee');
		$order = array('af.first_created' => 'asc');
		if ($select) {
			$this->db->select($select);
			$column_search = str_replace(' ', '', $select);
			$column_search = explode(',', $column_search);
		}
		$this->db->from('admin_fee af');
		$this->db->join('alterra_product ap', 'ap.product_id = af.id_product', 'LEFT');
		_datatable_search($column_search, $column_order, $order);
	}
	private function recordsFiltered_admin_fee($select)
	{
		$this->_get_admin_fee($select);
		$query = $this->db->get();
		return $query->num_rows();
	}
	private function recordsTotal_admin_fee()
	{
		$this->db->from('admin_fee');
		return $this->db->count_all_results();
	}

	function insert_admin_fee($data)
	{
		$this->db->insert('admin_fee', $data);
		return $this->db->insert_id();
	}

	function update_admin_fee($id_admin_fee, $data)
	{
		$this->db->set($data);
		$this->db->where('id_admin_fee', $id_admin_fee);
		$this->db->update('admin_fee');
		return $this->db->affected_rows();
	}

	function delete_admin_fee($id_admin_fee)
	{
		$this->db->where('id_admin_fee', $id_admin_fee);
		$this->db->delete('admin_fee');
		return $this->db->affected_rows();
	}

	function get_product_lain()
	{
		$query = $this->db->select('product_id, label, operator, nominal')->where('`product_id` NOT IN (SELECT id_product FROM admin_fee)', NULL, FALSE)->order_by('label')->get('alterra_product');
		return $query->result();
	}
}