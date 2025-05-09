<?php

class Model_setting_firmware_versions extends Base_model
{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = "tbl_setting_firmware_versions";
    }

    public function is_after_d404($arg)
    {
        if ($arg == "")
            return false;
        $sql = "SELECT * FROM $this->table WHERE type = 'mini' AND version = '$arg'";
        $query = $this->db->query($sql);
        $item =  $query->row_array();
        if (!is_array($item) || empty($item))
            return false;
        if ($item['id'] >= 21)
            return true;
        return false;
    }
}
