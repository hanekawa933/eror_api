<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(APPPATH . '../');
$dotenv->load();

class Kategori extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Kategori_Model', 'kategori');
        $this->load->model('Laporan_Model', 'laporan');
        $this->load->library('upload');
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
        $kategori = $this->kategori->get_all_kategori();

        if ($kategori) {
            $this->response([
                'statusCode' => 200,
                'data' => $kategori,
                'message' => 'Kategori successfully fetched...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Something went wrong...',
            ], 500);
        }
    }

    public function notif_get()
    {
        // $this->_auth();
        $query = $this->get('query');
        $kategori = $this->kategori->get_all_kategori();

        if($query == "admin" || $query == "teknisi" || !$query){
            foreach($kategori as $key=>$value){
                $laporan = $this->laporan->get_laporan_count_by_kategori_and_query($query, $value['id'], $this->get('id'));
                $kategori[$key]['notifikasi'] = $laporan;
            }
            if ($kategori) {
                $this->response([
                    'statusCode' => 200,
                    'data' => $kategori,
                    'message' => 'User successfully fetched...'
                ], 200);
            } else {
                $this->response([
                    'statusCode' => 500,
                    'message' => 'Something went wrong...',
                ], 500);
            }
        }else{
             $this->response([
                'statusCode' => 404,
                'data' => [],
                'message' => 'No query were to be found...',
            ], 404);
        }

        
    }

    public function item_get()
    {
        $this->_auth();
        $id = $this->get('id');
        $kategori = $this->kategori->get_by_id_kategori($id);

        if ($kategori > 0) {
            $this->response([
                'statusCode' => 200,
                'data' => $kategori,
                'message' => 'User successfully fetched...'
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
        $new_name = time().'_'.$_FILES["icon"]['name'];
        $config['upload_path'] = './assets/kategori/'; //path folder
        $config['allowed_types'] = 'svg|png'; //type yang dapat diakses bisa anda sesuaikan
        $config['max_size'] = 2048; //maksimum besar file 2M
        $config['file_name'] = $new_name;    
        $config['file_ext_tolower'] = TRUE;   


        $this->load->library('upload', $config);
        $this->upload->initialize($config);
       
            if ($this->upload->do_upload('icon')) {
                $nama = $this->post('nama');
                $kd_kategori = $this->post('kd_kategori');
                // $icon = $this->post('icon');
                $pic = $this->upload->data();
                $icon = "/assets/kategori/" . $pic['file_name'];
                $data = [
                    'nama' => $nama,
                    'kd_kategori' => $kd_kategori,
                    'icon' => $icon
                ];

                $kategori = $this->kategori->insert_kategori($data);

                if ($kategori) {
                    $this->response([
                        'statusCode' => 200,
                        'data' => $kategori,
                        'message' => 'Successfully create data kategori...'
                    ], 200);
                } else {
                    $this->response([
                        'statusCode' => 500,
                        'message' => 'Something went wrong...'
                    ], 500);
                }
        }else{
            $error = array('error' => $this->upload->display_errors());
            $this->response($error, 500);
        }
    }

    public function update_post()
    {
        $this->_auth();
        $data = [
            'nama' => $this->post('nama'),
            'kd_kategori' => $this->post('kd_kategori'),
            'icon' => $this->post('icon')
        ];

        $id = $this->get('id');

        if(is_null($data['icon'])){
            $new_name = time().'_'.$_FILES["icon"]['name'];
            $config['upload_path'] = './assets/kategori/'; //path folder
            $config['allowed_types'] = 'svg|png'; //type yang dapat diakses bisa anda sesuaikan
            $config['max_size'] = 2048; //maksimum besar file 2M
            $config['file_name'] = $new_name;    
            $config['file_ext_tolower'] = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('icon')) {
                $pic = $this->upload->data();
                $icon = "/assets/kategori/" . $pic['file_name'];
                $data['icon'] = $icon;
                $kategori_update = $this->kategori->update_kategori($id, $data);
                if ($kategori_update) {
                    $this->response([
                        'statusCode' => 200,
                        'message' => 'Successfully update kategori...'
                    ], 200);
                } else {
                    $this->response([
                        'statusCode' => 200,
                        'message' => 'Failed to update kategori maybe because kategori with that id doesn\'t exists...'
                    ], 200);
                }
            }else{
                $error = array('error' => $this->upload->display_errors());
                $this->response($error, 500);
            }   
        }else{
            unset($data['icon']);
            $kategori_update = $this->kategori->update_kategori($id, $data);
            if ($kategori_update) {
                $this->response([
                    'statusCode' => 200,
                    'message' => 'Successfully update kategori...'
                ], 200);
            } else {
                $this->response([
                    'statusCode' => 200,
                    'message' => 'Failed to update kategori maybe because kategori with that id doesn\'t exists...'
                ], 200);
            }            
        }
    }

    public function delete_delete()
    {
        $this->_auth();

        $id = $this->get('id');
        $kategori = $this->kategori->delete_kategori($id);
        if ($kategori) {
            $this->response([
                'statusCode' => 200,
                'message' => 'Successfully delete kategori...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 200,
                'message' => 'Failed to delete maybe because kategori with that id doesn\'t exists...'
            ], 200);
        }
    }
}
