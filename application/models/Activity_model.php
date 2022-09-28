<?php

class Activity_model extends CI_Model
{

	public $username;
	public $activity_date;
	public $activity;
	public $product_id;
	public $ip;

	public function get_last_activity($limit = 10)
	{
		$query = $this->db->order_by('activity_date', 'DESC')->get('log_activity', $limit)
			;
		return $query->result();
	}

	public function insert($params)
	{
		return $this->db->insert('log_activity', $params);
	}

}
