<?php

class Tasks_model extends CI_Model {
	
	public function get_incomplete_tasks($user_id, $layout) {
		if($layout == 1) {
			$q = $this->db->order_by('order', 'asc')->get_where('categories', array('user_id' => $user_id));
			$categories = $q->result();
			$data = array();
			foreach ($categories as $category) {
				$q = $this->db->order_by('order', 'asc')->get_where('tasks', array(
					'user_id' => $user_id, 
					'category_id' => $category->id, 
					'completed' => 0
				));
				$data[] = array(
					'cat_name' => $category->category,
					'cat_id' => $category->id,
					'tasks' => $q->result()
				);
			}
		} else {
			$q_1 = $this->db->order_by('order', 'asc')->get_where('categories', array(
				'user_id' => $user_id, 
				'column' => 1
			));
			$col_1_categories = $q_1->result();
			$q_2 = $this->db->order_by('order', 'asc')->get_where('categories', array(
				'user_id' => $user_id, 
				'column' => 2
			));
			$col_2_categories = $q_2->result();
			$data = array(
				0 => array(),
				1 => array()
			);
			foreach ($col_1_categories as $category) {
				$q = $this->db->order_by('order', 'asc')->get_where('tasks', array(
					'user_id' => $user_id, 
					'category_id' => $category->id, 
					'completed' => 0
				));
				$data[0][] = array(
					'cat_name' => $category->category,
					'cat_id' => $category->id,
					'tasks' => $q->result()
				);
			}
			foreach ($col_2_categories as $category) {
				$q = $this->db->order_by('order', 'asc')->get_where('tasks', array(
					'user_id' => $user_id, 
					'category_id' => $category->id, 
					'completed' => 0
				));
				$data[1][] = array(
					'cat_name' => $category->category,
					'cat_id' => $category->id,
					'tasks' => $q->result()
				);
			}
			
		}
		return $data;
	}

	public function get_tasks_by_date($user_id, $uri_date, $layout) {
		if($layout == 1) {
			$q = $this->db->order_by('order', 'asc')->get_where('categories', array('user_id' => $user_id));
			$categories = $q->result();
			$data = array();
			foreach ($categories as $category) {
				$q = $this->db->order_by('order', 'asc')->get_where('tasks', array(
					'user_id' => $user_id, 
					'category_id' => $category->id, 
					'date_completed' => $uri_date
				));
				$data[] = array(
					'cat_name' => $category->category,
					'cat_id' => $category->id,
					'tasks' => $q->result()
				);
			}
		} else {
			$q_1 = $this->db->order_by('order', 'asc')->get_where('categories', array(
				'user_id' => $user_id, 
				'column' => 1
			));
			$col_1_categories = $q_1->result();
			$q_2 = $this->db->order_by('order', 'asc')->get_where('categories', array(
				'user_id' => $user_id, 
				'column' => 2
			));
			$col_2_categories = $q_2->result();
			$data = array(
				0 => array(),
				1 => array()
			);
			foreach ($col_1_categories as $category) {
				$q = $this->db->order_by('order', 'asc')->get_where('tasks', array(
					'user_id' => $user_id, 
					'category_id' => $category->id, 
					'date_completed' => $uri_date
				));
				$data[0][] = array(
					'cat_name' => $category->category,
					'cat_id' => $category->id,
					'tasks' => $q->result()
				);
			}
			foreach ($col_2_categories as $category) {
				$q = $this->db->order_by('order', 'asc')->get_where('tasks', array(
					'user_id' => $user_id, 
					'category_id' => $category->id, 
					'date_completed' => $uri_date
				));
				$data[1][] = array(
					'cat_name' => $category->category,
					'cat_id' => $category->id,
					'tasks' => $q->result()
				);
			}
			
		}
		return $data;
	}
	
	public function get_categories($user_id) {
		$q = $this->db->order_by('order', 'asc')->get_where('categories', array('user_id' => $user_id));
		$categories = $q->result();
		$data = array(
			'ids' => array(),
			'cats' => array()
		);
		foreach($categories as $category) {
			$data['ids'][] = $category->id;
			$data['cats'][$category->id] = $category->category;
		}
		return $data;
	}
	
	public function update_task_order($tasks) {
		foreach( $tasks as $order => $id) {
			$this->db->where('id', $id);
			$this->db->update('tasks', array( 'order' => $order ));
		}
	}
	
	public function create_task($new_task) {
		$this->db->insert('tasks', $new_task);
		$id = $this->db->insert_id();
		$q = $this->db->get_where( 'tasks', array( 'id' => $id ) );
		return $q->result();
	}
	
	public function update_task($data) {
		$this->db->where('id', $data['id']);
		$this->db->update('tasks', $data);
	}
	
	public function destroy_task() {
		$this->db->where('id', $this->uri->segment(3) );
		$this->db->delete('tasks');
	}
	
}