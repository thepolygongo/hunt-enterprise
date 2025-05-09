<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_user');
		$this->load->model('model_pro_manager');
	}

	public function index()
	{
		$logged = $this->session->userdata('admin_user');
		if (empty($logged)) {
			$this->logged = 0;
		} else {
			$this->logged = 1;
		}

		if ($this->logged == 0) {
			$data = array(
				'title' => SITE_TITLE
			);
			$this->load->view('auth/login', $data);
		} else {
            redirect('user/viewUserList');
		}
	}

	public function login()
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$admin_user = $this->model_user->authenticate($email, $password);
		if(is_array($admin_user) && !empty($admin_user)){
			$this->session->set_userdata('admin_user', $admin_user);
			redirect('user/viewUserList');
		} else {
			redirect('auth');
		}
	}

	public function logout()
	{
		$this->session->unset_userdata('admin_user');
		$this->session->sess_destroy();
		redirect('/', 'refresh');
	}
}
