<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manager extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('admin_user')) {
            redirect('/auth');
        } else {
            $this->admin_user = $this->session->userdata('admin_user');
        }
		$this->load->model('model_user');
		$this->load->model('model_camera');
		$this->load->model('model_sub');
		$this->load->model('model_todo');
		$this->load->model('model_user_usage');
		$this->load->model('model_livecamera');
		$this->load->model('model_history_camera');
		$this->load->model('model_att');
		$this->load->model('model_verizon');
		$this->load->model('model_hologram');
		$this->load->model('model_pro_manager');
	}

	public function __prefix($page, $data = array())
	{
		if($this->session->userdata('admin_user'))
			$data['admin_user'] = $this->session->userdata('admin_user');
		$this->load->view('templates/header', $data);
		$this->load->view($page, $data);
		$this->load->view('templates/footer', $data);
	}

	public function index()
	{
        $this->viewManagerList();
	}

    public function viewManagerList()
	{
		$data['page'] = 'manager/viewManagerList';
		$data['title'] = 'ManagerList';
		$this->__prefix('pages/manager/viewManagerList', $data);
	}

    public function viewEdit($id)
    {
        $data['data'] = $this->model_pro_manager->getById($id);
		$user = $this->model_user->getById($data['data']['user_id']);
		$data['data']['email'] = $user['email'];
		$data['data']['password'] = $user['password'];
        $data['page'] = 'manager/viewEdit';
		$data['title'] = 'ManagerEdit';
		$this->__prefix('pages/manager/viewEdit', $data); 
    }

	public function viewManagerAdd()
	{
		$data['page'] = 'manager/viewManagerAdd';
		$data['title'] = 'Add Manager';
		$this->__prefix('pages/manager/viewManagerAdd', $data);
	}

    public function getManagerData()
	{
		$order_i = $this->input->get_post("order[0][column]");
		$order_field = $this->input->get_post("columns[" . $order_i . "][data]");
		$order_dir = $this->input->get_post("order[0][dir]");
		$length = $this->input->get_post("length");
		$start = $this->input->get_post("start");
		$searchval = $this->input->get_post("search[value]");

		$draw = $this->input->get_post("draw");

		$filteropt = array(
			"order_i" => $order_i,
			"order_field" => $order_field,
			"order_dir" => $order_dir,
			"length" => $length,
			"start" => $start,
			"search" => $searchval,
		);

		$total_count = $this->model_pro_manager->getTotalCount($filteropt);
		$data = $this->model_pro_manager->getSearchData($filteropt);
		$users = array();
		foreach ($data as $item) {
			$user = $this->model_user->getById($item['user_id']);
			$users[] = array(
				'id' => $item['id'],
				'email' => $user['email'],
				'password' => $user['password'],
				'created_at' => $item['created_at'],
			);
		}

		$senddata = array(
			"draw" => $draw,
			"recordsTotal" => $total_count,
			"iTotalDisplayRecords" => $total_count,
			"recordsFiltered" => count($users),
			"data" => $users
		);
		echo json_encode($senddata);
	}

    public function delete($id)
	{
		$this->db->where("id", $id);
		$this->db->delete($this->table);
	}

    public function edit()
    {
        $id = $this->input->get_post("id");
        $email = $this->input->get_post("email");
        $password = $this->input->get_post("password");
        $org_id = $this->input->get_post("org_id");
        $this->model_pro_manager->update_manager($id, [
            'email' => $email,
            'password' => $password,
            'org_id' => $org_id,
        ]);
        echo "ok";
    }

	public function createSpecial()
	{
		$data = array(
			'name' => $this->input->get_post("username"),
			'email' => $this->input->get_post('email'),
			'password' => $this->input->get_post('password'),
			'account_type' => $this->input->get_post('account_type')
		);

		$result = $this->model_user->create($data);

		$response = array(
			'result' => 'ok',
			'message' => $result,
		);
		if ($result != 'success') {
			$response = array(
				'result' => 'no',
				'message' => $result,
			);
		}
		echo json_encode($response);
	}

}
?>