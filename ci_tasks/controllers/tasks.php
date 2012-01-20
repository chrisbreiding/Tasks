<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tasks extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    
	    if ( !$this->session->userdata('tasks_logged_in') ) {
	        redirect('login');
	    }
	    
		$this->user_id = $this->session->userdata('tasks_user_id');

		$this->load->model('user_model');
		$user_info = $this->user_model->get_user_info($this->user_id);
		$this->user = $user_info->username;
		$this->layout = $user_info->layout;
	    
		$this->load->model('task_model');
	}
       	
	public function index()
	{
		$this->load->library('taskdate');
				
		$data = array(
			'title' => ucfirst( $this->user ) . '\'s Tasks',
			'body_class' => 'tasks incomplete-tasks layout-' . $this->layout,
			'date' => $this->taskdate->current_date(),
			'columns' => $this->layout,
			'task_data' => $this->task_model->get_tasks($this->user_id, $this->layout)
		);
		
		$this->load->view('tasks_view', $data);
	}
	
	public function completed()
	{
		$uri_date = $this->uri->segment(3);
		
		if ( !$uri_date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $uri_date) ) {
			show_404($this->uri->uri_string());
		}
		
		$this->load->library('taskdate');
		
		$data = array(
			'body_class' => 'tasks complete-tasks layout-' . $this->layout,
			'date' => $this->taskdate->past_date($uri_date),
			'columns' => $this->layout,
			'task_data' => $this->task_model->get_tasks($this->user_id, $this->layout, $uri_date)
		);
		$data['title'] = ucfirst( $this->user ) . '\'s Tasks - ' . $data['date']['today_long'];
		
		$this->load->view('tasks_view', $data);
	}
	
	public function sort_tasks()
	{
		$tasks = $this->input->post();
		$this->task_model->update_task_order($tasks['task']);
	}
	
	public function task_creator() 
	{
		$this->load->model('category_model');
		$data['categories'] = $this->category_model->get_categories($this->user_id);
		$this->load->view('_task_creator', $data);
	}
	
	public function create()
	{
		$new_task = $this->input->post();
		$new_task['completed'] = 0;
		$new_task['user_id'] = $this->user_id;

		$data['tasks'] = $this->task_model->create_task($new_task);
		$this->load->view('_tasks', $data);
	}

	public function update()
	{
		$data = $this->input->post();
		$this->task_model->update_task($data);
	}
	
	public function destroy()
	{
		$this->task_model->destroy_task();
	}
	
}