<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('store_master_model');
    }
    public function index() {
        $data['title'] = 'Store List';
        $data['stores'] = $this->store_master_model->Get(NULL, array("status" => 'A'));
        $this->template->load('default', 'home', $data);
    }
  

}
