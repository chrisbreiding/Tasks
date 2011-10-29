<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

   public function __construct()
   {
        parent::__construct();
   }
       	
	function index()
	{
	    if ( $this->session->userdata('tasks_logged_in') ) {
	        redirect('/');
	    }

		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_error_delimiters('<div class="message error">', '</div>');
		
		if ( $this->form_validation->run() ) {
			$this->load->model('user_model');
			
			$res = false;
			$res = $this->user_model->verify_user(
				$this->input->post('username'),
				$this->input->post('password')
			);                
            
			if ( $res !== FALSE ) {
														
				$data = array(
                   'tasks_username'	=> $res->username,
                   'tasks_user_id'	=> $res->id,
                   'tasks_logged_in'  => TRUE
                );

                $this->session->set_userdata($data);
				redirect('/');
				
			} else {
			
		        $this->session->set_flashdata('message', '<div class="message error">Your username or password is incorrect. Please try again.</div>');
		        redirect('login');
		        
		    }

		}
				
		$this->load->view('login_view');
	}

	function logout()
	{
	    $this->session->sess_destroy();
 		$data['notice'] = '<div class="message">You have logged out.</div>';
		$this->load->view('login_view', $data);
	}

}