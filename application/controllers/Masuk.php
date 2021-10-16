<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Masuk extends CI_Controller {

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
			}elseif ($recaptcha->score >= 0.5) {
				$this->load->model('sekolah_model');
				$this->sekolah_model->masuk_dasbor($this->input->post());
			} else {
				captcha_false:
				$this->session->set_flashdata('error', 'Sistem mendeteksi tindakan mencurigakan, silahkan coba lagi.');
				redirect('masuk');
			}
		}elseif (isset($this->session->userdata['sesi_login']) && isset($this->session->userdata['sesi_login']->id_sekolah)) {
			redirect('dasbor/utama');
		}
		$this->load->view('masuk');
	}
}