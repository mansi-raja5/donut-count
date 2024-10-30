<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class StoreController extends CI_Controller {

    protected $key = 'eemTSXWd99S12oEQLGSnI6qe8yBz5gTlhauce82WV8Hb5x5DyfgNro1b9G2/ZkIE7k2EOO5EC/9RtBeVO9cHzg==';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('StoreMasterModel');
    }

    private function response($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function getAllStores() {
        $stores = $this->StoreMasterModel->getAllStores();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($stores));
    }

    public function getAllUserStores(){
        // Get JWT token from Authorization header
        $headers = $this->input->request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if ($authHeader) {
            list($token) = sscanf($authHeader, 'Bearer %s');

            try {
                $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

                // Token is valid, extract user ID
                $userId = $decoded->sub;

                $stores = $this->StoreMasterModel->getAllStores($userId);
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
}
