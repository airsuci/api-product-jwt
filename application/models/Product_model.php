<?php

class Product_model extends CI_Model
{

	public $product_name;
	public $product_description;
	public $price;
	public $stock;
	public $user_input;
	public $user_update;
	public $input_date;
	public $update_date;

	public function get_last_entries()
	{
		$query = $this->db->get('product', 10);
		return $query->result();
	}

	public function check_product_name($name)
	{
		$this->db->where('LOWER(product_name)', strtolower($name));
		$query = $this->db->count_all_results('product');
		//print_r($query);exit;
		return $query;
	}

	public function insert($params)
	{
		return $this->db->insert('product', $params);
	}

	public function update($params,$id)
	{
		return $this->db->update('product', $params, array('id' => $id));
	}

	public function delete($id)
	{
		return $this->db->delete('product', array('id' => $id));
	}

	public function get_product_detail($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('product');
		return $query->row();
	}

}
