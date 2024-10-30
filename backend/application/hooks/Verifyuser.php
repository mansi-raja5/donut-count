<?php
class Verifyuser  {
    function checkuser() {
        $this->CI =& get_instance();

        // Check if the request is for the API
        if ($this->isApiRequest($this->CI->uri->uri_string())) {
            return;  // Skip authentication for API requests
        }

		if($this->CI->router->class == 'login'){
       	 return;
	    } 
	    if (!isset($this->CI->session)){
	        $this->CI->load->library('session');
	    } 
	    if(!$this->CI->session->userdata('username') &&
		 !$this->CI->session->userdata('usertype') ){
		    redirect(site_url('login'));
		}
    }

    private function isApiRequest($uri) {
//        echo $uri;
//        echo strpos($uri, 'api/');die;
        return strpos($uri, 'api/') !== false;
    }
}
?>