<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

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
		$this->load->model('model_pro_camera');
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
        $this->viewUserList();
	}

	public function viewUserList()
	{
		$data['page'] = 'user/viewUserList';
		$data['title'] = 'UserList';
		$this->__prefix('pages/user/viewUserList', $data);
	}

	public function viewUserDetail($id)
	{
		$user = $this->model_user->getById($id);
		$users = $this->model_user->getAllSortByEmail();
		$sku = parse_sku($user['jwt_sub_sku']);
		$user['jwt_sub_plan'] = $sku['plan'];
		$user['jwt_sub_period'] = $sku['period'];
		$user['jwt_sub_quantity'] = $sku['quantity'];
		$admin_user = $this->session->userdata('admin_user');
		if ($admin_user['account_type'] == 'pro_organization') {
			$org_id = $admin_user['id'];
		} else if ($admin_user['account_type'] == 'pro_manager') {
			$manager = $this->model_pro_manager->getUserId($admin_user['id']);
			$org_id = $manager['org_id'];
		}
		$page = 'pages/user/viewUserDetail';
		$data = array(
			"page" => $page,
			"title" => "User Detail",
			"user" => $user,
			"users" => $users,
			"org_id" => $org_id,
		);
		$this->__prefix($page, $data);
	}

	public function viewUserAdd()
	{
		$data['page'] = 'user/viewUserAdd';
		$data['title'] = 'Add User';
		$this->__prefix('pages/user/viewUserAdd', $data);
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
        $this->model_user->update_user($id, [
            'email' => $email,
            'password' => $password,
            'org_id' => $org_id,
        ]);
        echo "ok";
    }

	public function getSearchData()
	{
		$order_i = $this->input->get_post("order[0][column]");
		$order_field = $this->input->get_post("columns[" . $order_i . "][data]");
		$order_dir = $this->input->get_post("order[0][dir]");
		$length = $this->input->get_post("length");
		$start = $this->input->get_post("start");
		$searchval = $this->input->get_post("search[value]");

		$draw = $this->input->get_post("draw");
		// $account_type = $this->input->get_post("account_type");

		$filteropt = array(
			"order_i" => $order_i,
			"order_field" => $order_field,
			"order_dir" => $order_dir,
			"length" => $length,
			"start" => $start,
			"search" => $searchval,
			// "account_type" => $account_type,
		);

		$total_count = $this->model_user->getTotalCount($filteropt);
		$data = $this->model_user->getSearchData($filteropt);
		$users = array();
		// for ($i = 0; $i < sizeof($users); $i++) {
		// 	$users[$i]['active_cameras'] = $this->model_camera->count_smart_active($users[$i]['id']);
		// }
		$users = array();
		foreach ($data as $item) {
			$user = $this->model_user->getById($item['user_id']);
			if(!is_array($user) && empty($user))
				continue;
			$manager = $this->model_user->getById($item['manager_id']);
			if(!is_array($manager) && empty($manager))
				continue;
			$organization = $this->model_user->getById($item['org_id']);
			if(!is_array($organization) && empty($organization))
				continue;
			$users[] = array(
				'id' => $item['id'],
				'user_id' => $item['user_id'],
				'name' => $user['name'],
				'email' => $user['email'],
				'manager' => $manager['email'],
				'organization' => $organization['email'],
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

	public function changePassword()
	{
		$id = $this->input->get_post('id');
		$password = $this->input->get_post('password');

		$item = $this->model_user->getById($id);
		$item['password'] = $password;

		$this->model_user->replaceData($item);

		echo "success";
	}

	public function get_total_usage()
	{
		$user_id = $this->input->get_post('user_id');
		$user = $this->model_user->getById($user_id);
		$current_usage = $user['data_usage'];
		$usage_limitation = $this->model_user_usage->limitation_by_user($user);
		$senddata = array(
			'current_usage' => $current_usage,
			'usage_limitation' => $usage_limitation,
		);
		echo json_encode($senddata);
	}

	public function datausage_reset()
	{
		$user_id = $this->input->get_post('user_id');
		$user = $this->model_user->getById($user_id);
		$this->model_user_usage->datausage_clear_one($user_id);

		echo "Success";
	}

	public function create_token()
	{
		$user_id = $this->input->get_post('user_id');
		$user = $this->model_user->getById($user_id);

		if ($user['token'] == '') {
			$tokenData = array();
			$tokenData['user_login'] = $user['name'];
			$tokenData['user_pass'] = $user['password'];
			$token = AUTHORIZATION::generateToken($tokenData);
			$user['token'] = $token;
			$this->model_user->replaceData($user);
			echo $token;
		} else {
			echo $user['token'];
		}
	}

	public function update_hunt_info()
	{
		$user_id = $this->input->get_post('user_id');
		$name = $this->input->get_post('name');
		$email = $this->input->get_post('email');
		$account_type = $this->input->get_post('account_type');
		$is_main = $this->input->get_post('is_main');

		$user = $this->model_user->getById($user_id);
		$user['name'] = $name;
		$user['email'] = $email;
		$user['account_type'] = $account_type;
		$user['is_main'] = $is_main;

		$this->model_user->replaceData($user);

		echo "Updated Success";
	}

	public function smart_cams()
	{
		$user_id = $this->input->get_post('user_id');
		$cameras = $this->model_camera->smartcams_by_user($user_id);

		echo json_encode($cameras);
	}

	public function update_account_type()
	{
		$user_id = $this->input->get_post('user_id');
		$account_type = $this->input->get_post('account_type');
		$user = $this->model_user->getById($user_id);
		if (is_array($user) && !empty($user)) {
			$user['account_type'] = $account_type;
			if ($account_type == 'Ranch') {
				$date = new DateTime('now');
				$date->modify('first day of next month');
				$user['datausage_reset_at'] = $date->format('Y-m-03');
				$user['jwt_sub_status'] = 'active';
			} else if ($account_type == 'Support VIP') {
				$note = "Moved to Support by " . $this->admin_user['name'];
				$data = array(
					'user_id' => $user_id,
					'note' => $note,
					'by_admin' => $this->admin_user['name'],
				);
				$this->model_todo->replaceData($data);
			}
			$this->model_user->replaceData($user);
		}

		echo "Success";
	}

	public function getSubAccounts()
	{
		$user_id = $this->input->get_post('user_id');
		$draw = $this->input->get_post("draw");
		$sub_accounts = $this->model_sub->getSubs($user_id);

		$user_ids = array();
		foreach ($sub_accounts as $sub_account) {
			array_push($user_ids, $sub_account['sub_id']);
		}

		if (sizeof($user_ids) == 0) {
			$subs = array();
		} else {
			$subs = $this->model_user->getByIDs($user_ids);
		}

		$senddata = array(
			"draw" => $draw,
			"user_ids" => $user_ids,
			"recordsTotal" => count($subs),
			"iTotalDisplayRecords" => count($subs),
			"recordsFiltered" => count($subs),
			"data" => $subs
		);

		echo json_encode($senddata);
	}

	public function remove_subs()
	{
		$main_id = $this->input->get_post('user_id');
		$ids = $this->input->get_post('ids');
		foreach ($ids as $sub_id) {
			$this->model_sub->deleteByMainSub($main_id, $sub_id);
		}
	}

	public function add_sub()
	{
		$main_id = $this->input->get_post('main_id');
		$sub_id = $this->input->get_post('sub_id');

		$this->model_sub->deleteByMainSub($main_id, $sub_id);
		$data = array(
			'main_id' => $main_id,
			'sub_id' => $sub_id,
		);
		$this->model_sub->replaceData($data);
		echo 'success';
	}

	public function list_todo()
	{
		$user_id = $this->input->get_post('user_id');

		$items = $this->model_todo->getAllByUser($user_id);
		echo json_encode($items);
	}

	public function todo_remove()
	{
		$user_id = $this->input->get_post('user_id');
		$created_at = $this->input->get_post('created_at');
		$this->model_todo->deleteById($user_id, $created_at);
		echo "ok";
	}

	public function todo_add()
	{
		$user_id = $this->input->get_post('user_id');
		$note = $this->input->get_post('note');
		$data = array(
			'user_id' => $user_id,
			'note' => $note,
			'by_admin' => $this->admin_user['name'],
		);
		$this->model_todo->replaceData($data);
		echo "ok";
	}

	public function todo_check()
	{
		$user_id = $this->input->get_post('user_id');
		$created_at = $this->input->get_post('created_at');
		$checked = $this->input->get_post('checked');
		$data = $this->model_todo->getById($user_id, $created_at);
		$data['checked'] = $checked == 1 ? true : false;
		$this->model_todo->replaceData($data);
		echo "ok";
	}

	public function force_logout()
	{
		$user_id = $this->input->get_post('user_id');
		$user = $this->model_user->getById($user_id);
		$user['token'] = '';
		$this->model_user->replaceData($user);
		echo "OK";
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

	public function remove_camera()
	{
		$user_id = $this->input->get_post('user_id');
		$camera_id = $this->input->get_post('camera_id');

		$camera = $this->model_camera->getById($camera_id);
		$user = $this->model_user->getById($user_id);

		if (is_array($camera) && !empty($camera) && $camera['IMEI'] != '') {
			$history = array(
				"user_id" => $user["id"],
				"user_email" => $user["email"],
				"user_name" => $user["name"],
				"info" => "Admin - smart cam '" . $camera['IMEI'] . "' deleted",
				"IMEI" => $camera['IMEI'],
			);
			$this->model_history_camera->replaceData($history);
		}

		$result_flag = $this->model_camera->deleteById($camera_id);

		$response = array(
			'result' => 'ok',
			'message' => $result_flag,
			'id' => $camera_id
		);
		echo json_encode($response);
	}

	public function label_cameras()
	{
		$user_id = $this->input->get_post('user_id');
		$user = $this->model_user->getById($user_id);
		if (is_array($user) && !empty($user)) {
			$customer_name = "R" . $user['jwt_id'];
			$this->model_att->create_customer($customer_name);
			$cameras = $this->model_camera->smartcams_by_user($user_id);
			$verizon_devices = array();
			$hologram_devices = array();
			foreach ($cameras as $camera) {
				$device = $this->model_livecamera->getByIMEI($camera['IMEI']);
				if (is_array($device) && !empty($device)) {
					if ($device['att_verizon'] == 'ATT') {
						$this->model_att->device_set_customer($device, $customer_name);
					} else if ($device['att_verizon'] == 'Verizon') {
						array_push($verizon_devices, $device);
					} else if (strtoupper($device['att_verizon']) == 'HOLOGRAM' || strtoupper($device['att_verizon']) == 'VZ + HOLOGRAM') {
						array_push($hologram_devices, $device);
					}
				}
			}
			if (sizeof($hologram_devices) > 0) {
				$this->model_hologram->label_cameras($customer_name, $hologram_devices);
			}
			if (sizeof($verizon_devices) > 0) {
				$this->model_verizon->group_create($customer_name, $user['email'], $verizon_devices);
			}
		}
		echo "Success";
	}

	public function reset_password()
	{
		$user_id = $this->input->get_post('user_id');
		$password = $this->input->get_post('password');

		$item = $this->model_user->getById($user_id);
		$item['password'] = $password;

		$this->model_user->replaceData($item);

		echo "reset password successfully";
	}

	public function update_threshold()
	{
		$user_id = $this->input->get_post('user_id');
		$threshold = $this->input->get_post('threshold');

		$manager = $this->model_user->getOrgById($user_id);
		$org_id = $manager['org_id'];
	}
}