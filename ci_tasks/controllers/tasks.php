<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tasks extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    
	    if ( !$this->session->userdata('tasks_logged_in') ) {
	        redirect('login');
	    }
	    
		$this->user = $this->session->userdata('tasks_username');
		$this->user_id = $this->session->userdata('tasks_user_id');
	    
		$this->load->model('tasks_model');
	}
       	
	public function index()
	{
		$this->load->library('taskdate');
				
		$data = array(
			'title' => ucfirst( $this->user ) . '\'s Tasks',
			'body_class' => 'incomplete-tasks',
			'date' => $this->taskdate->current_date(),
			'tasks' => $this->tasks_model->get_incomplete_tasks($this->user_id)
		);
		
		$this->load->view('tasks_view', $data);
	}
	
	public function completed()
	{
		$uri_date = $this->uri->segment(3);
		$this->load->library('taskdate');
		
		$data = array(
			'body_class' => 'complete-tasks',
			'date' => $this->taskdate->past_date($uri_date),
			'tasks' => $this->tasks_model->get_tasks_by_date($this->user_id, $uri_date)
		);
		$data['title'] = ucfirst( $this->user ) . '\'s Tasks - ' . $data['date']['today_long'];
		
		$this->load->view('tasks_view', $data);
	}
	
	public function sort_tasks()
	{
		$tasks = $this->input->post();
		$this->tasks_model->update_task_order($tasks['task']);
	}
	
	public function create()
	{
		$new_task = array(
			'completed' => 0,
			'task' 		=> '',
			'user_id'	=> $this->user_id
		);
		$data['tasks'] = $this->tasks_model->create_task($new_task);
		$this->load->view('_tasks', $data);
	}

	public function update()
	{
		$data = $this->input->post();
		$this->tasks_model->update_task($data);
	}
	
	public function destroy()
	{
		$this->tasks_model->destroy_task();
	}
	
}