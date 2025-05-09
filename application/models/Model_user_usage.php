<?php

class Model_user_usage extends Base_model
{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_admin';
        $this->load->model('model_user');
        $this->load->model('model_camera');
        $this->load->model('model_livecamera');
    }

    public function limitation_by_user($user)
    {
        if ($user['account_type'] == 'Developer' || $user['account_type'] == 'VIP' || $user['account_type'] == 'Support VIP' || $user['account_type'] == 'Ranch') {
            return -1;
        }

        if ($user['jwt_sub_status'] == '') {
            return 0;
        } elseif ($user['jwt_sub_status'] == 'active') {
        } else {
            $today = date('Y-m-d H:i:s');
            if ($user['subscription_cancel_at'] < $today) {
                return 0;
            }
        }
        $sku = parse_sku($user['jwt_sub_sku']);
        $sub_type = $sku['sub_count'] * $sku['quantity'] + $sku['boost_count'] * $user['jwt_sub_databoost'];

        return $sub_type;
    }

    public function datausage_clear_one($user_id)
    {
        $sql = "UPDATE tbl_camera SET data_usage = 0 WHERE user_id = $user_id";
        $this->db->query($sql);
        $sql = "UPDATE tbl_admin SET data_usage = 0 WHERE id = $user_id";
        $this->db->query($sql);
    }

    public function is_smart_available_by_user($user)
    {
        $limitation = $this->limitation_by_user($user);
        if ($limitation == -1) {
            return true;
        } elseif ($limitation == 0) {
            return false;
        } else {
            $data_usage_total = $user['data_usage'];
            if ($data_usage_total >= $limitation) {
                $this->woo_rest_api($user, 100);

                return false;
            } elseif ($data_usage_total >= $limitation * 0.95) {
                $this->woo_rest_api($user, 95);
            } elseif ($data_usage_total >= $limitation * 0.9) {
                $this->woo_rest_api($user, 90);
            } elseif ($data_usage_total >= $limitation * 0.75) {
                $this->woo_rest_api($user, 75);
            } elseif ($data_usage_total >= $limitation * 0.5) {
                $this->woo_rest_api($user, 50);
            }
        }

        return true;
    }

    public function woo_rest_api($user, $limit_type)
    {
        if ($user['datausage_percent'] < $limit_type) {
            if ($limit_type == 100) {
                $this->check_active_cameras_by_user($user, true);
            }
            $user['datausage_percent'] = $limit_type;
            $this->replaceData($user);

            $url = 'https://wiseeyetech.com/';
            $consumerKey = 'ck_f6d81396b504abe95db71f4650dfa8697f56ee9e';
            $consumerSecret = 'cs_2e4cdf58258daf4edfdc274d20ca09bb36088437';
            $woocommerce = new Automattic\WooCommerce\Client(
                $url,
                $consumerKey,
                $consumerSecret,
                [
                    'version' => 'wc/v3',
                ]
            );

            $note = $limit_type . '_data';
            $sub_id = $user['jwt_sub_id'];

            if ($sub_id > 0) {
                print_r($woocommerce->post("subscriptions/$sub_id/notes", ['note' => $note]));
            }
        }
        echo $user['email'] . " limit $limit_type %";
    }

    public function check_active_cameras_by_user($user, $data_usage_limited = false)
    {
        $count = 0;
        $deactivated_reason = 'camrera counts over';
        if ($user['account_type'] == 'Developer' || $user['account_type'] == 'VIP' || $user['account_type'] == 'Support VIP' || $user['account_type'] == 'Ranch') {
            $count = 10000;
        } else {
            if ($user['jwt_sub_status'] == 'active') {
                if ($data_usage_limited) {
                    $deactivated_reason = 'data usage limited';
                } elseif ($this->is_smart_available_by_user($user)) {
                    $sku = parse_sku($user['jwt_sub_sku']);
                    $count = $sku['quantity'];
                } else {
                    $deactivated_reason = 'data usage limited';
                }
            } else {
                $today = date('Y-m-d H:i:s');
                $deactivate = date('Y-m-d H:i:s', strtotime($user['subscription_cancel_at'] . ' + 8 days'));
                if ($deactivate < $today) {
                    $deactivated_reason = 'sub status - ' . $user['jwt_sub_status'];
                } else {
                    if ($data_usage_limited) {
                        $deactivated_reason = 'data usage limited';
                    } elseif ($this->is_smart_available_by_user($user)) {
                        $sku = parse_sku($user['jwt_sub_sku']);
                        $count = $sku['quantity'];
                    } else {
                        $deactivated_reason = 'data usage limited';
                    }
                }
            }
        }
        $active_count = 0;
        $cameras = $this->model_camera->smartcams_by_user($user['id']);
        $devices = [];
        foreach ($cameras as $camera) {
            $device = $this->model_livecamera->getByIMEI($camera['IMEI']);
            if (is_array($device) && !empty($device)) {
                array_push($devices, $device);
            }
        }

        foreach ($devices as $device) {
            if ($active_count >= $count) {
                if (!$this->model_livecamera->is_suspended($device)) {
                    $device = $this->model_livecamera->device_suspend($device, 1);
                    $device['auto_deactivated'] = 1;
                    $device['deactivated_reason'] = $deactivated_reason;
                    $this->model_livecamera->replaceData($device);
                }
            } else {
                if (!$this->model_livecamera->is_suspended($device)) {
                    ++$active_count;
                }
            }
        }

        foreach ($devices as $device) {
            if ($active_count >= $count) {
                break;
            } else {
                if ($this->model_livecamera->is_suspended($device)) {
                    if ($device['auto_deactivated'] == 1) {
                        $device = $this->model_livecamera->device_suspend($device, 0);
                        $device['auto_deactivated'] = 0;
                        $device['deactivated_reason'] = '';
                        $this->model_livecamera->replaceData($device);
                        ++$active_count;
                    }
                }
            }
        }

        return $active_count;
    }

    public function is_available_add_smart($user)
    {
        $sku = parse_sku($user['jwt_sub_sku']);
        if ($user['account_type'] == 'Developer' || $user['account_type'] == 'VIP' || $user['account_type'] == 'Support VIP' || $user['account_type'] == 'Ranch') {
            return true;
        }
        if ($this->model_camera->count_smart_active($user['id']) >= $sku['quantity']) {
            return false;
        }

        return true;
    }

    public function replaceData($data)
    {
        return $this->db->replace($this->table, $data);
    }
}
?>