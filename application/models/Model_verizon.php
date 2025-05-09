<?php

class Model_verizon extends Base_model
{
    private $token_session = array();
    private $token_count = 0;
    private $session_count = 0;

    public function __construct()
    {
        parent::__construct();
        $this->table = "tbl_verizon_api_token";
        $this->table_failed = "tbl_verizon_api_failed";

        $this->token_session = $this->getOne();
        if (!is_array($this->token_session) || empty($this->token_session)) {
            $this->db->replace($this->table, array(
                'token' => 'a',
                'session' => 'a',
            ));
            $this->token_session = $this->getOne();
        }

        $this->load->model('model_livecamera');
    }

    public function getOne()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function api_get_device($IMEI)
    {
        $token = $this->token_session['token'];
        $session = $this->token_session['session'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thingspace.verizon.com/api/m2m/v1/devices/actions/list',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                    "carrierNameFilter": "Verizon Wireless",
                    "filter": {
                        "deviceIdentifierFilters": [
                            {
                                "kind": "imei",
                                "contains": "' . $IMEI . '"
                            }
                        ]
                    }
                }',
            CURLOPT_HTTPHEADER => array(
                "VZ-M2M-Token: $session",
                "Authorization: Bearer $token",
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode == 401) {
            if ($this->token_count == 0) {
                $this->api_get_token();
                return $this->api_get_device($IMEI);
            }
        } else if ($statusCode == 400) {
            $response_json = json_decode($response, TRUE);
            if ($response_json['errorCode'] == "UnifiedWebService.REQUEST_FAILED.SessionToken.Expired" || $response_json['errorCode'] == "UnifiedWebService.INPUT_INVALID.SessionToken.Invalid") {
                if ($this->session_count < 2) {
                    $this->api_get_session();
                    return $this->api_get_device($IMEI);
                }
            } else {
                $this->db->replace($this->table_failed, array(
                    'errorCode' => $response_json['errorCode'],
                    'errorMessage' => $response_json['errorMessage'],
                    'IMEI' => $IMEI,
                ));
            }
        } else {
            // echo $response;
            $response_json = json_decode($response, TRUE);
            $devices =  $response_json['devices'];
            if (sizeof($devices) == 1) {
                $state = $devices[0]['carrierInformations'][0]['state'];
                return $state;
            } else {
                $this->db->replace($this->table_failed, array(
                    'errorCode' => 'Invalid.IMEI',
                    'errorMessage' => 'IMEI does not exist',
                    'IMEI' => $IMEI,
                    'api' => 'api_get_device'
                ));
            }
        }
        return false;
    }

