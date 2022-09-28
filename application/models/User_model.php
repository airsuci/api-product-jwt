<?php

class User_model extends CI_Model
{

	public $username;
	public $pass;
	public $email;

	public function get_user_by_username($username)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		return $query->row();
	}

}
