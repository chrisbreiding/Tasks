<?php

class Task_model extends CI_Model {
	
	public function get_tasks($user_id, $layout, $uri_date = NULL) {
	
		$completed_or_not = $uri_date ? 1 : 0;
		
		$query = 
			"SELECT categories.category, categories.column, tasks.id, tasks.task, tasks.important, tasks.link_href, tasks.link_text, tasks.category_id, tasks.completed
			FROM tasks, categories 
			WHERE categories.id = tasks.category_id 
			AND categories.user_id = $user_id
			AND tasks.user_id = $user_id
			AND categories.display = 1 
			AND tasks.completed = $completed_or_not";
			
		if($uri_date) {
			$query .= 
				" AND tasks.date_completed = '$uri_date'";
		}
		
		$query .= " ORDER BY categories.order, tasks.order ASC";
		
		$rows = $this->db->query($query)->result();
					
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
	
	public function update_task_order($tasks) {
		foreach( $tasks as $order => $id) {
			$this->db->where('id', $id);
			$this->db->update('tasks', array( 'order' => $order ));
		}
	}
	
	public function create_task($new_task) {
	
		$this->db->insert('tasks', $new_task);
		
		$id = $this->db->insert_id();
		
		$row = $this->db->select('id, completed, task, important, link_href, link_text')
				->from('tasks')
				->where( array( 'id' => $id ) )
				->limit(1)
				->get()
				->row();
				
		$tasks[0] = array(
			'id' 			=> $row->id,
			'completed'		=> $row->completed,
			'task' 			=> $row->task,
			'important' 	=> $row->important,
			'link_href' 	=> $row->link_href,
			'link_text' 	=> $row->link_text
		);
		
		return $tasks;
		
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