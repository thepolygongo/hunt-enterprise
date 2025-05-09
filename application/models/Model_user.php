<?php

class Model_user extends Base_Model
{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_admin';
        $this->load->model('model_pro_manager');
        $this->load->model('model_pro_user');
    }

	public function create($data)
	{
		$user = $this->getByName($data['name']);
		if (!empty($user)) {
			return "Username already exist, Please use other name.";
		} else {
			$user = $this->getByEmail($data['email']);
			if (!empty($user)) {
				return "Email already exist, Please try to reset password.";
			}
			$admin_user = $this->session->userdata('admin_user');
			if($data['account_type'] == 'pro_manager') {
				$user_id = $this->add($this->table, $data);
				$org_id = $admin_user['id'];
				$this->model_pro_manager->add_user(array(
					'user_id' => $user_id,
					'org_id' => $org_id,
				));
			} else if($data['account_type'] == 'pro_user') {
				$user_id = $this->add($this->table, $data);
				if($admin_user['account_type'] == 'pro_organization') {
					$org_id = $admin_user['id'];
					$manager_id = 0;
				} else if($admin_user['account_type'] == 'pro_manager') {
					$manager_id = $admin_user['id'];
					$user = $this->model_pro_manager->getByManagerId($manager_id);
					$org_id = $user['org_id'];
				}
				$this->model_pro_user->add_user(array(
					'user_id' => $user_id,
					'org_id' => $org_id,
					'manager_id' => $manager_id,
				));
			}

			return "success";
		}
	}

    public function getByName($name)
    {
        $this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('name', $name);
		$query = $this->db->get();
		return $query->row_array();
    }

    public function getById($arg)
	{
		$this->db->select('*');
		$this->db->where('id', $arg);
		$query = $this->db->get($this->table);
		return $query->row_array();
	}

	public function getPro($email)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('email', $email);
		$this->db->where('account_type', 'pro_organization');
		$this->db->or_where('account_type', 'pro_manager');
		$query = $this->db->get();
		return $query->row_array();
	}

	public function getByEmail($email)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('email', $email);
		$query = $this->db->get();
		return $query->row_array();
	}

    public function getAllSortByEmail()
	{
		$sql = "SELECT * FROM `tbl_admin` WHERE 1 ORDER BY email asc";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function authenticate($email, $password)
	{
		$user = $this->getPro($email);
		if (!empty($user)) {
			if ($password == $user['password']) {
				return $user;
			} else {
				return "password incorect";
			}
		} else {
			return "The email doesn't exist";
		}
	}

    // public function add_user($data)
    // {
    //     $this->db->insert($this->table, $data);
	// 	$insert_id = $this->db->insert_id();
	// 	return $insert_id;
    // }

    public function update_user($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_user($id)
    {
        return $this->db->delete($this->table, array('id' => $id));
    }

    public function getTotalCount($filterOpt)
	{
		$admin_user = $this->session->userdata('admin_user');
		$sql = "SELECT COUNT(id) as item_count FROM tbl_pro_user WHERE ";
		if ($admin_user['account_type'] == 'pro_organization'){
			$org_id = $admin_user['id'];
			$sql .= "org_id = $org_id AND ( ";
		} else if ($admin_user['account_type'] == 'pro_manager') {
			$manager_id = $admin_user['id'];
			$sql .= "manager_id = $manager_id AND ( ";
		}
		$sql .= 'id LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'user_id LIKE "%' . $filterOpt['search'] . '%" ) ';
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return $result['item_count'];
	}

    public function getSearchData($filterOpt)
	{
		$admin_user = $this->session->userdata('admin_user');
		$sql = "SELECT *, UNIX_TIMESTAMP(created_at) as created_at FROM tbl_pro_user WHERE ";
		if ($admin_user['account_type'] == 'pro_organization'){
			$org_id = $admin_user['id'];
			$sql .= "org_id = $org_id AND ( ";
		} else if ($admin_user['account_type'] == 'pro_manager') {
			$manager_id = $admin_user['id'];
			$sql .= "manager_id = $manager_id AND ( ";
		}
		
		$sql .= 'id LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'user_id LIKE "%' . $filterOpt['search'] . '%" ) ';
		$sql .= " ORDER BY " . $filterOpt['order_field'] . " " . $filterOpt['order_dir'] . " ";
		$sql .= " LIMIT " . $filterOpt['length'] . " OFFSET " . $filterOpt['start'];
		$query = $this->db->query($sql);
		$users = $query->result_array();
		return $users;
	}
    
    public function replaceData($data)
	{
		return $this->db->replace($this->table, $data);
	}

    public function getByIDs($arg)
	{
		$filter = " id IN (" . implode(',', $arg) . ")";
		$sql = "select * from $this->table where $filter";
		$query = $this->db->query($sql);
		$items = $query->result_array();
		return $items;
	}
}
?>