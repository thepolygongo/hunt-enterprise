<?php

/**
 * Camera Model
 * Created by: Li
 * Date: 5/10/219
 *
 */
class Model_camera extends Base_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table = "tbl_camera";
		$this->load->model('model_livecamera');
		$this->load->model('model_user_usage');
		$this->load->model('model_history_camera');
	}

	public function getById($arg)
	{
		$this->db->select('*');
		$this->db->where('id', $arg);
		$query = $this->db->get($this->table);
		return $query->row_array();
	}

	public function getAll()
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function deleteById($id)
	{
		$this->db->where("id", $id);
		$this->db->delete($this->table);
	}

	public function deleteAllByUserId($arg)
	{
		return $this->db->delete($this->table, array('user_id' => $arg));
	}

	public function get_by_user($user_id)
	{
		$data = $this->db->query("SELECT * FROM $this->table WHERE user_id=$user_id ORDER BY name");
		$result = $data->result_array();
		return $result;
	}

	public function getByIMEI($str)
	{
		$this->db->select("*");
		$this->db->from($this->table);
		$this->db->where("IMEI", $str);
		$query = $this->db->get();
		return $query->row_array();
	}

	public function getListByUser($filterOpt)
	{
		$user_id = $filterOpt['user_id'];
		$sql = 'SELECT * FROM ' . $this->table . ' WHERE ';
		$sql .= "user_id = $user_id AND ";
		$sql .= "ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
		$sql .= "LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCountByUser($filterOpt)
	{
		$user_id = $filterOpt['user_id'];
		$sql = 'SELECT  count(id) as total_count FROM ' . $this->table . ' WHERE ';
		$sql .= "user_id = $user_id  ";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return (int) $data['total_count'];
	}

	public function replaceData($data)
	{
		$this->db->replace($this->table, $data);
		return $this->db->insert_id();
	}

	public function total_smart()
	{
		$sql = "SELECT COUNT(id) as total_smart FROM $this->table  WHERE IMEI != ''";
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return $result['total_smart'];
	}

	public function get_all()
	{
		$this->db->select("*");
		$this->db->from($this->table);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function list_invalid_IMEIs_count($filterOpt)
	{
		$sql = "SELECT COUNT(*) AS count ";
		$sql .= 'FROM tbl_camera LEFT JOIN tbl_livecamera ON tbl_livecamera.IMEI = tbl_camera.IMEI ';
		$sql .= "WHERE tbl_livecamera.id IS NULL AND tbl_camera.IMEI != '' AND ";
		$sql .= 'tbl_camera.IMEI LIKE "%' . $filterOpt['search'] . '%" ';
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return $result['count'];
	}

	public function list_invalid_IMEIs($filterOpt)
	{
		$sql = 'SELECT tbl_camera.*, UNIX_TIMESTAMP(tbl_camera.created_at) as created_at ';
		$sql .= 'FROM tbl_camera LEFT JOIN tbl_livecamera ON tbl_livecamera.IMEI = tbl_camera.IMEI ';
		$sql .= "WHERE tbl_livecamera.id IS NULL AND tbl_camera.IMEI != '' AND ";
		$sql .= 'tbl_camera.IMEI LIKE "%' . $filterOpt['search'] . '%" ';
		$sql .= "ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
		$sql .= "LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
		// echo $sql;
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function account_change($user_old, $user_new, $cameras)
	{
		$data = array(
			'user_id' => $user_new,
		);
		$this->db->where("user_id", $user_old);
		$this->db->where_in('id', $cameras);
		$this->db->update($this->table, $data);
	}

	public function daily_add_chart()
	{
		$sql = "SELECT DATE(created_at) AS ForDate,
				COUNT(*) AS NumPosts
				FROM   $this->table
				WHERE IMEI != ''
				GROUP BY DATE(created_at)
				ORDER BY ForDate";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function data_usage()
	{
		$sql = "SELECT tbl_camera.data_usage, tbl_camera.name AS camera_name, tbl_camera.id AS camera_id, tbl_camera.IMEI, tbl_camera, tbl_admin.account_type, 
		tbl_admin.id AS user_id, tbl_admin.name AS user_name 
		FROM tbl_camera LEFT JOIN tbl_admin ON tbl_camera.user_id = tbl_admin.id
		WHERE tbl_camera.data_usage > 350000";
	}

	public function list_data_usage_count($filterOpt)
	{
		$sql = "SELECT COUNT(id) as item_count FROM $this->table WHERE ";
		$sql .= 'IMEI LIKE "%' . $filterOpt['search'] . '%" ';
		$sql .= "AND data_usage > 350000 ";
		// echo $sql;
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return  $result['item_count'];
	}

	public function list_data_usage($filterOpt)
	{
		$sql = "SELECT tbl_camera.data_usage, tbl_camera.name AS camera_name, tbl_camera.id AS camera_id, tbl_camera.IMEI, tbl_admin.account_type, 
		tbl_admin.id AS user_id, tbl_admin.name AS user_name 
		FROM tbl_camera LEFT JOIN tbl_admin ON tbl_camera.user_id = tbl_admin.id
		WHERE tbl_camera.data_usage > 350000";
		$sql .= ' AND IMEI LIKE "%' . $filterOpt['search'] . '%" ';
		$sql .= " ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
		$sql .= " LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
		$query = $this->db->query($sql);
		$users = $query->result_array();
		return $users;
	}

	public function data_usage_total_developer()
	{
		$sql = "SELECT SUM(tbl_camera.data_usage) as data_usage_total  , COUNT(tbl_camera.id) as count_total";
		$sql .= " FROM tbl_camera LEFT JOIN tbl_admin ON tbl_camera.user_id = tbl_admin.id";
		$sql .= " WHERE (tbl_admin.account_type = 'Developer' OR tbl_admin.account_type = 'Ranch')";
		$sql .= " AND data_usage > 350000 ";
		// echo $sql;
		$query = $this->db->query($sql);
		$result = $query->row_array();
		if($result['count_total'] == 0)
			return 0;
		return  $result['data_usage_total'] /  $result['count_total'];
	}

	public function data_usage_total_general()
	{
		$sql = "SELECT SUM(tbl_camera.data_usage) as data_usage_total , COUNT(tbl_camera.id) as count_total";
		$sql .= " FROM tbl_camera LEFT JOIN tbl_admin ON tbl_camera.user_id = tbl_admin.id";
		$sql .= " WHERE (tbl_admin.account_type != 'Developer' AND tbl_admin.account_type != 'Ranch')";
		$sql .= " AND data_usage > 350000 ";
		// echo $sql;
		$query = $this->db->query($sql);
		$result = $query->row_array();
		if($result['count_total'] == 0)
			return 0;
		return  $result['data_usage_total'] / $result['count_total'];
	}

	public function smartcams_by_user($user_id) 
	{
		$this->db->select("*");
		$this->db->from($this->table);
		$this->db->where("IMEI != ''");
		$this->db->where("user_id", $user_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function smartcams_to_assign_by_user($org_id) 
	{
		$this->db->select("*");
		$this->db->from($this->table);
		$this->db->where("IMEI != ''");
		$this->db->where("user_id", $org_id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function count_smart_active($user_id)
	{
		$cameras = $this->get_by_user($user_id);
		$active_count = 0;
		foreach ($cameras as $camera) {
			if ($camera['IMEI'] != '') {
				$device = $this->model_livecamera->getByIMEI($camera['IMEI']);
				if (is_array($device) && !empty($device)) {
					$is_suspended = $this->model_livecamera->is_suspended($device);
					if (!$is_suspended) {
						$active_count++;
					}
				}
			}
		}
		return $active_count;
	}

	public function create_smart_by_admin($user, $IMEI, $camera_name)
	{
		// if (!$this->model_user_usage->is_available_add_smart($user)) {
		// 	return array(
		// 		'success' => false,
		// 		'message' => 'Before adding new smart cam, Please visit our website and update your subscription. http://www.wiseeyetech.com/my-account',
		// 	);
		// }

		$camera = $this->getByIMEI($IMEI);
		if (is_array($camera) && !empty($camera)) {
			return array(
				'success' => false,
				'message' => 'IMEI is already registered, please try to use other IMEI or contact with support team.',
			);
		}

		$device = $this->model_livecamera->getByIMEI($IMEI);
		if (!is_array($device) || empty($device)) {
			return array(
				'success' => false,
				'message' => 'The device is not register on our database',
			);
		} else if ($device['iccid'] == '') {
			return array(
				'success' => false,
				'message' => 'IVALID iccid',
			);
		}

		// if ($this->model_user_usage->is_available_active_smart($user)) {
		// 	$device = $this->model_livecamera->device_suspend($device, 0);
		// } else {
		// 	$device = $this->model_livecamera->device_suspend($device, 1);
		// }

		$history = array(
			"user_id" => $user['id'],
			"user_email" => $user["email"],
			"user_name" => $user["name"],
			"info" => "Admin - smart cam '" . $IMEI . "' added as " . $camera_name,
			"IMEI" => $IMEI,
		);
		$this->model_history_camera->replaceData($history);

		$fix_location = 0;
		if ($device['version'] == 'B')
			$fix_location = 1;
		$data = array(
			'name' => $camera_name,
			'IMEI' => $IMEI,
			'fix_location' => $fix_location,
			'user_id' => $user['id'],
		);

		$id = $this->replaceData($data);
		return array(
			'success' => true,
			'message' => 'Success',
			'id' => $id
		);
	}
}
