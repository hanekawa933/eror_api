<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(APPPATH . '../');
$dotenv->load();

class SuperAdmin extends RestController
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('SuperAdmin_Model');
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
			$token = $decoded;
			if($token->userdata->admin){
			    $this->user_data = $decoded;
			}else{
    			    $this->response([
    				'statusCode' => 403,
    				'message' => 'Request failed due to forbidden access...'
    			], 401);
			}
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
		$users = $this->SuperAdmin_Model->get_all_superadmin();

		if ($users) {
			$this->response([
				'statusCode' => 200,
				'data' => $users,
				'message' => 'Superadmin successfully fetched...'
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
			'username' => $this->post('username'),
			'password' => password_hash($this->post('password'), PASSWORD_DEFAULT),
			'nama_lengkap' => $this->post('nama_lengkap'),
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		];

		$user = $this->SuperAdmin_Model->login_superadmin($data['username']);
		$password_verify = $this->post('password_verify');


		if (password_verify($password_verify, $data['password'])) {
			if (!$user) {
				$create = $this->SuperAdmin_Model->create_superadmin($data);
				if ($create == true) {
					$this->response([
						'statusCode' => 200,
						'data' => $data,
						'message' => 'Superadmin successfully created...'
					], 200);
				} else {
					$this->response([
						'statusCode' => 500,
						'message' => 'Something went wrong...'
					], 500);
				}
			} else {
				$this->response([
					'statusCode' => 409,
					'message' => 'Username already exists...',
				], 409);
			}
		} else {
			$this->response([
				'statusCode' => 200,
				'message' => 'Password doesn\'t match...',
			], 200);
		}
	}

	public function login_post()
	{
		$username = $this->post('username');
		$password = $this->post('password');

		$get_user = $this->SuperAdmin_Model->login_superadmin($username);

		if ($get_user > 0) {
			if (password_verify($password, $get_user['password'])) {
				$secret_key = $_ENV['SECRET_KEY'];
				$iat = new DateTimeImmutable();
				$expire = $iat->modify('+99999 minutes')->getTimestamp();
				$server_name = 'http://localhost';
				$userdata = $get_user;

				$jwt = [
					'iat' => $iat->getTimestamp(),
					'iss' => $server_name,
					'nbf' => $iat->getTimestamp(),
					'exp' => $expire,
					'userdata' => [
						'id' => $userdata['id'],
						'username' => $userdata['username'],
						'admin' => true
					]
				];

				$encoded = JWT::encode($jwt, $secret_key, 'HS512');
				// $decoded = JWT::decode($encoded, $secret_key, array('HS512'));


				$this->response([
					'statusCode' => 200,
					'token' => array($encoded),
					'message' => 'Successfully logged in...'
				], 200);
			} else {
				$this->response([
					'statusCode' => 401,
					'message' => 'Invalid username or password...'
				], 401);
			}
		} else {
			$this->response([
				'statusCode' => 401,
				'message' => 'Invalid username or password...'
			], 401);
		}
	}

	public function profile_get()
	{
		$this->_auth();
		$token = $this->user_data;
		$user = $this->SuperAdmin_Model->get_user_login_superadmin($token->userdata->id);

		if ($user) {
			$this->response([
				'statusCode' => 200,
				'data' => $user,
				'message' => 'Successfully fetched superadmin profile...'
			], 200);
		} else {
			$this->response([
				'statusCode' => 404,
				'message' => 'Superadmin profile not found...'
			], 404);
		}
	}

	public function update_profile_put()
	{
		$this->_auth();
		$token = $this->user_data;
		$data = [
			'username' => $this->put('username'),
			'nama_lengkap' => $this->put('nama_lengkap'),
			'updated_at' => date('Y-m-d H:i:s'),
		];

		$user_update = $this->SuperAdmin_Model->update_superadmin($token->userdata->id, $data);
		if ($user_update) {
				$this->response([
					'statusCode' => 200,
					'message' => 'Successfully update superadmin profile...'
				], 200);
		}else{
				$this->response([
					'statusCode' => 200,
					'message' => 'Failed to update superadmin profile...'
				], 200);
		}	
	}

	public function update_password_put(){
        $this->_auth();
        $token = $this->user_data;
        $data = [
            'password' => password_hash($this->put('password_baru'), PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ];
            
        $password_lama = $this->put('password_lama');
        $password_verify = $this->put('password_verify');

        $user = $this->SuperAdmin_Model->get_user_login_superadmin($token->userdata->id);

        if($user){
            if(password_verify($password_lama, $user['password'])){
                if (password_verify($password_verify, $data['password'])){            
                    if(password_verify($password_verify, $user['password'])){
                        $this->response([
                            'statusCode' => 400,
                            'message' => 'Password anda sama dengan sebelumnya!'
                        ], 400);                
                    }else{
                        $user_update = $this->SuperAdmin_Model->update_superadmin($token->userdata->id, $data);
                        if ($user_update) {
                            $this->response([
                                'statusCode' => 200,
                                'message' => 'Berhasil update password!'
                            ], 200);
                        } else {
                            $this->response([
                                'statusCode' => 400,
                                'message' => 'Gagal untuk mengupdate password!'
                            ], 400);
                        }        
                    }
                }else{
                    $this->response([
                        'statusCode' => 200,
                        'message' => 'Password tidak sama!'
                    ], 200);
                }
            }else{
                $this->response([
                        'statusCode' => 400,
                        'message' => 'Password lama tidak sesuai!'
                    ], 400);
            }                
        }else{
            $this->response([
                'statusCode' => 404,
                'message' => 'User tidak ditemukan!'
            ], 404);
        }
    }

	public function update_put()
	{
		$this->_auth();
		$data = [
			'username' => $this->put('username'),
			'password' => password_hash($this->put('password'), PASSWORD_DEFAULT),
			'nama_lengkap' => $this->put('nama_lengkap'),
			'updated_at' => date('Y-m-d H:i:s'),
		];

		$id = $this->get('id');

		$password_verify = $this->put('password_verify');
		$user_update = $this->SuperAdmin_Model->update_superadmin($id, $data);

		if (password_verify($password_verify, $data['password'])) {
			if ($user_update) {
				$this->response([
					'statusCode' => 200,
					'message' => 'Successfully update superadmin profile...'
				], 200);
			} else {
				$this->response([
					'statusCode' => 200,
					'message' => 'Failed to update superadmin profile maybe because superadmin with that id doesn\'t exists...'
				], 200);
			}
		} else {
			$this->response([
				'statusCode' => 200,
				'message' => 'Password doesn\'t match...'
			], 200);
		}
	}

	public function delete_delete()
	{
		$this->_auth();

		$id = $this->get('id');
		$user = $this->SuperAdmin_Model->delete_superadmin($id);
		if ($user) {
			$this->response([
				'statusCode' => 200,
				'message' => 'Successfully delete superadmin profile...'
			], 200);
		} else {
			$this->response([
				'statusCode' => 200,
				'message' => 'Failed to delete maybe because user superadmin with that id doesn\'t exists...'
			], 200);
		}
	}
}
