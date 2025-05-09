<?php

class Model_pro_camera extends Base_Model
{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_pro_camera';
		$this->load->model('model_user');
		$this->load->model('model_pro_manager');
    }

	public function getByCameraId($camera_id)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('camera_id', $camera_id);
		$this->db->where('user_id', 0);
		$query = $this->db->get();
		return $query->row_array();
	}

	public function getById($id)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row_array();
	}

	public function getByOrgId($org_id)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('org_id', $org_id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function getByManagerId($manager_id)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('manager_id', $manager_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function assignCamera($camera_id, $user_id)
	{
		$this->db->set('user_id', $user_id);
		$this->db->where('camera_id', $camera_id);
        return $this->db->update($this->table);
	}

    public function add_camera($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update_camera($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_camera($id)
    {
        return $this->db->delete($this->table, array('id' => $id));
    }

    public function getTotalCount($filterOpt)
	{
		$admin_user = $this->session->userdata('admin_user');
		if ($admin_user['account_type'] == 'pro_organization') {
			$org_id = $admin_user['id'];
		} else if ($admin_user['account_type'] == 'pro_manager') {
			$manager = $this->model_pro_manager->getUserId($admin_user['id']);
			$org_id = $manager['org_id'];
		}
		$org_id = $admin_user['id'];
		$sql = "SELECT COUNT(id) as item_count FROM $this->table WHERE ";
		$sql .= "org_id = $org_id AND ( ";
		$sql .= 'id LIKE "%' . $filterOpt['search'] . '%" OR ';		
		$sql .= 'user_id LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'camera_id LIKE "%' . $filterOpt['search'] . '%" )';
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return $result['item_count'];
	}

    public function getSearchData($filterOpt)
	{
		$admin_user = $this->session->userdata('admin_user');
		if ($admin_user['account_type'] == 'pro_organization') {
			$org_id = $admin_user['id'];
		} else if ($admin_user['account_type'] == 'pro_manager') {
			$manager = $this->model_pro_manager->getUserId($admin_user['id']);
			$org_id = $manager['org_id'];
		}
		$sql = "SELECT *, UNIX_TIMESTAMP(created_at) as created_at FROM $this->table WHERE ";
		$sql .= "org_id = $org_id AND ( ";
		$sql .= 'id LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'user_id LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'camera_id LIKE "%' . $filterOpt['search'] . '%" )';
		$sql .= " ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
		$sql .= " LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
		$query = $this->db->query($sql);
		$users = $query->result_array();
		return $users;
	}

	public function getListByUser($filterOpt)
	{
		$user_id = $filterOpt['user_id'];
		$sql = 'SELECT * FROM ' . $this->table . ' WHERE ';
		$sql .= "user_id = $user_id ";
		$sql .= "ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
		$sql .= "LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCountByUser($filterOpt)
	{
		$user_id = $filterOpt['user_id'];
		$sql = 'SELECT  count(id) as total_count FROM ' . $this->table . ' WHERE ';
		$sql .= "user_id = $user_id";
		$query = $this->db->query($sql);
		$data = $query->row_array();
		return (int) $data['total_count'];
	}
    
    public function replaceData($data)
	{
		return $this->db->replace($this->table, $data);
	}
}
?>