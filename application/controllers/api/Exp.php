<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(APPPATH . '../');
$dotenv->load();

class Exp extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Exp_model', 'exp');
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
        $exp = $this->exp->get_all_exp();

        if ($exp) {
            $this->response([
                'statusCode' => 200,
                'data' => $exp,
                'message' => 'exp successfully fetched...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Something went wrong...',
            ], 500);
        }
    }

    public function item_get()
    {
        $this->_auth();
        $id = $this->get('id');
        $exp = $this->exp->get_by_id_exp($id);

        if ($exp > 0) {
            $this->response([
                'statusCode' => 200,
                'data' => $exp,
                'message' => 'Exp successfully fetched...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Something went wrong...',
            ], 500);
        }
    }

    public function index_post()
    {
        $this->_auth();
        $data = [
            'nama' => $this->post('nama'),
            'exp' => $this->post('exp')
        ];

        $exp = $this->exp->insert_exp($data);

        if ($exp) {
            $this->response([
                'statusCode' => 200,
                'data' => $exp,
                'message' => 'Successfully create data exp...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Something went wrong...'
            ], 200);
        }
    }
}
