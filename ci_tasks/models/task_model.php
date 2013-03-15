<?php

class Task_model extends CI_Model {

	public function get_tasks($user_id, $layout, $uri_date = NULL) {

		if($uri_date) {
			$join = "JOIN"; // date completed pages should not show empty categories
			$completed_or_not = 1;
			$date_completed_condition = " AND tasks.date_completed = '$uri_date'";
		} else {
			$join = "RIGHT JOIN";
			$completed_or_not = 0;
			$date_completed_condition = "";
		}

    $query =
      "SELECT * FROM (
        SELECT categories.category, categories.column, tasks.id as task_id, tasks.task, tasks.important, tasks.link_href, tasks.link_text, tasks.order, categories.id as cat_id, tasks.completed, categories.user_id, categories.display
        FROM tasks
        $join categories ON tasks.category_id = categories.id
        AND tasks.user_id = $user_id
        AND tasks.completed = $completed_or_not
        $date_completed_condition
      ) AS t
      WHERE t.user_id = $user_id
      AND t.display = 1";

		$rows = $this->db->query($query)->result();

		$data = array();

		if($layout == 1) {

			foreach($rows as $row) {

				$task = array(
					'id' 			=> $row->task_id,
					'completed'		=> $row->completed,
					'task' 			=> $row->task,
					'important' 	=> $row->important,
					'link_href' 	=> $row->link_href,
					'link_text' 	=> $row->link_text,
          'order' => $row->order
				);

				$data[$row->cat_id]['cat_id'] = $row->cat_id;
				$data[$row->cat_id]['cat_name'] = $row->category;

				if($task['id']) {
					$data[$row->cat_id]['tasks'][] = $task;
				}

			}

		} else {

			foreach($rows as $row) {

				$task = array(
					'id' 			=> $row->task_id,
					'completed'		=> $row->completed,
					'task' 			=> $row->task,
					'important' 	=> $row->important,
					'link_href' 	=> $row->link_href,
					'link_text' 	=> $row->link_text
				);

				$data[$row->column][$row->cat_id]['cat_id'] = $row->cat_id;
				$data[$row->column][$row->cat_id]['cat_name'] = $row->category;

				if($task['id']) {
					$data[$row->column][$row->cat_id]['tasks'][] = $task;
				}

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
