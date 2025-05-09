<?php

class Model_hologram extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table_sim_hologram = "tbl_sim_hologram";
        $this->table_sim_api_failed = "tbl_sim_api_failed";
        $this->table_sim_hologram_tag = "tbl_sim_hologram_tag";
    }

    public function getByICCID($str)
    {
        $this->db->select("*");
        $this->db->from($this->table_sim_hologram);
        $this->db->where("iccid", $str);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getTagID($tag_string)
    {
        $this->db->select("*");
        $this->db->from($this->table_sim_hologram_tag);
        $this->db->where("tag", $tag_string);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_hologram_info($device)
    {
        $iccid = $device['iccid'];
        $sim = $this->getByICCID($iccid);
        if (is_array($sim) && !empty($sim)) {
            return $sim;
        }
        $url = "https://dashboard.hologram.io/api/1/links/cellular?sim=$iccid";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . API_HOLOGRAM_KEY
            ),
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response_json = json_decode($response, TRUE);
        if ($response_json['success'] && $response_json['size'] == 1) {
            $deviceid = $response_json['data'][0]['deviceid'];
            $link_id = $response_json['data'][0]['id'];
            $sim = array(
                'device_id' => $device['id'],
                'iccid' => $device['iccid'],
                'IMEI' => $device['IMEI'],
                'hologram_device_id' => $deviceid,
                'hologram_link_id' => $link_id,
            );
            $this->db->replace($this->table_sim_hologram, $sim);
            return $sim;
        } else {
            $this->db->replace($this->table_sim_api_failed, array(
                'errorCode' => $statusCode,
                'errorMessage' => $response,
                'device_id' => $device["id"],
                'IMEI' => $device["IMEI"],
                'iccid' => $device["iccid"],
                'carrier' => $device['att_verizon'],
                'api' => 'get_hologram_info'
            ));
            return null;
        }
    }

    public function get_info_with_state($device)
    {
        $iccid = $device['iccid'];
        
        $url = "https://dashboard.hologram.io/api/1/links/cellular?sim=$iccid";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . API_HOLOGRAM_KEY
            ),
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response_json = json_decode($response, TRUE);
        if ($response_json['success'] && $response_json['size'] == 1) {
            $deviceid = $response_json['data'][0]['deviceid'];
            $link_id = $response_json['data'][0]['id'];
            $state = $response_json['data'][0]['state'];
            $sim = array(
                'device_id' => $device['id'],
                'iccid' => $device['iccid'],
                'IMEI' => $device['IMEI'],
                'hologram_device_id' => $deviceid,
                'hologram_link_id' => $link_id,
                'state' => $state,
            );
            $this->db->replace($this->table_sim_hologram, $sim);
            return $sim;
        } else {
            $this->db->replace($this->table_sim_api_failed, array(
                'errorCode' => $statusCode,
                'errorMessage' => $response,
                'device_id' => $device["id"],
                'IMEI' => $device["IMEI"],
                'iccid' => $device["iccid"],
                'carrier' => $device['att_verizon'],
                'api' => 'get_info_with_state'
            ));
            return null;
        }
    }

    public function send_sms($device, $message)
    {
        $sim = $this->get_hologram_info($device);
        if (!is_array($sim) || empty($sim)) {
            return false;
        }

        $url = "https://dashboard.hologram.io/api/1/sms/incoming";
        $data = array(
            "deviceid" => $sim['hologram_device_id'],
            "body" => $message
        );
        $data_string = json_encode($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . API_HOLOGRAM_KEY,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => $data_string,
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response_json = json_decode($response, TRUE);
        if ($response_json['success']) {
            return true;
        } else {
            $this->db->replace($this->table_sim_api_failed, array(
                'errorCode' => $statusCode,
                'errorMessage' => $response,
                'device_id' => $device["id"],
                'IMEI' => $device["IMEI"],
                'iccid' => $device["iccid"],
                'carrier' => $device['att_verizon'],
                'api' => 'send_sms'
            ));
            return false;
        }
    }

    public function pause_live($device, $state) // live or pause
    {
        $sim = $this->get_info_with_state($device);
        if (!is_array($sim) || empty($sim)) {
            return false;
        }
        $current_state = $sim['state'];
        // echo $current_state;
        if(strtolower($current_state) == $state){
            // echo "same";
            return true;
        }

        $deviceid = $sim['hologram_device_id'];
        $url = "https://dashboard.hologram.io/api/1/devices/$deviceid/state";

        $data = array(
            "state" => $state
        );
        $data_string = json_encode($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . API_HOLOGRAM_KEY,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => $data_string,
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response_json = json_decode($response, TRUE);

        if ($response_json['success']) {
            return true;
        } else {
            $this->db->replace($this->table_sim_api_failed, array(
                'errorCode' => $statusCode,
                'errorMessage' => $response,
                'device_id' => $device["id"],
                'IMEI' => $device["IMEI"],
                'iccid' => $device["iccid"],
                'carrier' => $device['att_verizon'],
                'api' => 'pause_live'
            ));
            return false;
        }
    }

    public function activate($device)
    {
        $iccid = $device['iccid'];
        $url = "https://dashboard.hologram.io/api/1/links/cellular/sim_$iccid/claim";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . API_HOLOGRAM_KEY,
            ),
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response_json = json_decode($response, TRUE);

        if ($response_json['success']) {
            return true;
        } else {
            $this->db->replace($this->table_sim_api_failed, array(
                'errorCode' => $statusCode,
                'errorMessage' => $response,
                'device_id' => $device["id"],
                'IMEI' => $device["IMEI"],
                'iccid' => $device["iccid"],
                'carrier' => $device['att_verizon'],
                'api' => 'activate'
            ));
            return false;
        }
    }


    public function label_cameras($tag_string, $devices)
    {
        $tag = $this->getTagID($tag_string);
        if (!is_array($tag) || empty($tag)) {
            $tag = $this->tag_create($tag_string);
            if (!is_array($tag) || empty($tag))
                return false;
        }
        foreach ($devices as $device) {
            $this->tag_add($tag['tag_id'], $device);
        }
    }

    public function tag_create($tag)
    {
        $url = "https://dashboard.hologram.io/api/1/devices/tags?orgid=71528";

        $data = array(
            "name" => $tag,
            "deviceids" => []
        );
        $data_string = json_encode($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . API_HOLOGRAM_KEY,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => $data_string,
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response_json = json_decode($response, TRUE);

        if ($response_json['success']) {
            $tag_id = $response_json['data']['tags'][0]['id'];
            $data = array(
                'tag_id' => $tag_id,
                'tag' => $tag
            );
            $this->db->replace($this->table_sim_hologram_tag, $data);
            return $data;
        } else {
            $this->db->replace($this->table_sim_api_failed, array(
                'errorCode' => $statusCode,
                'errorMessage' => $response,
                'device_id' => '',
                'IMEI' => '',
                'iccid' => '',
                'carrier' => 'Hologram',
                'api' => 'tag_create'
            ));
            return false;
        }
    }

    public function tag_add($tag_id, $device)
    {
        $sim = $this->get_hologram_info($device);
        if (!is_array($sim) || empty($sim)) {
            return false;
        }

        $url = "https://dashboard.hologram.io/api/1/devices/tags/$tag_id/link?orgid=71528";

        $deviceid = $sim['hologram_device_id'];
        $data = array(
            "deviceids" => [$deviceid]
        );
        $data_string = json_encode($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . API_HOLOGRAM_KEY,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => $data_string,
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response_json = json_decode($response, TRUE);

        if ($response_json['success']) {
            return true;
        } else {
            $this->db->replace($this->table_sim_api_failed, array(
                'errorCode' => $statusCode,
                'errorMessage' => $response,
                'device_id' => $device["id"],
                'IMEI' => $device["IMEI"],
                'iccid' => $device["iccid"],
                'carrier' => $device['att_verizon'],
                'api' => 'tag_add'
            ));
            return false;
        }
    }
}
