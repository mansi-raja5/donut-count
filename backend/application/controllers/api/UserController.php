<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class UserController extends CI_Controller {

    protected $key = 'eemTSXWd99S12oEQLGSnI6qe8yBz5gTlhauce82WV8Hb5x5DyfgNro1b9G2/ZkIE7k2EOO5EC/9RtBeVO9cHzg==';

    public function __construct() {
        parent::__construct();
        $this->load->model('UserStoreModel');
        $this->load->model('UserModel');
        $this->load->library('form_validation');

    }

    public function stores() {
        // Get JWT token from Authorization header
        $headers = $this->input->request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if ($authHeader) {
            list($token) = sscanf($authHeader, 'Bearer %s');

            try {
                $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

                // Token is valid, extract user ID
                $userId = $decoded->sub;

                // Fetch stores by user ID
                $stores = $this->UserStoreModel->getStoresByUserId($userId);

                // Set content type to JSON and return the data
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($stores));

            } catch (ExpiredException $e) {
                // Handle expired token
                $this->response(['status' => 'error', 'message' => 'Token expired'], 401);
            } catch (SignatureInvalidException $e) {
                // Handle invalid token
                $this->response(['status' => 'error', 'message' => 'Invalid token'], 401);
            } catch (UnexpectedValueException $e) {
                // Handle other token decoding errors
                $this->response(['status' => 'error', 'message' => 'Token error: ' . $e->getMessage()], 401);
            } catch (Exception $e) {
                // Handle other exceptions
                $this->response(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }
        } else {
            // No token provided
            $this->response(['status' => 'error', 'message' => 'No token provided'], 401);
        }
    }

    private function isLoggedIn() {
        return $this->session->userdata('logged_in');
    }

    public function register() {
        // Get the JSON POST data
        $postData = json_decode($this->input->raw_input_stream, true);

        // Set validation rules
        $this->form_validation->set_data($postData);
        //$this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[user.username]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[user.email]');
        $this->form_validation->set_rules('store_id', 'Store ID', 'trim|required');
        $this->form_validation->set_rules('role', 'Role', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->response(['status' => 'error', 'message' => validation_errors()]);
        } else {

            //print_r($postData);exit;
            // Prepare user data
            $userData = [
                'username' => $postData['first_name'] . '_' . $postData['last_name'],
                'password' => password_hash($postData['password'], PASSWORD_DEFAULT),
                'name' => $postData['first_name'] . ' ' . $postData['last_name'],
                'role' => $postData['role'],
                'status' => 1, // Assuming 1 is for active status
                'type' => $postData['role'] === 'Manager' ? 'M' : 'E', // Assuming 'M' for Manager, 'E' for Employee
                'email' => $postData['email']
            ];

           // print_r($userData);exit;

            // Insert user data into `user` table
            $userId = $this->UserModel->insertUser($userData);

            if ($userId) {
                if($postData['store_id'] != "all") {
                    $storeKeys = explode(',', $postData['store_id']);
                } else {
                    $this->db->select('key');
                    $query = $this->db->get('store_master');
                    $storeKeys = array_column($query->result_array(), 'key');
                }

                foreach ($storeKeys as $storeKey) {
                    $storeKey = trim($storeKey);

                    $userStoreData = [
                        'user_id' => $userId,
                        'store_key' => $storeKey
                    ];

                    // Insert the user_store data into `user_store` table
                    $this->db->insert('user_store', $userStoreData);
                }

                $this->response(['status' => 'success', 'message' => 'Registration successful']);
            } else {
                $this->response(['status' => 'error', 'message' => 'Failed to register user']);
            }
        }

    }

    private function response($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function login() {
        $data = json_decode($this->input->raw_input_stream, true);

        $email = $data['email'];
        $password = $data['password'];

        $user = $this->UserModel->getUserByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            // Payload data for the JWT
            $payload = [
                'sub' => $user->id, // Subject of the token (user ID)
                'exp' => time() + 60 * 60 * 24, // Expiration time (1 day for example)
                'kid' => 'donut_count', // Key ID
            ];

            // Generate JWT
            $jwt = JWT::encode($payload, $this->key,'HS256');

            // Send JWT back to the user
            $this->response([
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $jwt
                //'user' => $user // Consider excluding sensitive information
            ]);
        } else {
            $this->response(['status' => 'error', 'message' => 'Login failed']);
        }
    }


    public function logout() {
        $headers = $this->input->request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if ($authHeader) {
            list($token) = sscanf($authHeader, 'Bearer %s');

            try {
                $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

                print_r($decoded);exit;
                $tokenId = $decoded->jti;
                $this->response(['status' => 'success', 'message' => 'Logout successful']);

            } catch (ExpiredException $e) {
                // Handle expired token
                $this->response(['status' => 'error', 'message' => 'Token expired'], 401);
            } catch (SignatureInvalidException $e) {
                // Handle invalid token
                $this->response(['status' => 'error', 'message' => 'Invalid token'], 401);
            } catch (UnexpectedValueException $e) {
                // Handle other token decoding errors
                $this->response(['status' => 'error', 'message' => 'Token error: ' . $e->getMessage()], 401);
            } catch (Exception $e) {
                // Handle other exceptions
                $this->response(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }
        } else {
            // No token provided
            $this->response(['status' => 'error', 'message' => 'No token provided'], 401);
        }
    }
}
