<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SpecialDayController extends CI_Controller {

    protected $key = 'eemTSXWd99S12oEQLGSnI6qe8yBz5gTlhauce82WV8Hb5x5DyfgNro1b9G2/ZkIE7k2EOO5EC/9RtBeVO9cHzg==';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('SpecialDayModel'); // Load your model
        $this->load->helper('url');
        $this->load->model('UserStoreModel');

    }

    private function response($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function addSpecialDay()
    {
        // Get JWT token from Authorization header
        $headers = $this->input->request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if ($authHeader) {
            list($token) = sscanf($authHeader, 'Bearer %s');

            try {
                $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

                // Token is valid, extract user ID
                $userId = $decoded->sub;

                $data = json_decode($this->input->raw_input_stream, true);
                $result = $this->SpecialDayModel->addSpecialDay($data);
                if ($result) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(['message' => 'Special day added successfully']));
                } else {
                    $this->output
                        ->set_status_header(400)
                        ->set_content_type('application/json')
                        ->set_output(json_encode(['error' => 'Failed to add special day']));
                }

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

    public function getSpecialDays()
    {
        // Get JWT token from Authorization header
        $headers = $this->input->request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if ($authHeader) {
            list($token) = sscanf($authHeader, 'Bearer %s');

            try {
                $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

                // Token is valid, extract user ID
                $userId = $decoded->sub;

                $status = $this->input->get('status');
                $name = $this->input->get('name');
                $type = $this->input->get('type');

                $specialDays = $this->SpecialDayModel->getSpecialDays($status, $name, $type);

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($specialDays));

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

    public function getDonutCountsBySpecialDayName() {

        // Get JWT token from Authorization header
        $headers = $this->input->request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if ($authHeader) {
            list($token) = sscanf($authHeader, 'Bearer %s');

            try {
                $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

                // Token is valid, extract user ID
                $userId = $decoded->sub;

                // Retrieve the store keys associated with the user
                $userStores = $this->UserStoreModel->getStoresByUserId($userId);
                $storeKeys = $userStores['stores'];

                $name = $this->input->get('name');
                $year = $this->input->get('year'); // Fetch year from query string
                $storeKey = $this->input->get('store_key');
                $result = $this->SpecialDayModel->getDonutCountBySpecialDayName($name, $year,$storeKey, $storeKeys);

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($result));

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
