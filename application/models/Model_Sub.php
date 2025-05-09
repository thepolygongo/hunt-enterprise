<?php

class Model_sub extends Base_model
{
	protected $table;

	public function __construct()
	{
		parent::__construct();
		$this->table = "tbl_main_sub";
	}

	public function getAll()
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$query = $this->db->get();
		return $query->result_array();
	}
    
	public function getSubs($arg)
	{
		$this->db->select('sub_id');
		$this->db->where('main_id', $arg);
		$this->db->from($this->table);
		$query = $this->db->get();
        // echo $this->db->last_query();
		return $query->result_array();
	}
	
	public function deleteByIDs($arg)
	{
		$filter = " id IN (" . implode(',', $arg) . ")";
		$sql = "delete from $this->table where $filter";
		echo $sql;
		$this->db->query($sql);
	}

	public function deleteByMainSub($main_id, $sub_id)
	{
		$this->db->where("main_id", $main_id);
		$this->db->where("sub_id", $sub_id);
		$this->db->delete($this->table);
	}
	
	public function replaceData($data)
	{
		return $this->db->replace($this->table, $data);
	}
}
