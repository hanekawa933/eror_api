<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(APPPATH . '../');
$dotenv->load();

class Faq extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Faq_Model', 'faq');
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
        $faq = $this->faq->get_all_faq();

        if ($faq) {
            $this->response([
                'statusCode' => 200,
                'data' => $faq,
                'message' => 'faq successfully fetched...'
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
        $faq = $this->faq->get_by_id_faq($id);

        if ($faq > 0) {
            $this->response([
                'statusCode' => 200,
                'data' => $faq,
                'message' => 'Faq successfully fetched...'
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
            'pertanyaan' => $this->post('pertanyaan'),
            'jawaban' => $this->post('jawaban')
        ];

        $faq = $this->faq->insert_faq($data);

        if ($faq) {
            $this->response([
                'statusCode' => 200,
                'data' => $faq,
                'message' => 'Successfully create data Faq...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Something went wrong...'
            ], 200);
        }
    }

    public function update_put()
    {
        $this->_auth();
        $data = [
            'pertanyaan' => $this->put('pertanyaan'),
            'jawaban' => $this->put('jawaban'),
        ];

        $id = $this->get('id');

        $faq_update = $this->faq->update_faq($id, $data);
        if ($faq_update) {
            $this->response([
                'statusCode' => 200,
                'message' => 'Successfully update faq...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 200,
                'message' => 'Failed to update faq maybe because faq with that id doesn\'t exists...'
            ], 200);
        }
    }

    public function delete_delete()
    {
        $this->_auth();

        $id = $this->get('id');
        $faq = $this->faq->delete_faq($id);
        if ($faq) {
            $this->response([
                'statusCode' => 200,
                'message' => 'Successfully delete faq...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 200,
                'message' => 'Failed to delete maybe because faq with that id doesn\'t exists...'
            ], 200);
        }
    }
}
