<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    
	    if ( !$this->session->userdata('tasks_logged_in') ) {
	        redirect('login');
	    }
	    
		$this->user_id = $this->session->userdata('tasks_user_id');

		$this->load->model('user_model');
		$this->user_info = $this->user_model->get_user_info($this->user_id);	    
	}
       	
	public function index()
	{
		$this->load->model('category_model');
		$data = array(
			'title' => ucfirst( $this->user_info->username ) . '\'s Settings',
			'body_class' => 'settings layout-' . $this->user_info->layout,
			'user_data' => $this->user_info,
			'category_data' => $this->category_model->get_category_info($this->user_id, $this->user_info->layout)
		);
		
		$this->load->view('settings_view', $data);
	}
	
}