<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;
$dotenv = Dotenv\Dotenv::createImmutable(APPPATH . '../');
$dotenv->load();

class Auth extends RestController {
	function __construct()
	{
		parent::__construct();
		$this->load->model('SuperAdmin_Model');
		$this->load->model('Bukti_Laporan_Model', 'bukti');

	}

    private $user_data;
    public function auth()
    {
        //JWT Auth middleware
        $headers = $this->input->get_request_header('x-auth-token');
        $secret_key = $_ENV['SECRET_KEY']; //secret key for encode and decode
        $token="token";
        if (!empty($headers)) {
        	if (preg_match('/Bearer\s(\S+)/', $headers , $matches)) {
                $token = $matches[1];
        	}
    	}else{
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


public function login_post(){
		$username = $this->post('username');
		$password = $this->post('password');

		$get_user = $this->SuperAdmin_Model->login_superadmin($username);

		if($get_user > 0){
			if(password_verify($password, $get_user['password'])){
				$secret_key = $_ENV['SECRET_KEY'];
				$iat = new DateTimeImmutable();
				$expire = $iat->modify('+60 minutes')->getTimestamp();
				$server_name = 'http://localhost';
				$userdata = $get_user;
		
				$jwt = [
					'iat' => $iat->getTimestamp(),
					'iss' => $server_name,
					'nbf' => $iat->getTimestamp(),
					'exp' => $expire,
					'userdata' => [
                        'id' => $userdata['id'],
                        'username' => $userdata['username']
                    ]
				];
		
				$encoded = JWT::encode($jwt, $secret_key, 'HS512');
				$decoded = JWT::decode($encoded, $secret_key, array('HS512'));
		
		
				$this->response([
					'statusCode' => 200,
					'token' => $decoded,
					'message' => 'Successfully logged in...'
				], 200);
			}else{
				$this->response([
					'statusCode' => 401,
					'message' => 'Invalid username or password...'
				], 401);
			}
		}else{
			$this->response([
				'statusCode' => 401,
				'message' => 'Invalid username or password...'
			], 401);
		}
    }

    public function coba_get(){
    	$id = $this->get('id');
    	$get = $this->bukti->get_bukti_by_laporan_id($id);
    	$this->response([
				'statusCode' => 200,
				'data' => $get,
				'message' => 'Invalid username or password...'
			], 200);
    }
}