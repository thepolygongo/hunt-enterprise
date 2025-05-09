<?php

class Model_pro_user extends Base_Model
{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_pro_user';
    }

	public function getByEmail($email)
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('email', $email);
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

    public function add_user($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update_manager($id, $data)
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
		$org_id = $admin_user['id'];
		$sql = "SELECT COUNT(id) as item_count FROM $this->table WHERE ";
		$sql .= "org_id = $org_id AND ( ";
		$sql .= 'id LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'email LIKE "%' . $filterOpt['search'] . '%" )';
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return $result['item_count'];
	}

    public function getSearchData($filterOpt)
	{
		$admin_user = $this->session->userdata('admin_user');
		$org_id = $admin_user['id'];
		$sql = "SELECT *, UNIX_TIMESTAMP(created_at) as created_at FROM $this->table WHERE ";
		$sql .= "org_id = $org_id AND ( ";
		$sql .= 'id LIKE "%' . $filterOpt['search'] . '%" OR ';
		$sql .= 'email LIKE "%' . $filterOpt['search'] . '%" )';
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
}
?>