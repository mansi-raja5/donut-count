<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_controller'] = function() {
    header('Access-Control-Allow-Origin: *'); // Adjust this to be more restrictive if needed
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Handle preflight request for POST requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        // Return only the headers and not the content
        header('Content-Type: application/json');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header("HTTP/1.1 200 OK");
        exit;
    }
};

$hook['post_controller'] = array(
'class'     => 'Verifyuser',
'function'  => 'checkuser',
'filename'  => 'Verifyuser.php',
'filepath'  => 'hooks'
);