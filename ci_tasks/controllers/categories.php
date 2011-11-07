<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    
	    if ( !$this->session->userdata('tasks_logged_in') ) {
	        redirect('login');
	    }
	    
		$this->user_id = $this->session->userdata('tasks_user_id');
		$this->load->model('category_model');
	}

	public function create()
	{
		$new_cat = $this->input->post();
		$new_cat['user_id'] = $this->user_id;
		
		$data['categories'] = $this->category_model->create_category($new_cat);
		$this->load->view('_categories', $data);
	}
       	
	public function update()
	{
		$data = $this->input->post();
		$this->category_model->update_category($data);
	}
	
	public function sort_categories()
	{
		$categories = $this->input->post();
		$this->category_model->update_category_order($categories['cat']);
	}
	
}