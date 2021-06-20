<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_fee extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('admin_fee_model');
	}

	function index()
	{
		$this->breadcrumb->add('Home', base_url());
		$this->breadcrumb->add('PPOB Admin Fee', base_url(strtolower($this->router->class)));
		$data['active_link'] = '';
		$data['list_product_lain'] = $this->admin_fee_model->get_product_lain();
		load_view('admin_fee/index.php', $data);
	}

	function admin_fee_add()
	{
		if ($this->method == 'POST') {
			$data_add = $this->input->post();
			unset($data_add['id_admin_fee']);
			$this->admin_fee_model->insert_admin_fee($data_add);
			redirect('admin_fee');
		}else{
			show_404();
		}
	}

	function admin_fee_edit()
	{
		if ($this->method == 'POST') {
			$id_admin_fee = $this->input->post('id_admin_fee');
			$data_update = $this->input->post();
			unset($data_update['id_admin_fee']);
			$this->admin_fee_model->update_admin_fee($id_admin_fee, $data_update);
			redirect('admin_fee');
		}else{
			show_404();
		}
	}

	function admin_fee_delete()
	{
		if ($this->method == 'GET') {
			$id_admin_fee = $this->input->get('id_admin_fee');
			$this->admin_fee_model->delete_admin_fee($id_admin_fee);
		}else{
			show_404();
		}
	}

	function admin_fee_datatable()
	{
		$select = 'af.id_admin_fee, af.id_product, af.fee, ap.label, ap.operator, ap.nominal';
		$list = $this->admin_fee_model->get_admin_fee(false, $select);
		$data = array();
		$no = $this->input->post('start');
		foreach ($list->data as $value) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $value->label;
			$row[] = $value->operator;
			$row[] = $value->nominal;
			$row[] = $value->fee;
			$button = '<a href="#" data-toggle="modal" data-id="'.$value->id_admin_fee.'" data-target="#edit-modal"> <i class="icon-pencil"></i> </a>
			<a href="#" onclick="delete_data('.$value->id_admin_fee.')"> <i class="icon-trash"></i> </a>
			';
			$row[] = $button;

			$data[] = $row;
		}
		_datatable_output($data, $list->recordsTotal, $list->recordsFiltered);
	}

	function admin_fee_detail($id_admin_fee)
	{
		$select = '*';
		$row = $this->admin_fee_model->get_admin_fee($id_admin_fee, $select);
		echo json_encode($row);
	}
}