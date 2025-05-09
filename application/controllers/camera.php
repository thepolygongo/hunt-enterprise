<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Camera extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
        if (!$this->session->userdata('admin_user')) {
            redirect('/auth');
        } else {
            $this->admin_user = $this->session->userdata('admin_user');
        }
		$this->load->model('model_user');
		$this->load->model('model_livecamera');
		$this->load->model('model_camera');
		$this->load->model('model_device_setting_received');
		$this->load->model('model_setting_firmware_versions');
		$this->load->model('model_pro_camera');
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
        $this->viewCameraList();
	}

    public function viewCameraList()
	{
		$data['page'] = 'camera/viewCameraList';
		$data['title'] = 'CameraList';
		$this->__prefix('pages/camera/viewCameraList', $data);
	}

    public function viewEdit($id)
    {
        $page = 'camera/viewEdit';
        $item = $this->model_livecamera->getById($id);

        $data = array(
            'page' => $page,
            'data' => $item,
        );

        $this->__prefix($page, $data);
    }

    public function getCameraData()
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

		$total_count = $this->model_pro_camera->getTotalCount($filteropt);
		$data = $this->model_pro_camera->getSearchData($filteropt);
		$cameras = array();
		foreach ($data as $item) {
			$camera = $this->model_camera->getById($item['camera_id']);
            $user = $this->model_user->getById($item['user_id']);
            $user_email = '';
            if (is_array($user) && !empty($user))
                $user_email = $user['email'];
			$cameras[] = array(
				'id' => $item['id'],
				'name' => $camera['name'],
				'IMEI' => $camera['IMEI'],
                'user_email' => $user_email,
				'created_at' => $item['created_at'],
			);
		}

		$senddata = array(
			"draw" => $draw,
			"recordsTotal" => $total_count,
			"iTotalDisplayRecords" => $total_count,
			"recordsFiltered" => count($cameras),
			"data" => $cameras
		);
		echo json_encode($senddata);
	}
    
    public function getListByUser()
    {
        $order_i = $this->input->get_post("order[0][column]");
        $order_field = $this->input->get_post("columns[" . $order_i . "][data]");
        $order_dir = $this->input->get_post("order[0][dir]");
        $length = $this->input->get_post("length");
        $start = $this->input->get_post("start");
        $searchval = $this->input->get_post("search[value]");
        $draw = $this->input->get_post("draw");
        $user_id = $this->input->get_post("user_id");

        $filteropt = array(
            "order_i" => $order_i,
            "order_field" => $order_field,
            "order_dir" => $order_dir,
            "length" => $length,
            "start" => $start,
            "search" => $searchval,
            "user_id" => $user_id,
        );

        // $cameras = $this->model_camera->getListByUser($filteropt);
        // $total_count = $this->model_camera->getCountByUser($filteropt);

        // for ($i = 0; $i < count($cameras); $i++) {
        //     $IMEI = $cameras[$i]['IMEI'];
        //     $device = $this->model_livecamera->getByIMEI($IMEI);
        //     if (!is_array($device) || empty($device)) {
        //         $cameras[$i]['device_id'] = 0;
        //         $cameras[$i]['is_active'] = 0;
        //         $cameras[$i]['att_verizon'] = '';
        //         $cameras[$i]['version'] = '';
        //         $cameras[$i]['iccid'] = '';
        //     } else {
        //         $cameras[$i]['device_id'] = $device['id'];
        //         $cameras[$i]['iccid'] = $device['iccid'];
        //         $cameras[$i]['version'] = $device['version'];
        //         $cameras[$i]['att_verizon'] = $device['att_verizon'];
        //         $cameras[$i]['is_active'] = $this->model_livecamera->is_suspended($device) ? 0 : 1;
        //     }
        // }
        $pro_cameras = $this->model_pro_camera->getListByUser($filteropt);
        $total_count = $this->model_pro_camera->getCountByUser($filteropt);
        $cameras = [];
        foreach($pro_cameras as $pro_cam) {
            $camera = $this->model_camera->getById($pro_cam['camera_id']);
            $IMEI = $camera['IMEI'];
            $device = $this->model_livecamera->getByIMEI($IMEI);
            if (!is_array($device) || empty($device)) {
                $camera['device_id'] = 0;
                $camera['is_active'] = 0;
                $camera['att_verizon'] = '';
                $camera['version'] = '';
                $camera['iccid'] = '';
            } else {
                $camera['device_id'] = $device['id'];
                $camera['iccid'] = $device['iccid'];
                $camera['version'] = $device['version'];
                $camera['att_verizon'] = $device['att_verizon'];
                $camera['is_active'] = $this->model_livecamera->is_suspended($device) ? 0 : 1;
            }
            $cameras[] = $camera;
        }

        $senddata = array(
            "draw" => $draw,
            "recordsTotal" => $total_count,
            "iTotalDisplayRecords" => $total_count,
            "recordsFiltered" => count($cameras),
            "data" => $cameras
        );
        echo json_encode($senddata);
    }

    function device_setting_get()
    {
        $IMEI = $this->input->get_post("IMEI");
        $device = $this->model_livecamera->getUNIXTime_by_IMEI($IMEI);

        if (!is_array($device) || empty($device)) {
            $response = array(
                'result' => API_RESULT_ERROR,
                'message' => "IVALID IMEI",
                'device' => array(),
                'setting' => array()
            );
            echo json_encode($response);
        } else {
            $liveCamera = array();
            if ($device['version'] == 'A') {
                $setting_arr = $this->multiexplode(array("#"), $device['device_setting']);
                if (sizeof($setting_arr) > 28) {
                    $liveCamera['camera_mode'] = $setting_arr[1];
                    $liveCamera['delay'] = $setting_arr[15];
                    $liveCamera['multi_shot'] = $setting_arr[5];
                    $liveCamera['time_lapse'] = $setting_arr[16];
                    $liveCamera['picture_size'] = $setting_arr[2];
                    $liveCamera['pir_sensitivity'] = $setting_arr[12];
                    $liveCamera['night_mode'] = $setting_arr[6];
                    $liveCamera['work_timer1'] = $setting_arr[17];
                    $liveCamera['sms_remote'] = $setting_arr[28];
                    $liveCamera['work_timer2'] = $setting_arr[18];
                }
            } else if ($device['version'] == 'B' || $device['version'] == 'C') {
                $setting_arr = $this->multiexplode(array("#"), $device['device_setting']);
                if (sizeof($setting_arr) > 26) {
                    $firmware_version = $device['firmware_version'];
                    if ($this->model_setting_firmware_versions->is_after_d404($firmware_version)) {
                        $liveCamera['camera_mode'] = $setting_arr[1];
                        $liveCamera['trans_video'] = $setting_arr[20];
                        $liveCamera['multi_shot'] = $setting_arr[5];
                        $liveCamera['video_quality'] = $setting_arr[3];
                        $liveCamera['pir_sensitivity'] = $setting_arr[12];
                        $liveCamera['video_length'] = $setting_arr[4];
                        $liveCamera['picture_size'] = $setting_arr[2];
                        $liveCamera['delay'] = $setting_arr[15];
                        $liveCamera['sending_mode'] = $setting_arr[24];
                        $liveCamera['camera_name'] = $setting_arr[22];
                        $liveCamera['sms_remote'] = $setting_arr[26];
                        $liveCamera['time_lapse'] = $setting_arr[16];
                        $liveCamera['night_mode'] = $setting_arr[6];
                        $liveCamera['work_timer1'] = $setting_arr[17];
                        $liveCamera['ir_flash'] = $setting_arr[7];
                        $liveCamera['work_timer2'] = $setting_arr[18];
                    } else {
                        $liveCamera['multi_shot'] = $setting_arr[5];
                        $liveCamera['ir_flash'] = $setting_arr[7];
                        $liveCamera['pir_sensitivity'] = $setting_arr[12];
                        $liveCamera['delay'] = $setting_arr[15];
                        $liveCamera['picture_size'] = $setting_arr[2];
                        $liveCamera['camera_name'] = $setting_arr[22];
                        $liveCamera['sms_remote'] = $setting_arr[26];
                        $liveCamera['time_lapse'] = $setting_arr[16];
                        $liveCamera['sending_mode'] = $setting_arr[24];
                        $liveCamera['work_timer1'] = $setting_arr[17];
                        $liveCamera['night_mode'] = $setting_arr[6];
                        $liveCamera['work_timer2'] = $setting_arr[18];
                    }
                }
            } else if ($device['version'] == 'DC2') {
                $setting_arr = $this->multiexplode(array("#"), $device['device_setting']);
                if (sizeof($setting_arr) > 26) {
                    $firmware_version = $device['firmware_version'];
                    $liveCamera['camera_mode'] = $setting_arr[1];
                    $liveCamera['trans_video'] = $setting_arr[20];
                    $liveCamera['multi_shot'] = $setting_arr[5];
                    $liveCamera['video_quality'] = $setting_arr[3];
                    $liveCamera['pir_sensitivity'] = $setting_arr[12];
                    $liveCamera['video_length'] = $setting_arr[4];
                    $liveCamera['picture_size'] = $setting_arr[2];
                    $liveCamera['delay'] = $setting_arr[15];
                    $liveCamera['sending_mode'] = $setting_arr[24];
                    $liveCamera['camera_name'] = $setting_arr[22];
                    $liveCamera['sms_remote'] = $setting_arr[26];
                    $liveCamera['time_lapse'] = $setting_arr[16];
                    $liveCamera['night_mode'] = $setting_arr[6];
                    $liveCamera['work_timer1'] = $setting_arr[17];
                    $liveCamera['ir_flash'] = $setting_arr[7];
                    $liveCamera['work_timer2'] = $setting_arr[18];
                }
            } else if ($device['version'] == 'MC2') {
                $setting = $this->model_device_setting_received->getByIMEI($device['IMEI']);
                // var_dump($setting);
                foreach ($setting as $key => $value) {
                    $label = mc2OptionLabel($key, $value);
                    // echo "$key=>$label";
                    if ($key == "pir_interval") {
                        $key = "time_lapse";
                    }
                    if ($key == "synced_at") {
                        $liveCamera['last_text_uploaded_at'] = $value;
                    }
                    if ($label != '-null-') {
                        $liveCamera[$key] = $label;
                    }
                }
            } else if ($device['version'] == 'DC2B') {
                $setting = $this->model_device_setting_received->getByIMEI($device['IMEI']);
                foreach ($setting as $key => $value) {
                    $label = dc2bOptionLabel($key, $value);
                    if ($key == "pir_interval") {
                        $key = "time_lapse";
                    }
                    if ($key == "synced_at") {
                        $liveCamera['last_text_uploaded_at'] = $value;
                    }
                    if ($label != '-null-') {
                        $liveCamera[$key] = $label;
                    }
                }
            }

            $response = array(
                'result' => API_RESULT_OK,
                'device' => $device,
                'setting' => $liveCamera,
            );
            echo json_encode($response);
        }
    }

    private function multiexplode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }

    public function updateIMEI()
    {
        $user_id = $this->input->get_post("user_id");
        $camera_id = $this->input->get_post("camera_id");
        $IMEI = $this->input->get_post("IMEI");
        $newIMEI = $this->input->get_post("newIMEI");

        $device = $this->model_livecamera->getByIMEI($newIMEI);
        if (!is_array($device) || empty($device)) {
            $response = array(
                'result' => 'no',
                'message' => 'Invalid IMEI',
            );
            echo json_encode($response);
            return;
        }
        $newCamera = $this->model_camera->getByIMEI($newIMEI);
        if (is_array($newCamera) && !empty($newCamera)) {
            $response = array(
                'result' => 'no',
                'message' => 'new IMEI registered already',
            );
            echo json_encode($response);
            return;
        }
        $user = $this->model_user->getById($user_id);

        $camera = $this->model_camera->getById($camera_id);
        if ($camera['IMEI'] == "") {
            $response = array(
                'result' => 'no',
                'message' => 'Not Smart Cam',
            );
            echo json_encode($response);
            return;
        } else {
            $camera["IMEI"] = $newIMEI;
            $this->model_camera->replaceData($camera);
            $device = $this->model_livecamera->device_suspend($device, 0);

            $history = array(
                "user_id" => $user["id"],
                "user_email" => $user["email"],
                "user_name" => $user["name"],
                "info" => "IMEI changed - from $IMEI to $newIMEI",
                "IMEI" => $camera['IMEI'],
            );
            $this->model_history_camera->replaceData($history);
            $data = array(
                'user_id' => $user_id,
                'note' => "IMEI changed - from $IMEI to $newIMEI",
                'by_admin' => $this->admin_user['name'],
            );
            $this->model_todo->replaceData($data);
            echo "ok";
        }
    }

    public function delete()
	{
        $id = $this->input->get_post("id");
		$this->model_pro_camera->delete_camera($id);
	}

    public function add_camera()
	{
		// $user_id = $this->input->get_post('user_id');
		$admin_user = $this->session->userdata('admin_user');
		if ($admin_user['account_type'] == 'pro_organization') {
			$org_id = $admin_user['id'];
		} else if ($admin_user['account_type'] == 'pro_manager'){
			$manager = $this->model_pro_manager->getUserId($admin_user['id']);
			$org_id = $manager['org_id'];
		}
		$camera_IMEI = $this->input->get_post('camera_IMEI');
		$camera_name = $this->input->get_post('camera_name');

		$user = $this->model_user->getById($org_id);
		if (is_array($user) && !empty($user)) {
			$result = $this->model_camera->create_smart_by_admin($user, $camera_IMEI, $camera_name);
			
			if ($result['success']) {
				$response = array(
					'result' => 'ok',
					'message' => 'Success',
					'id' => $result['id']
				);
				$this->model_pro_camera->add_camera([
					'camera_id' => $result['id'],
					'org_id' => $org_id,
				]);
			} else {
				$response = array(
					'result' => 'no',
					'message' => $result['message']
				);
			}
		} else {
			$response = array(
				'result' => 'no',
				'message' => 'Invalid User Id'
			);
		}
		echo json_encode($response);
	}

    public function smart_cams()
    {
        $org_id = $this->input->get_post('org_id');
		$cameras = $this->model_camera->smartcams_to_assign_by_user($org_id);
        $filter_cams = [];
        foreach( $cameras as $camera ) {
            $pro_cam  = $this->model_pro_camera->getByCameraId($camera['id']);
            $filter_cams[] = $camera;
        }

		echo json_encode($filter_cams);
    }

    public function assign_camera()
    {
        $camera_id = $this->input->get_post('camera_id');
		$user_id = $this->input->get_post('user_id');
		$org_id = $this->input->get_post('org_id');
        $result = $this->model_pro_camera->assignCamera($camera_id, $user_id);
        $response = array(
            'result' => 'ok',
            'message' => 'Success',
        );
        echo json_encode($response);
    }

}
?>