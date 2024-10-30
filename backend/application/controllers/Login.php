<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('login_model');
    }
    public function index() {
    	//if($this->session->userdata('username') =="")
       		 $this->load->view('login');
       	// else
       	// 	redirect("home");
    }
  	public function verify() {
  	 	$data = $this->input->post();
  	 	$result = $this->login_model->verify($data);
  	 	if($result)
        {
        	$this->session->set_userdata('username', $result->username);
            $this->session->set_userdata('usertype', $result->type);
            redirect("home");
        }
        else
        {
            $this->session->set_flashdata('error', 'Invalid username or password. Please try again!!');
            redirect("login");
        }

    }
    public function logout(){
    	$this->session->unset_userdata(array("username","usertype"));
    	redirect('login');
    }

}
