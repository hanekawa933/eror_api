<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(APPPATH . '../');
$dotenv->load();

class Status extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Status_Model', 'status');
    }

    private $user_data;
    private function _auth()
    {
        //JWT Auth middleware
        $headers = $this->input->get_request_header('x-auth-token');
        $secret_key = $_ENV['SECRET_KEY']; //secret key for encode and decode
        $token = "token";
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $token = $matches[1];
            }
        } else {
            $this->response([
                'statusCode' => 400,
                'message' => 'Request failed due to empty token...'
            ], 400);
        }
        try {
            $decoded = JWT::decode($token, $secret_key, array('HS512'));
            $this->user_data = $decoded;
        } catch (Exception $e) {
            $this->response([
                'statusCode' => 401,
                'message' => 'Request failed due to unauthorized...'
            ], 401);
        }
    }

    public function index_get()
    {
        $this->_auth();
        $status = $this->status->get_all_status();

        if ($status) {
            $this->response([
                'statusCode' => 200,
                'data' => $status,
                'message' => 'Status successfully fetched...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Something went wrong...',
            ], 500);
        }
    }
}
