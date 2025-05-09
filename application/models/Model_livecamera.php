<?php
require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\AwsException;

/**
 * Live Camera Model
 * Created by: Li
 * Date: 2/6/2020
 */
class Model_livecamera extends Base_model
{
	public function __construct()
	{
		parent::__construct();
		$this->table = "tbl_livecamera";
		$this->load->model('model_att');
		$this->load->model('model_verizon');
		$this->load->model('model_hologram');
		if (defined('AWS_ACCESS_KEY_ID')) {
			$this->client = new DynamoDbClient([
				'region' => 'us-east-2',
				'version' => 'latest',
				'credentials' => [
					'key' => AWS_ACCESS_KEY_ID,
					'secret' => AWS_SECRET_ACCESS_KEY,
				],
			]);
		} else {
			$this->client = new DynamoDbClient([
				'region' => 'us-east-2',
				'version' => 'latest',
			]);
		}
	}

	public function getById($arg)
	{
		$this->db->select('*');
		$this->db->where('id', $arg);
		$query = $this->db->get($this->table);
		return $query->row_array();
	}

	public function getByIMEI($arg)
	{
		$this->db->select('*');
		$this->db->where('IMEI', $arg);
		$query = $this->db->get($this->table);
		return $query->row_array();
	}

	public function getUNIXTime_by_IMEI($arg)
	{
		$this->db->select('*, UNIX_TIMESTAMP(last_text_uploaded_at) as last_text_uploaded_at, UNIX_TIMESTAMP(last_image_uploaded_at) as last_image_uploaded_at, UNIX_TIMESTAMP(estimated_update_at) as estimated_update_at');
		$this->db->where('IMEI', $arg);
		$query = $this->db->get($this->table);
		return $query->row_array();
	}

	public function getByPhone($arg)
	{
		$this->db->select('*');
		$this->db->where('phone', $arg);
		$query = $this->db->get($this->table);
		return $query->row_array();
	}

	public function getByICCID($arg)
	{
		$this->db->select('*');
		$this->db->where('iccid', $arg);
		$query = $this->db->get($this->table);
		return $query->row_array();
	}

	public function replaceData($data)
	{
		return $this->db->replace($this->table, $data);
	}

	public function deleteById($arg)
	{
		return $this->db->delete($this->table, array('id' => $arg));
	}

