<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daftar extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->config('recaptcha', TRUE);
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->input->post('recaptcha_response')) {
			$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
			$recaptcha_secret = $this->config->item('recaptcha_secretkey', 'recaptcha');
			$recaptcha_response = $this->input->post('recaptcha_response');

			$recaptcha = file_get_contents_curl($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
			$recaptcha = json_decode($recaptcha);
			if (!isset($recaptcha->score)) {
				goto captcha_false;
			} elseif ($recaptcha->score >= 0.5) {
				$this->load->model('daftar_model');
				$this->daftar_model->mendaftar($this->input->post());
			} else {
				captcha_false:
				$this->session->set_flashdata('error', 'Sistem mendeteksi tindakan mencurigakan, silahkan coba lagi.');
				redirect('daftar/gagal');
			}
		}elseif (isset($this->session->userdata['sess_logged_in']) && $this->session->userdata['sess_logged_in'] == 1) {
			//jika ada sesi login, redirect
		}
		$this->load->view('daftar');
	}

	public function sukses()
	{
		$this->load->view('daftar_sukses');
	}

	public function gagal()
	{
		$this->load->view('daftar_gagal');
	}
}