    public function device_activate($device)
    {
        $token = $this->token_session['token'];
        $session = $this->token_session['session'];

        $state = $this->api_get_device($device["IMEI"]);
        if ($state != null) {
            // echo $state;
            if ($state == 'active' || $state == 'pending activation')
                return; // already active;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thingspace.verizon.com/api/m2m/v1/devices/actions/activate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "devices":[
                  {
                    "deviceIds":[
                      {
                        "kind":"imei",
                        "id":"' . $device['IMEI'] . '"
                      }
                      ,
                      {
                        "kind":"iccid",
                        "id":"' . $device['iccid'] . '"
                      }
                    ]
                  }
                ],
                "servicePlan":"DTM2MData",
                "mdnZipCode":"98801"
              }',
            CURLOPT_HTTPHEADER => array(
                "VZ-M2M-Token: $session",
                "Authorization: Bearer $token",
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode == 401) {
            if ($this->token_count == 0) {
                $this->api_get_token();
                return $this->device_activate($device);
            }
        } else if ($statusCode == 400) {
            $response_json = json_decode($response, TRUE);
            if ($response_json['errorCode'] == "UnifiedWebService.REQUEST_FAILED.SessionToken.Expired" || $response_json['errorCode'] == "UnifiedWebService.INPUT_INVALID.SessionToken.Invalid") {
                if ($this->session_count < 2) {
                    $this->api_get_session();
                    return $this->device_activate($device);
                }
            } else {
                $this->db->replace($this->table_failed, array(
                    'errorCode' => $response_json['errorCode'],
                    'errorMessage' => $response_json['errorMessage'],
                    'IMEI' => $device['IMEI'],
                    'iccid' => $device["iccid"],
                    'api' => 'device_activate'
                ));
            }
        } else {
        }
    }

    public function device_deactivate($device)
    {
        $token = $this->token_session['token'];
        $session = $this->token_session['session'];

        $state = $this->api_get_device($device["IMEI"]);
        if ($state != null) {
            // echo $state;
            if ($state == 'deactive' || $state == 'pending deactivation')
                return; // already active;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thingspace.verizon.com/api/m2m/v1/devices/actions/deactivate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "devices":[
                  {
                    "deviceIds":[
                      {
                        "kind":"imei",
                        "id":"' . $device['IMEI'] . '"
                      }
                    ]
                  }
                ],
                "reasonCode": "FF",
                "deleteAfterDeactivation": false
              }',
            CURLOPT_HTTPHEADER => array(
                "VZ-M2M-Token: $session",
                "Authorization: Bearer $token",
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode == 401) {
            if ($this->token_count == 0) {
                $this->api_get_token();
                return $this->device_deactivate($device);
            }
        } else if ($statusCode == 400) {
            $response_json = json_decode($response, TRUE);
            if ($response_json['errorCode'] == "UnifiedWebService.REQUEST_FAILED.SessionToken.Expired" || $response_json['errorCode'] == "UnifiedWebService.INPUT_INVALID.SessionToken.Invalid") {
                if ($this->session_count < 2) {
                    $this->api_get_session();
                    return $this->device_deactivate($device);
                }
            } else {
                $this->db->replace($this->table_failed, array(
                    'errorCode' => $response_json['errorCode'],
                    'errorMessage' => $response_json['errorMessage'],
                    'IMEI' => $device['IMEI'],
                    'iccid' => $device["iccid"],
                    'api' => 'device_deactivate'
                ));
            }
        } else {
        }
    }

    public function send_sms($device, $message)
    {
        $token = $this->token_session['token'];
        $session = $this->token_session['session'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thingspace.verizon.com/api/m2m/v1/sms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '
            {
                "deviceIds": [
                  {
                    "id": "' . $device["IMEI"] . '",
                    "kind": "IMEI"
                  }
                ],
                "smsMessage": "' . $message . '"
              }',
            CURLOPT_HTTPHEADER => array(
                "VZ-M2M-Token: $session",
                "Authorization: Bearer $token",
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode == 401) {
            if ($this->token_count == 0) {
                $this->api_get_token();
                return $this->send_sms($device, $message);
            }
        } else if ($statusCode == 400) {
            $response_json = json_decode($response, TRUE);
            if ($response_json['errorCode'] == "UnifiedWebService.REQUEST_FAILED.SessionToken.Expired" || $response_json['errorCode'] == "UnifiedWebService.INPUT_INVALID.SessionToken.Invalid") {
                if ($this->session_count < 2) {
                    $this->api_get_session();
                    return $this->send_sms($device, $message);
                }
            } else {
                $this->db->replace($this->table_failed, array(
                    'errorCode' => $response_json['errorCode'],
                    'errorMessage' => $response_json['errorMessage'],
                    'IMEI' => $device["IMEI"],
                    'iccid' => $device["iccid"],
                    'api' => 'send_sms'
                ));
            }
        }

        $this->db->replace($this->table_history_sms, array(
            'errorCode' => $statusCode,
            'response' => $response,
            'IMEI' => $device["IMEI"],
            'iccid' => $device["iccid"],
            'carrier' => 'Verizon',
            'message' => $message,
            'api' => 'send_sms'
        ));

        return $response;
    }

    public function group_create($group_name, $group_description, $devices)
    {
        $token = $this->token_session['token'];
        $session = $this->token_session['session'];

        $data = array(
            "accountName" => "0442421605-00001",
            "groupName" => $group_name,
            "groupDescription" => $group_description,
            "devicesToAdd" => array()
        );

        foreach ($devices as $device) {
            $item = array(
                "kind" => "imei",
                "id" => $device['IMEI']
            );
            array_push($data['devicesToAdd'], $item);
        }

        $data_string = json_encode($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thingspace.verizon.com/api/m2m/v1/groups',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                "VZ-M2M-Token: $session",
                "Authorization: Bearer $token",
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode == 401) {
            if ($this->token_count == 0) {
                $this->api_get_token();
                return $this->group_create($group_name, $group_description, $devices);
            }
        } else if ($statusCode == 400) {
            $response_json = json_decode($response, TRUE);
            if ($response_json['errorCode'] == "UnifiedWebService.REQUEST_FAILED.SessionToken.Expired" || $response_json['errorCode'] == "UnifiedWebService.INPUT_INVALID.SessionToken.Invalid") {
                if ($this->session_count < 2) {
                    $this->api_get_session();
                    return $this->group_create($group_name, $group_description, $devices);
                }
            } else {
                $this->db->replace($this->table_failed, array(
                    'errorCode' => $response_json['errorCode'],
                    'errorMessage' => $response_json['errorMessage'],
                    'IMEI' => $device['IMEI'],
                    'iccid' => $device["iccid"],
                    'api' => 'device_deactivate'
                ));
            }
        } else {
        }
    }

    public function group_update($group_name, $group_description, $devices)
    {
        $token = $this->token_session['token'];
        $session = $this->token_session['session'];

        $data = array(
            "accountName" => "0442421605-00001",
            "groupName" => $group_name,
            "groupDescription" => $group_description,
            "devicesToAdd" => array()
        );

        foreach ($devices as $device) {
            $item = array(
                "kind" => "imei",
                "id" => $device['IMEI']
            );
            array_push($data['devicesToAdd'], $item);
        }

        $data_string = json_encode($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thingspace.verizon.com/api/m2m/v1/groups/0442421605-00001/name/' . $group_name,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                "VZ-M2M-Token: $session",
                "Authorization: Bearer $token",
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode == 401) {
            if ($this->token_count == 0) {
                $this->api_get_token();
                return $this->group_update($group_name, $group_description, $devices);
            }
        } else if ($statusCode == 400) {
            $response_json = json_decode($response, TRUE);
            if ($response_json['errorCode'] == "UnifiedWebService.REQUEST_FAILED.SessionToken.Expired" || $response_json['errorCode'] == "UnifiedWebService.INPUT_INVALID.SessionToken.Invalid") {
                if ($this->session_count < 2) {
                    $this->api_get_session();
                    return $this->group_update($group_name, $group_description, $devices);
                }
            } else {
                $this->db->replace($this->table_failed, array(
                    'errorCode' => $response_json['errorCode'],
                    'errorMessage' => $response_json['errorMessage'],
                    'IMEI' => $device['IMEI'],
                    'iccid' => $device["iccid"],
                    'api' => 'device_deactivate'
                ));
            }
        } else {
        }
    }

    // private functions
    private function api_get_token()
    {
        $this->token_count++;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thingspace.verizon.com/api/ts/v1/oauth2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic YTEwNzYyNmYtNjExNy00ZGNhLTgxMGItMTc4Y2IzNDQxYWFmOmY0N2YwOTdjLWU0NTUtNDAxOC1hMWQ5LWIxNzM3M2IwMDY5Yg==',
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: ts-web-ingress-route=1663074822.296.60.781342|46431d97474db5ac57e000cd39f69cd1; token="bearer MTc0N2MxNDMtNWExYy00YTQ1LWFmYmItNGFlMTNkYzNlYTJh"'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $response_json = json_decode($response, TRUE);
        $access_token =  $response_json['access_token'];

        $this->token_session['token'] = $access_token;
        $this->db->replace($this->table, $this->token_session);
    }

    private function api_get_session()
    {
        $this->session_count++;
        $token = $this->token_session['token'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thingspace.verizon.com/api/m2m/v1/session/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                    "username": "developmentWE",
                    "password": "WiseEye3#"
                }',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token",
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode == 401) {
            if ($this->token_count == 0) {
                $this->api_get_token();
                if ($this->session_count < 2) {
                    $this->api_get_session();
                }
            }
        } else {
            $response_json = json_decode($response, TRUE);
            $sessionToken =  $response_json['sessionToken'];

            $this->token_session['session'] = $sessionToken;
            $this->db->replace($this->table, $this->token_session);
        }
    }
}
