<?php

class Task_model extends CI_Model {
	
	public function get_incomplete_tasks($user_id, $layout) {
		$q = $this->db->query(
			"SELECT categories.category, categories.column, tasks.id, tasks.task, tasks.important, tasks.link_href, tasks.link_text, tasks.category_id, tasks.completed 
			FROM tasks, categories 
			WHERE categories.id = tasks.category_id 
			AND categories.user_id = $user_id
			AND tasks.user_id = $user_id
			AND categories.display = 1 
			AND tasks.completed = 0 
			ORDER BY categories.order, tasks.order ASC"
		);
		
		$rows = $q->result();
					
		$data = array();
			
		if($layout == 1) {
			foreach($rows as $row) {
				$task = array(
					'id' 			=> $row->id,
					'completed'		=> $row->completed,
					'task' 			=> $row->task,
					'important' 	=> $row->important,
					'link_href' 	=> $row->link_href,
					'link_text' 	=> $row->link_text
				);
				$data[$row->category_id]['cat_id'] = $row->category_id;
				$data[$row->category_id]['cat_name'] = $row->category;
				$data[$row->category_id]['tasks'][] = $task;
			}

		} else {
			
			foreach($rows as $row) {
				$task = array(
					'id' 			=> $row->id,
					'completed'		=> $row->completed,
					'task' 			=> $row->task,
					'important' 	=> $row->important,
					'link_href' 	=> $row->link_href,
					'link_text' 	=> $row->link_text
				);
				$data[$row->column][$row->category_id]['cat_id'] = $row->category_id;
				$data[$row->column][$row->category_id]['cat_name'] = $row->category;
				$data[$row->column][$row->category_id]['tasks'][] = $task;
			}

			ksort($data);
			
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
				if($q->num_rows() > 0) {
					$data[] = array(
						'cat_name' => $category->category,
						'cat_id' => $category->id,
						'tasks' => $q->result()
					);
				}
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
				if($q->num_rows() > 0) {
					$data[0][] = array(
						'cat_name' => $category->category,
						'cat_id' => $category->id,
						'tasks' => $q->result()
					);
				}
			}
			foreach ($col_2_categories as $category) {
				$q = $this->db->order_by('order', 'asc')->get_where('tasks', array(
					'user_id' => $user_id, 
					'category_id' => $category->id, 
					'date_completed' => $uri_date
				));
				if($q->num_rows() > 0) {
					$data[1][] = array(
						'cat_name' => $category->category,
						'cat_id' => $category->id,
						'tasks' => $q->result()
					);
				}
			}
			
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