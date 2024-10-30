<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class DonutCountController extends CI_Controller {

    protected $key = 'eemTSXWd99S12oEQLGSnI6qe8yBz5gTlhauce82WV8Hb5x5DyfgNro1b9G2/ZkIE7k2EOO5EC/9RtBeVO9cHzg==';


    public function __construct()
    {
        parent::__construct();
        $this->load->model('Donutcount_model');
        $this->load->model('UserStoreModel');

    }

    private function response($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function showDonutCount()
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

                $storeKey = $this->input->get('store_key');
                $timeRange = $this->input->get('time_range');
                $month = $this->input->get('month'); // Format: MM
                $date = $this->input->get('date'); // Format: YYYY-MM-DD
                $year = $this->input->get('year'); // Format: YYYY
                $type = $this->input->get('type');
                $days = $this->input->get('days');
                $weekEnding = $this->input->get('week_ending');
                $numYears = $this->input->get('years');
                $numMonths = $this->input->get('months');
                $startDate = $this->input->get('start_date'); // New parameter for start date
                $endDate = $this->input->get('end_date'); // New parameter for end date
                $yearsrange = $this->input->get('yearsrange'); // Fetch year from query string

                // Retrieve the store keys associated with the user
                $userStores = $this->UserStoreModel->getStoresByUserId($userId);
                $storeKeys = $userStores['stores'];

                $donutCounts = $this->Donutcount_model->getDonutCountsGroupedByStoreKey($storeKey, $type, $days, $timeRange, $month, $date, $year, $weekEnding, $numYears, $numMonths, $startDate, $endDate, $yearsrange, $storeKeys);

                // Set content type to JSON and return the data
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($donutCounts));

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


    public function bestSale()
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

                $days = $this->input->get('days');
                $months = $this->input->get('months');
                $years = $this->input->get('years');
                $type = $this->input->get('type');

                $userStores = $this->UserStoreModel->getStoresByUserId($userId);
                $storeKeys = $userStores['stores'];

                $donutCounts = $this->Donutcount_model->getBestSaleByType($days, $months, $years, $type, $storeKeys);

                // Set content type to JSON and return the data
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($donutCounts));

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

    public function salesReport()
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

                $storeKey = $this->input->get('store_key');
                $donutType = $this->input->get('donut_type');
                $timeRange = $this->input->get('time_range');
                $total = $this->input->get('total');

                $userStores = $this->UserStoreModel->getStoresByUserId($userId);
                $storeKeys = $userStores['stores'];

                $salesReportData = $this->Donutcount_model->salesReport($storeKey, $donutType, $timeRange, $total, $storeKeys);

                // Set content type to JSON and return the data
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($salesReportData));

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