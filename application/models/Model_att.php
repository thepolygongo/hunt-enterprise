<?php

class Model_att extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table_failed = "tbl_att_api_failed";
    }

	public function device_activate($device, $is_active)
	{
		if ($device['iccid'] == ''){
			$this->db->replace($this->table_failed, array(
				'IMEI' => $device['IMEI'],
				'iccid' => $device['iccid'],
				'api' => 'device_activate',
				'errorCode' => '',
				'errorMessage' => 'ICCID invalid'
			));
			return;
		}
		
		$new_status = $is_active ? 'ACTIVATED' : 'DEACTIVATED';
		$old_status = $this->device_status($device);
		
		if($old_status == null || $new_status == $old_status){
			return;
		}

		$url = "https://restapi19.att.com/rws/api/v1/devices/".$device['iccid'];

		$data = array(
			"status" => $is_active? 'ACTIVATED' : 'DEACTIVATED'
		);
		$data_string = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				"Authorization:" . ATT_API_KEY,
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string)
			)
		);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode > 399) {
			// echo $response;
            $response_json = json_decode($response, TRUE);
			// echo json_encode($response_json);
			$this->db->replace($this->table_failed, array(
				'errorCode' => $response_json['errorCode'],
				'errorMessage' => $response_json['errorMessage'],
				'IMEI' => $device['IMEI'],
				'iccid' => $device['iccid'],
				'api' => 'device_activate-'.$data['status']
			));
		}

		return true;
	}
	
	public function device_status($device)
	{
		$iccid = $device['iccid'];
		$url = "https://restapi19.att.com/rws/api/v1/devices/" . $iccid;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				"Authorization:" . ATT_API_KEY,
			)
		);

		$response = curl_exec($ch);
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($statusCode == 200) {
			$response_json = json_decode($response, TRUE);
			$device_status = $response_json['status'];
			return $device_status;
		} else {
			// echo $response;
			// $response_json = json_decode($response, TRUE);
			// echo json_encode($response_json);
			$this->db->replace($this->table_failed, array(
				'errorCode' => 0,
				'errorMessage' => $response,
				'IMEI' => $device['IMEI'],
				'iccid' => $device['iccid'],
				'api' => 'check status'
			));
		}
		return null;
	}

    public function send_sms($device, $message)
    {
		$url = "https://restapi19.att.com/rws/api/v1/devices/".$device['iccid']."/smsMessages";

		$data = array(
			"messageText" => $message
		);
		$data_string = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				"Authorization:" . ATT_API_KEY,
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string)
			)
		);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode > 399) {
            $response_json = json_decode($response, TRUE);
			$this->db->replace($this->table_failed, array(
				'errorCode' => $response_json['errorCode'],
				'errorMessage' => $response_json['errorMessage'],
				'IMEI' => $device['IMEI'],
				'iccid' => $device['iccid'],
				'api' => 'device_activate'
			));
		}

		return true;
    }
	
	public function get_customer($customer_id)
	{
		$url = "https://restapi19.att.com/rws/api/v1/customers/".$customer_id;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				"Authorization:" . ATT_API_KEY,
			)
		);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

		echo $response;
		return true;
	}
	
	public function create_customer($customer_name)
	{
		$data = array(
			"name" => $customer_name,
			"accountName" => 'Wise Eye Technologies - ENT'
		);
		$data_string = json_encode($data);

		$url = "https://restapi19.att.com/rws/api/v1/customers";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				"Authorization:" . ATT_API_KEY,
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string)
			)
		);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

		// echo $response;
		return true;
	}
	
	public function device_set_customer($device, $customer_name)
	{
		if ($device['iccid'] == ''){
			$this->db->replace($this->table_failed, array(
				'IMEI' => $device['IMEI'],
				'iccid' => $device['iccid'],
				'api' => 'device_set_customer',
				'errorCode' => '',
				'errorMessage' => 'ICCID invalid'
			));
			return;
		}
		$url = "https://restapi19.att.com/rws/api/v1/devices/".$device['iccid'];

		$data = array(
			"customer" => $customer_name
		);
		$data_string = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				"Authorization:" . ATT_API_KEY,
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string)
			)
		);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode > 399) {
			// echo $response;
            $response_json = json_decode($response, TRUE);
			// echo json_encode($response_json);
			$this->db->replace($this->table_failed, array(
				'errorCode' => $response_json['errorCode'],
				'errorMessage' => $response_json['errorMessage'],
				'IMEI' => $device['IMEI'],
				'iccid' => $device['iccid'],
				'api' => 'device_set_customer'
			));
		}

		return true;
	}

}
