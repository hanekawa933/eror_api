<?php
defined('BASEPATH') or exit('No direct script access allowed');



use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;


//Load Composer's autoloader
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(APPPATH . '../');
$dotenv->load();

class User extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('User_Model', 'user');
        $this->load->model('Exp_model', 'exp');
        $this->load->model('Role_Model', 'role');
        $this->load->library('phpmailer_lib');
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
        $token = $this->user_data;
        if($token->userdata->admin){
            $users = $this->user->get_all_user();
            if ($users) {
                foreach ($users as $key => $res) {
                    unset($users[$key]['password']);
                }
                $this->response([
                    'statusCode' => 200,
                    'data' => $users,
                    'message' => 'User successfully fetched...'
                ], 200);
            } else {
                $this->response([
                    'statusCode' => 200,
                    'data' => [],
                    'message' => 'Something went wrong...',
                ], 200);
            }
        }else{
            $this->response([
                'statusCode' => 403,
                'data' => [],
                'message' => 'Forbidden access!',
            ], 403);            
        }
        
    }

    public function index_post()
    {
        $this->_auth();
        $data = [
            'role_id' => $this->post('role_id'),
            'nama_lengkap' => $this->post('nama_lengkap'),
            'password' => password_hash($this->post('password'), PASSWORD_DEFAULT),
            'jenis_kelamin' => $this->post('jenis_kelamin'),
            'email' => $this->post('email'),
            'no_telp' => $this->post('no_telp'),
            'jabatan' => $this->post('jabatan'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $user = $this->user->login_user($data['email']);
        $password_verify = $this->post('password_verify');


        if (password_verify($password_verify, $data['password'])) {
            if (!$user) {
                $create = $this->user->create_user($data);
                if ($create == true) {
                    $this->response([
                        'statusCode' => 200,
                        'data' => $data,
                        'message' => 'User berhasil teregistrasi!'
                    ], 200);
                } else {
                    $this->response([
                        'statusCode' => 500,
                        'message' => 'Terjadi kesalahan!'
                    ], 500);
                }
            } else {
                $this->response([
                    'statusCode' => 409,
                    'message' => 'Email sudah terdaftar!',
                ], 409);
            }
        } else {
            $this->response([
                'statusCode' => 200,
                'message' => 'Password tidak sama!',
            ], 200);
        }
    }

    public function register_post()
    {
        $data = [
            'role_id' => 1,
            'nama_lengkap' => $this->post('nama_lengkap'),
            'password' => password_hash($this->post('password'), PASSWORD_DEFAULT),
            'email' => $this->post('email'),
            'current_exp' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $user = $this->user->get_by_email($data['email']);
        $password_verify = $this->post('password_verify');

        if (password_verify($password_verify, $data['password'])) {
            if (!$user) {
                $create = $this->user->create_user($data);
                if ($create == true) {

                    $this->response([
                        'statusCode' => 200,
                        'data' => $data,
                        'message' => 'User berhasil teregistrasi!'
                    ], 200);
                } else {
                    $this->response([
                        'statusCode' => 500,
                        'message' => 'Terjadi kesalahan!'
                    ], 500);
                }
            } else {
                $this->response([
                    'statusCode' => 409,
                    'message' => 'Email sudah terdaftar!',
                ], 409);
            }
        } else {
            $this->response([
                'statusCode' => 401,
                'message' => 'Password tidak sama!',
            ], 401);
        }
    }

    public function login_post()
    {
        $email = $this->post('email');
        $password = $this->post('password');

        $user = $this->user->login_user($email);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $secret_key = $_ENV['SECRET_KEY'];
                $iat = new DateTimeImmutable();
                $expire = $iat->modify('+36000 minutes')->getTimestamp();
                $server_name = 'http://localhost';
                unset($user['password']);
                $userdata = $user;
                $exp_user = $user['current_exp'];
                if ($exp_user < 101) {
                    $id1 = 1;
                } else if ($exp_user >= 101 && $exp_user <= 200) {
                    $id1 = 2;
                } else if ($exp_user >= 201 && $exp_user <= 400) {
                    $id1 = 3;
                } else if ($exp_user >= 401 && $exp_user <= 800) {
                    $id1 = 4;
                } else if ($exp_user >= 801 && $exp_user <= 1600) {
                    $id1 = 5;
                } else if ($exp_user >= 1601 && $exp_user <= 3200) {
                    $id1 = 6;
                } else if ($exp_user > 3200) {
                    $id1 = 7;
                }
                $level = $this->exp->get_by_id_exp($id1);
                $user['exp'] = $exp_user;
                $user['level'] = $level['nama'];
                $role = $this->role->get_all_role();
                foreach ($role as $res) {
                    if ($res['id'] == $user['role_id']) {
                        $user['role_name'] = $res['nama'];
                    }
                }

                $jwt = [
                    'iat' => $iat->getTimestamp(),
                    'iss' => $server_name,
                    'nbf' => $iat->getTimestamp(),
                    'exp' => $expire,
                    'userdata' => [
                        'id' => $userdata['id'],
                        'email' => $userdata['email'],
                        'admin' => false,
                    ]
                ];

                $encoded = JWT::encode($jwt, $secret_key, 'HS512');
                // $decoded = JWT::decode($encoded, $secret_key, array('HS512'));


                $this->response([
                    'statusCode' => 200,
                    'token' => array($encoded),
                    'data' => $user,
                    'message' => 'Berhasil login!'
                ], 200);
            } else {
                $this->response([
                    'statusCode' => 401,
                    'message' => 'Email atau password salah!'
                ], 401);
            }
        } else {
            $this->response([
                'statusCode' => 401,
                'message' => 'Email atau password salah!'
            ], 401);
        }
    }

    public function profile_get()
    {
        $this->_auth();
        $token = $this->user_data;
        $user = $this->user->get_user_login($token->userdata->id);
        $exp_user = $user['current_exp'];
        if ($exp_user < 101) {
            $id1 = 1;
        } else if ($exp_user >= 101 && $exp_user <= 200) {
            $id1 = 2;
        } else if ($exp_user >= 201 && $exp_user <= 400) {
            $id1 = 3;
        } else if ($exp_user >= 401 && $exp_user <= 800) {
            $id1 = 4;
        } else if ($exp_user >= 801 && $exp_user <= 1600) {
            $id1 = 5;
        } else if ($exp_user >= 1601 && $exp_user <= 3200) {
            $id1 = 6;
        } else if ($exp_user > 3200) {
            $id1 = 7;
        }
        $level = $this->exp->get_by_id_exp($id1);
        $user['exp'] = $exp_user;
        $user['level'] = $level['nama'];
        $role = $this->role->get_all_role();
        foreach ($role as $res) {
            if ($res['id'] == $user['role_id']) {
                $user['role_name'] = $res['nama'];
            }
        }
        // $data = [];
        // $data = [$user, 'Exp' => $exp_user, 'Pangkat' => $level['nama']];
        if ($user) {
            $this->response([
                'statusCode' => 200,
                'data' => $user,
                'message' => 'Successfully fetched user profile...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 404,
                'message' => 'User profile not found...'
            ], 404);
        }
    }

    public function update_profile_put()
    {
        $this->_auth();
        $token = $this->user_data;
        $data = [
            'nama_lengkap' => $this->put('nama_lengkap'),
            'jenis_kelamin' => $this->put('jenis_kelamin'),
            'email' => $this->put('email'),
            'no_telp' => $this->put('no_telp'),
            'jabatan' => $this->put('jabatan'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $user_update = $this->user->update_user($token->userdata->id, $data);
        if ($user_update) {
            $this->response([
                'statusCode' => 200,
                'message' => 'Successfully update user profile...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 200,
                'message' => 'Failed to update user profile...'
            ], 200);
        }
    }

    public function update_password_put()
    {
        $this->_auth();
        $token = $this->user_data;
        $data = [
            'password' => password_hash($this->put('password_baru'), PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $password_lama = $this->put('password_lama');
        $password_verify = $this->put('password_verify');

        $user = $this->user->get_by_id($token->userdata->id);

        if ($user) {
            if (password_verify($password_lama, $user['password'])) {
                if (password_verify($password_verify, $data['password'])) {
                    if (password_verify($password_verify, $user['password'])) {
                        $this->response([
                            'statusCode' => 400,
                            'message' => 'Password anda sama dengan sebelumnya!'
                        ], 400);
                    } else {
                        $user_update = $this->user->update_user($token->userdata->id, $data);
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
                } else {
                    $this->response([
                        'statusCode' => 200,
                        'message' => 'Password tidak sama!'
                    ], 200);
                }
            } else {
                $this->response([
                    'statusCode' => 400,
                    'message' => 'Password lama tidak sesuai!'
                ], 400);
            }
        } else {
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
            'nama_lengkap' => $this->put('nama_lengkap'),
            'password' => $this->put('password'),
            'password_verify' => $this->put('password_verify'),
            'jenis_kelamin' => $this->put('jenis_kelamin'),
            'email' => $this->put('email'),
            'no_telp' => $this->put('no_telp'),
            'jabatan' => $this->put('jabatan'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $id = $this->get('id');

        if ($data['password'] == null && $data['password_verify'] == null) {
            unset($data['password']);
            unset($data['password_verify']);
            $user_update = $this->user->update_user($id, $data);
            if ($user_update) {
                $this->response([
                    'statusCode' => 200,
                    'message' => 'Successfully update admin profile...'
                ], 200);
            } else {
                $this->response([
                    'statusCode' => 200,
                    'message' => 'Failed to update admin profile maybe because admin with that id doesn\'t exists...'
                ], 200);
            }
        } else {
            if ($data['password'] == $data['password_verify']) {
                unset($data['password_verify']);
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                $user_update = $this->user->update_user($id, $data);
                if ($user_update) {
                    $this->response([
                        'statusCode' => 200,
                        'message' => 'Successfully update user profile...'
                    ], 200);
                } else {
                    $this->response([
                        'statusCode' => 200,
                        'message' => 'Failed to update admin profile maybe because admin with that id doesn\'t exists...'
                    ], 200);
                }
            } else {
                $this->response([
                    'statusCode' => 200,
                    'message' => 'Password doesn\'t match...'
                ], 200);
            }
        }
    }

    public function forgot_post()
    {
        date_default_timezone_set('Asia/Jakarta');
        $email = $this->post('email');
        $data = [
            'email' => $email,
            'kode' => rand(1000, 9999),
            'expired_at' => date('H:i:s', time() + 600)
        ];
        $get_user = $this->user->login_user($email);
        if ($get_user) {
            $insert = $this->user->insert_forgot($data);
            if ($insert) {
                // Load PHPMailer library
                $this->load->library('phpmailer_lib');

                // PHPMailer object
                $mail = $this->phpmailer_lib->load();

                // SMTP configuration
                $mail->isSMTP();
                $mail->Host     = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'setnegx6@gmail.com';
                $mail->Password = '@Thisisreallyhardpassword999';
                $mail->SMTPSecure = 'tls';
                $mail->Port     = 587;

                $mail->setFrom('setnegx6@gmail.com', 'noReply');

                // Add a recipient
                $mail->addAddress($email);
                // Email subject
                $mail->Subject = 'Eror account password change request';

                // Set email format to HTML
                $mail->isHTML(true);
                // Email body content
                $mail->Body = '
                <div class="container" style="width: 800px; height: 100%;">
                    <div class="card" style="width: 100%; height: 50%;display: flex; justify-content: center;align-items: center; text-align: center; 
                        background:#e0ebe3;border-bottom:1px gray; padding-left:50px;">
                            <h3 style="font-size: 25px; text-align: center; padding:0 280px;">Eror Kode OTP</h3>
                    </div>    
                    <div class="card" style="width: 100%; height: 100%;; 
                        background:#fffff;border-bottom:1px gray; padding-left:200px;display:flex;justify-content:center;">
                        <div style="card"  style="width:50%;background:#e1ecf0;padding:15px;margin:20px 30px;border-radius:10px;border:1px white;">
                            <h2 class="card-text" >Hallo, '.$get_user['nama_lengkap'] . '</h2>
                                <h4 class="card-text">Kami menerima permintaan untuk merubah password anda, berikut kode OTP-mu :</h4>
                                <div class="row">
                                    <div class="col-12">
                                    <h3 style="font-size: 30px; text-align: center    ;padding: 0 175px; " >' . $data['kode'] . '</h3>
                                    </div>
                                    <h4 class="card-text">Jangan berikan kode OTP-mu kepada siapapun.</h4>
                                </div>
                        </div>
                    </div>
                    <div class="card-footer" style="width: 100%; height: 10%;display: flex; justify-content: center;align-items: center; text-align: center; 
                            background:#e0ebe3;border-bottom:1px gray; padding-left:50px;">
                            <h3 style="font-size: 10px; text-align: center; padding:0 300px;">© 2021 E-ror . All rights reserved.</h3>    
                    </div>
                </div>                    
            ';
                $send = $mail->send();
                $this->response([
                    'statusCode' => 200,
                    'message' => 'Email terkirim!'
                ], 200);
            }
        } else {
            $this->response([
                'statusCode' => 400,
                'message' => 'Email anda tidak terdaftar!'
            ], 400);
        }
    }

    public function auth_reset_post()
    {
        date_default_timezone_set('Asia/Jakarta');
        $data = [
            'email' => urldecode($this->get('email')),
            'kode' => $this->post('kode'),
        ];

        $auth = $this->user->get_check_kode($data);

        if ($auth) {
            if (time() <= strtotime($auth['expired_at'])) {
                $this->response([
                    'statusCode' => 200,
                    'data' => $data,
                    'message' => 'Autentikasi berhasil! Silahkan reset password.'
                ], 200);
            } else {
                $this->response([
                    'statusCode' => 400,
                    'data' => $auth,
                    'message' => 'Autentikasi gagal! kode OTP telah expired.'
                ], 400);
            }
        } else {
            $this->response([
                'statusCode' => 404,
                'data' => $data,
                'message' => 'Email atau kode OTP tidak terdaftar!'
            ], 404);
        }
    }


    public function reset_password_put()
    {
        $data = [
            'password' => password_hash($this->put('password'), PASSWORD_DEFAULT),
        ];

        $password_verify = $this->put('password_verify');

        $auth = [
            'email' => urldecode($this->get('email')),
            'kode' => $this->get('kode')
        ];
        $user = $this->user->get_check_kode($auth);

        if ($user) {
            $verified_user = $this->user->get_by_email($user['email']);
            if (password_verify($password_verify, $data['password'])) {
                $user_update = $this->user->update_user($verified_user['id'], $data);
                if ($user_update) {
                    $delete_otp = $this->user->delete_used_kode($auth);
                    $this->response([
                        'statusCode' => 200,
                        'message' => 'Berhasil update password!'
                    ], 200);
                } else {
                    $this->response([
                        'statusCode' => 400,
                        'log' => $user['id'],
                        'message' => 'Gagal untuk mengupdate password!'
                    ], 400);
                }
            } else {
                $this->response([
                    'statusCode' => 400,
                    'message' => 'Password tidak sama!'
                ], 400);
            }
        } else {
            $this->response([
                'statusCode' => 404,
                'message' => 'Email atau kode OTP tidak ditemukan'
            ], 404);
        }
    }

    public function update_pic_profile_post()
    {
        $this->_auth();
        $token = $this->user_data;

        $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '/eror_api/assets/img/foto_profile';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|mp3|mp4|mkv|avi|mov'; //type yang dapat diakses bisa anda sesuaikan
        $config['max_size'] = '2048000000'; //maksimum besar file 2M
        $config['max_width']  = '0'; //lebar maksimum 4096 px
        $config['max_height']  = '0'; //tinggi maksimu 2160 px
        $config['file_name'] = $_FILES['foto_profile']['name'];

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!empty($_FILES['foto_profile']['name'])) {
            if ($this->upload->do_upload('foto_profile')) {
                $pic = $this->upload->data();
                $picture = "/assets/img/foto_profile/" . $pic['file_name'];

                $data = [
                    'foto_profile' => $picture,
                    'id' => $token->userdata->id
                ];
                $update_foto = $this->user->update_foto($data);
                if ($update_foto) {
                    $this->response([
                        'statusCode' => 200,
                        'data' => $update_foto,
                        'gambar' => $picture,
                        'message' => 'Successfully create data laporan...'
                    ], 200);
                }
            } else {
                $eror = $this->upload->display_errors();
                $this->response([
                    'statusCode' => 500,
                    'message' => $eror
                ], 500);
            }
        } else {
            $eror = $this->upload->display_errors();
            $this->response([
                'statusCode' => 500,
                'message' => $eror
            ], 500);
        }
    }
    
    public function profile_post(){
        $this->_auth();
        $token = $this->user_data;
        $data = [
            'nama_lengkap' => $this->post('nama_lengkap'),
            'email' => $this->post('email'),
            'jabatan' => $this->post('jabatan'),
            'jenis_kelamin' => $this->post('jenis_kelamin'),
            'no_telp' => $this->post('no_telp'),
            'foto_profile' => '',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $user = $this->user->get_user_login($token->userdata->id);
        
        foreach($data as $key=>$res){
            $data[$key] = is_null($res) ? $user[$key] : $data[$key];
        }
        
        if(!empty($_FILES['foto_profile'])){
            $new_name = time().'_'.$_FILES["foto_profile"]['name'];
            $config['upload_path'] = './assets/img/foto_profile'; //path folder
            $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp'; //type yang dapat diakses bisa anda sesuaikan
            $config['max_size'] = 2048; //maksimum besar file 2M
            $config['file_name'] = $new_name;    
            $config['file_ext_tolower'] = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('foto_profile')) {
                $pic = $this->upload->data();
                $picture = "/assets/img/foto_profile/" . $pic['file_name'];
                $data['foto_profile'] = $picture;
                $user_update = $this->user->update_user($token->userdata->id, $data);
                if ($user_update) {
                    $this->response([
                        'statusCode' => 200,
                        'message' => 'Successfully update profile...'
                    ], 200);
                } else {
                    $this->response([
                        'statusCode' => 200,
                        'message' => 'Gagal update profil!'
                    ], 200);
                }
            }else{
                $error = array('error' => $this->upload->display_errors());
                $this->response($error, 500);
            }   
        }else{
            unset($data['foto_profile']);
            $user_update = $this->user->update_user($token->userdata->id, $data);
            if ($user_update) {
                $this->response([
                    'statusCode' => 200,
                    'message' => 'Successfully update profil!'
                ], 200);
            } else {
                $this->response([
                    'statusCode' => 200,
                    'message' => 'Gagal update profil!'
                ], 200);
            }            
        }
    }

    public function expired_kode_get()
    {
        date_default_timezone_set('Asia/Jakarta');
        $data = [
            'email' => urldecode($this->get('email')),
            'kode' => $this->get('kode'),
        ];

        $auth = $this->user->get_check_kode($data);
        if ($auth) {
            if (time() <= strtotime($auth['expired_at'])) {
                $this->response([
                    'statusCode' => 200,
                    'data' => $data,
                    'message' => 'Autentikasi berhasil! Silahkan reset password.'
                ], 200);
            } else {
                $this->response([
                    'statusCode' => 400,
                    'message' => 'Autentikasi gagal! kode OTP telah expired.'
                ], 400);
            }
        } else {
            $this->response([
                'statusCode' => 404,
                'data' => $data,
                'message' => 'Email atau kode OTP tidak terdaftar!'
            ], 404);
        }
    }

    public function delete_delete()
    {
        $this->_auth();

        $id = $this->get('id');
        $user = $this->user->delete_cascade_user($id);
        if ($user) {
            $this->response([
                'statusCode' => 200,
                'message' => 'Successfully delete user profile...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 200,
                'message' => 'Failed to delete maybe because user with that id doesn\'t exists...'
            ], 200);
        }
    }
}
