<?php

class Tasks_model extends CI_Model {
	
	function get_incomplete_tasks($user_id) {
		$q = $this->db->order_by('order', 'asc')->get_where( 'tasks', array( 'user_id' => $user_id, 'completed' => 0 ) );
		return $q->result();
	}

	function get_tasks_by_date($user_id, $uri_date) {
		$q = $this->db->order_by('order', 'asc')->get_where( 'tasks', array( 'user_id' => $user_id, 'date_completed' => $uri_date ) );
		return $q->result();
	}
	
	function update_task_order($tasks) {
		foreach( $tasks as $order => $id) {
			$this->db->where('id', $id);
			$this->db->update('tasks', array( 'order' => $order ));
		}
	}
	
	function create_task($new_task) {
		$this->db->insert('tasks', $new_task);
		$id = $this->db->insert_id();
		$q = $this->db->get_where( 'tasks', array( 'id' => $id ) );
		return $q->result();
	}
	
	function update_task($data) {
		$this->db->where('id', $data['id']);
		$this->db->update('tasks', $data);
	}
	
	function destroy_task() {
		$this->db->where('id', $this->uri->segment(3) );
		$this->db->delete('tasks');
	}
	
}