<?php 
/**
 * Base Model
 * Created by: thepolygongo
 * Date: 24/7/19
 *
 */
class Base_Model extends CI_Model {
	
	public function add($table, $bind) {
		$query = $this->db->insert($table, $bind);
	
		if($query) {
			return $this->db->insert_id();
		}
		else {
			return false;
		}
	}
	
	public function delete($table, $where) {
		return $this->db->delete($table, $where);
	}
	
	public function delete_not_in($table, $where) {
		$this->db->where_not_in($where['field'], $where['values']);
		return $this->db->delete($table);
	}
	
	public function update($table, $bind, $where) {
		return $this->db->update($table, $bind, $where);
	}
	
	public function get($table, $where = array(), $limit=false) {
		if(!empty($where))
			$this->db->where($where);
		
		if($limit==1) {
			$query = $this->db->get($table, 1);
			return $query->row_array();
		}
		elseif($limit) {
			$query = $this->db->get($table, $limit);
			return $query->result_array();
		}
		else {
			$query = $this->db->get($table);
			return $query->result_array();
		}
	}
	
	public function total_count()
	{
		$sql = "SELECT COUNT(id) as total_count FROM $this->table  WHERE 1";
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return $result['total_count'];
	}
}
