<?php
class User_model extends CI_Model {

	function verify_user($username, $password) {
		$pass = $username . ':' . sha1($password) . ':CrocmpXxXCpniW2wUGWWq2O4Ftb53p';
		$q = $this->db
				->where('username', $username)
				->where('password', $pass)
				->limit(1)
				->get('users');
		
		if ( $q->num_rows > 0 ) {
			return $q->row();
		}
		
		return false;
	}
	
}