	public function old_fw_cameras($device_type, $fw_version, $start_id)
	{
		$sql = "SELECT * FROM $this->table WHERE version = '$device_type' AND firmware_version != '$fw_version' AND id > $start_id ORDER BY id ASC LIMIT 10";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function get_DC2B()
	{
		$sql = "SELECT * FROM $this->table WHERE version LIKE '%DC2B%' AND camera_mode != 5 LIMIT 20";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function update_camera_mode($IMEI)
	{
		$data = array(
			'camera_mode' => 5,
		);
		$this->db->where('IMEI', $IMEI);
		$this->db->update($this->table, $data);
	}

	// public function verizon_not_use()
	// {
	// 	$sql = "SELECT tbl_livecamera.* FROM tbl_livecamera LEFT JOIN tbl_camera ON tbl_livecamera.IMEI = tbl_camera.IMEI WHERE tbl_livecamera.att_verizon = 'Verizon' AND iccid != '' AND (tbl_livecamera.state != 'deactive' OR tbl_livecamera.state is NULL) AND tbl_camera.id IS NULL LIMIT 20;";
	// 	$query = $this->db->query($sql);
	// 	$result = $query->result_array();
	// 	return $result;
	// }

	public function deactive_multi($ids)
	{
		$data = array(
			'state' => 'deactive',
		);
		$this->db->where_in('id', $ids);
		$this->db->update($this->table, $data);
	}

	/*========================
																	   READ
																   ==========================*/
	public function getTotalCount($filterOpt)
	{
		$sql = "SELECT count(id) as total_count FROM $this->table WHERE ";
		$sql .= "IMEI LIKE '%" . $filterOpt["search"] . "%' OR ";
		$sql .= "iccid LIKE '%" . $filterOpt["search"] . "%' OR ";
		$sql .= "firmware_version LIKE '%" . $filterOpt["search"] . "%' OR ";
		$sql .= "phone LIKE '%" . $filterOpt["search"] . "%' ";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return (int) $data['total_count'];
	}

	public function getSearchData($filterOpt)
	{
		$sql = 'SELECT *, UNIX_TIMESTAMP(last_text_uploaded_at) as last_text_uploaded_at, UNIX_TIMESTAMP(last_image_uploaded_at) as last_image_uploaded_at, UNIX_TIMESTAMP(created_at) as created_at FROM tbl_livecamera WHERE ';
		$sql .= 'IMEI LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'iccid LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'firmware_version LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'phone LIKE "%' . $filterOpt['search'] . '%"  ';
		$sql .= "ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
		$sql .= "LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function list_not_connected_count($filterOpt)
	{
		$sql = "SELECT count(id) as total_count ";
		$sql .= 'FROM tbl_livecamera LEFT JOIN tbl_camera ON tbl_livecamera.IMEI = tbl_camera.IMEI ';
		$sql .= 'WHERE tbl_camera.id IS NOT NULL AND ';
		$sql .= 'last_text_uploaded_at < DATE_SUB(DATE(now()), INTERVAL 15 DAY) AND ';
		$sql .= 'last_image_uploaded_at < DATE_SUB(DATE(now()), INTERVAL 15 DAY) AND ';
		$sql .= '(tbl_livecamera.IMEI LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'phone LIKE "%' . $filterOpt['search'] . '%")  ';
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return (int) $data['total_count'];
	}

	public function list_not_connected($filterOpt)
	{
		$sql = 'SELECT tbl_livecamera.*, UNIX_TIMESTAMP(tbl_livecamera.last_text_uploaded_at) as last_text_uploaded_at, UNIX_TIMESTAMP(last_image_uploaded_at) as last_image_uploaded_at, UNIX_TIMESTAMP(tbl_camera.created_at) as created_at_unix ';
		$sql .= 'FROM tbl_livecamera LEFT JOIN tbl_camera ON tbl_livecamera.IMEI = tbl_camera.IMEI ';
		$sql .= 'WHERE tbl_camera.id IS NOT NULL AND ';
		$sql .= 'last_text_uploaded_at < DATE_SUB(DATE(now()), INTERVAL 15 DAY) AND ';
		$sql .= 'last_image_uploaded_at < DATE_SUB(DATE(now()), INTERVAL 15 DAY) AND ';
		$sql .= '(tbl_livecamera.IMEI LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'phone LIKE "%' . $filterOpt['search'] . '%")  ';
		$sql .= "ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
		$sql .= "LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
		// echo $sql;
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function all_datausage()
	{
		$sql = "SELECT * FROM tbl_livecamera WHERE data_usage_image_count > 0 OR data_usage_video_count > 0";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function list_auto_deactivated_count($filterOpt)
	{
		$sql = "SELECT count(id) as total_count FROM $this->table WHERE ";
		$sql .= 'auto_deactivated = 1 AND (state = "DEACTIVATED" OR state = "deactive") AND ';
		$sql .= '(IMEI LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'phone LIKE "%' . $filterOpt['search'] . '%" ) ';
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return (int) $data['total_count'];
	}

	public function list_auto_deactivated($filterOpt)
	{
		$sql = 'SELECT *, UNIX_TIMESTAMP(last_text_uploaded_at) as last_text_uploaded_at, UNIX_TIMESTAMP(last_image_uploaded_at) as last_image_uploaded_at, UNIX_TIMESTAMP(created_at) as created_at FROM tbl_livecamera WHERE ';
		$sql .= 'auto_deactivated = 1 AND (state = "DEACTIVATED" OR state = "deactive") AND ';
		$sql .= '(IMEI LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'phone LIKE "%' . $filterOpt['search'] . '%" ) ';
		$sql .= "ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
		$sql .= "LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function send_sms($device, $message)
	{
		if (strtoupper($device['att_verizon']) == "VERIZON") {
			$this->model_verizon->send_sms($device, $message);
		} else if (strtoupper($device['att_verizon']) == "ATT") {
			$this->model_att->send_sms($device, $message);
		} else if (strtoupper($device['att_verizon']) == 'HOLOGRAM' || strtoupper($device['att_verizon']) == 'VZ + HOLOGRAM') {
			$this->model_hologram->send_sms($device, $message);
		}
	}

	public function update_state($device_id, $state)
	{
		$this->db->set('state', $state);
		$this->db->where('id', $device_id);
		$this->db->update($this->table); // gives UPDATE mytable SET field = field+1 WHERE id = 2
	}

	public function is_suspended($device)
	{
		if (strtoupper($device['att_verizon']) == "VERIZON") {
			if ($device['state'] == 'deactive')
				return true;
		} else if (strtoupper($device['att_verizon']) == "ATT") {
			if ($device['state'] == 'DEACTIVATED')
				return true;
		} else if (strtoupper($device['att_verizon']) == 'HOLOGRAM' || strtoupper($device['att_verizon']) == 'VZ + HOLOGRAM') {
			if ($device['state'] == 'pause')
				return true;
		}
		return false;
	}

	public function device_suspend($device, $is_suspend, $save = true)
	{
		if ($is_suspend == 1) {
			if (strtoupper($device['att_verizon']) == "VERIZON") {
				$device['state'] = 'deactive';
				$this->model_verizon->device_deactivate($device);
			} else if (strtoupper($device['att_verizon']) == "ATT") {
				$device['state'] = 'DEACTIVATED';
				$this->model_att->device_activate($device, 0);
			} else if (strtoupper($device['att_verizon']) == 'HOLOGRAM' || strtoupper($device['att_verizon']) == 'VZ + HOLOGRAM') {
				$device['state'] = 'pause';
				$this->model_hologram->pause_live($device, 'pause');
			}
		} else {
			if (strtoupper($device['att_verizon']) == "VERIZON") {
				$device['state'] = 'active';
				$this->model_verizon->device_activate($device);
			} else if (strtoupper($device['att_verizon']) == "ATT") {
				$device['state'] = 'ACTIVATED';
				$this->model_att->device_activate($device, 1);
			} else if (strtoupper($device['att_verizon']) == 'HOLOGRAM' || strtoupper($device['att_verizon']) == 'VZ + HOLOGRAM') {
				$device['state'] = 'live';
				$this->model_hologram->pause_live($device, 'live');
			}
		}

		if ($save) {
			$this->device_status_change($device, 1 - $is_suspend);
			$this->update_state($device['id'], $device['state']);
		}
		return $device;
	}

	// private function device_status_change($device, $is_active)
	// {
	// 	$device_id = $device['id'];
	// 	$IMEI = $device['IMEI'];
	// 	// $sql = "DELETE FROM tbl_device_status_change_history WHERE IMEI = '$IMEI'";
	// 	// $this->db->query($sql);
	// 	$sql = "INSERT INTO tbl_device_status_change_history (device_id, IMEI, is_active) VALUES ($device_id, '$IMEI', $is_active)";
	// 	$this->db->query($sql);
	// }

	private function device_status_change($device, $is_active)
	{
		$IMEI = $device['IMEI'];
		$device_id = $device['id'];
		try {
			$is_active = isset($is_active) && is_numeric($is_active) ? (string) $is_active : "0";
			$is_checked = "0";
			$item = [
				'IMEI' => ['S' => $IMEI],
				'device_id' => ['N' => (String) $device_id],
				'is_active' => ['N' => $is_active],
				'is_checked' => ['N' => $is_checked],
				'created_at' => ['N' => (String) time()],
				'pk_all' => ['S' => 'All']
			];
			$result = $this->client->putItem([
				'TableName' => 'tbl_device_status_change_history',
				'Item' => $item,
			]);
			return "Device status changed successfully";
		} catch (AwsException $e) {
			return "Error :" . $e->getMessage();
		}
	}
}
