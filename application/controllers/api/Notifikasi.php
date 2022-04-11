<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(APPPATH . '../');
$dotenv->load();

class Notifikasi extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Notifikasi_Model', 'notifikasi');
        $this->load->model('Bukti_Laporan_Model', 'bukti');
        $this->load->model('Laporan_Model', 'laporan');
        $this->load->model('User_Model', 'user');
        date_default_timezone_set('Asia/Jakarta');
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
        $Notifikasi = $this->notifikasi->get_all_notifikasi();

        if ($Notifikasi) {
            $this->response([
                'statusCode' => 200,
                'data' => $Notifikasi,
                'message' => 'Notifikasi successfully fetched...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan.',
            ], 500);
        }
    }

    public function item_get()
    {
        $this->_auth();
        $id = $this->get('id');
        $notifikasi = $this->notifikasi->get_by_id_notifikasi($id);

        if ($notifikasi) {
            $this->response([
                'statusCode' => 200,
                'data' => $notifikasi,
                'message' => 'Notifikasi successfully fetched...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan.',
            ], 500);
        }
    }

    public function user_get()
    {
        $this->_auth();
        $token = $this->user_data;
        $notifikasi = $this->notifikasi->get_notifikasi_by_user_login($token->userdata->id);
        $this->response([
            'statusCode' => 200,
            'data' => $notifikasi,
            'message' => 'Notifikasi successfully fetched...'
        ], 200);
    }

    public function admin_validasi_post()
    {
        $this->_auth();
        $id = $this->get('id');
        $laporan = $this->laporan->get_laporan_by_id($id);
        $ket_admin = $this->post('ket_admin');
        $notif = [
            'laporan_id' => $id,
            'pesan' => 'Laporan ' . $laporan['kode_laporan'] . ' telah di validasi oleh admin!',
            'keterangan' => 'Laporan anda mengenai "' . $laporan['jenis_kerusakan'] . '" ' . $ket_admin,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $notifikasi = $this->notifikasi->insert_notif($notif);
        if ($notifikasi) {
            $data = [
                'keterangan_admin' => $ket_admin,
                'status_id' => 3
            ];
            $update = $this->laporan->update_laporan($id, $data);
            $this->response([
                'statusCode' => 200,
                'data' => $laporan,
                'message' => 'Notifikasi successfully insert'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan!',
            ], 500);
        }
    }

    public function admin_duplikasi_post()
    {
        $this->_auth();
        $id = $this->get('id');
        $laporan = $this->laporan->get_laporan_by_id($id);
        $ket_admin = $this->post('ket_admin');
        $notif = [
            'laporan_id' => $id,
            'pesan' => 'Laporan ' . $laporan['kode_laporan'] . ' telah di validasi oleh admin sebagai duplikat!',
            'keterangan' => 'Laporan anda mengenai "' . $laporan['jenis_kerusakan'] . '" ' . $ket_admin,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $notifikasi = $this->notifikasi->insert_notif($notif);
        if ($notifikasi) {
            $data = [
                'keterangan_admin' => $ket_admin,
                'status_id' => 2
            ];
            $update = $this->laporan->update_laporan($id, $data);
            
            $dapet_exp=20;
            $update_user = $this->user->add_exp($dapet_exp, $laporan['uId']);
            $this->response([
                'statusCode' => 200,
                'data' => $notifikasi,
                'message' => 'Notifikasi successfully insert'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan.',
            ], 500);
        }
    }
    
    public function admin_duplikasi_no_exp_post()
    {
        $this->_auth();
        $id = $this->get('id');
        $laporan = $this->laporan->get_laporan_by_id($id);
        $ket_admin = $this->post('ket_admin');
        $notif = [
            'laporan_id' => $id,
            'pesan' => 'Laporan ' . $laporan['kode_laporan'] . ' telah di validasi oleh admin sebagai duplikat!',
            'keterangan' => 'Laporan anda mengenai "' . $laporan['jenis_kerusakan'] . '" ' . $ket_admin,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $notifikasi = $this->notifikasi->insert_notif($notif);
        if ($notifikasi) {
            $data = [
                'keterangan_admin' => $ket_admin,
                'status_id' => 2
            ];
            $update = $this->laporan->update_laporan($id, $data);
            $this->response([
                'statusCode' => 200,
                'data' => $notifikasi,
                'message' => 'Notifikasi successfully insert'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan.',
            ], 500);
        }
    }

    public function teknisi_cek_post()
    {
        $this->_auth();
        $id = $this->get('id');
        $laporan = $this->laporan->get_laporan_by_id($id);
        $notif = [
            'laporan_id' => $id,
            'pesan' => 'Laporan ' . $laporan['kode_laporan'] . ' sedang dalam proses pengecekan oleh teknisi!',
            'keterangan' => 'Laporan anda mengenai "' . $laporan['jenis_kerusakan'] . '" sedang dalam proses pengecekan oleh teknisi!',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $notifikasi = $this->notifikasi->insert_notif($notif);
        if ($notifikasi) {
            $data = [
                'status_id' => 4
            ];
            $update = $this->laporan->update_laporan($id, $data);
            $this->response([
                'statusCode' => 200,
                'data' => $notifikasi,
                'message' => 'Notifikasi successfully insert'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan.',
            ], 500);
        }
    }
    public function teknisi_perbaikan_post()
    {
        $this->_auth();
        $id = $this->get('id');
        $laporan = $this->laporan->get_laporan_by_id($id);
        $ket_teknisi = $this->post('ket_teknisi');
        if($ket_teknisi == null){
            $keterangan = "sedang dalam proses perbaikan";
        }else{
            $keterangan = $ket_teknisi;
        }
        $notif = [
            'laporan_id' => $id,
            'pesan' => 'Laporan ' . $laporan['kode_laporan'] . ' sedang dalam proses perbaikan oleh teknisi!',
            'keterangan' => 'Laporan anda mengenai "' . $laporan['jenis_kerusakan'] . '" ' . $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $notifikasi = $this->notifikasi->insert_notif($notif);
        if ($notifikasi) {
            $data = [
                'keterangan_teknisi' => $ket_teknisi,
                'status_id' => 5
            ];
            $update = $this->laporan->update_laporan($id, $data);
            $this->response([
                'statusCode' => 200,
                'data' => $notifikasi,
                'message' => 'Notifikasi successfully insert'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan.',
            ], 500);
        }
    }

    public function finish_post()
    {
        $this->_auth();
        $token = $this->user_data;
        $id = $this->get('id');
        $laporan = $this->laporan->get_laporan_by_id($id);
        $ket_teknisi = $this->post('ket_teknisi');
        $notif = [
            'laporan_id' => $id,
            'pesan' => 'Laporan ' . $laporan['kode_laporan'] . ' selesai diperbaiki!',
            'keterangan' => 'Laporan anda mengenai "' . $laporan['jenis_kerusakan'] . '" ' . $ket_teknisi,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $pic = array();
        $total = count($_FILES['gambar']['name']);
        $l = $_FILES;
        $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '/eror_api/assets/img/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp'; //type yang dapat diakses bisa anda sesuaikan
        $config['max_size'] = '2048'; //maksimum besar file 2M
        $config['max_width']  = '0'; //lebar maksimum 4096 px
        $config['max_height']  = '0'; //tinggi maksimu 2160 px
        // $config['file_name'] = $_FILES['gambar']['name']; //nama yang terupload nantinya$config['max_width']  = '4096'; //lebar maksimum 4096 px
        // $config['max_width']  = '4096'; //lebar maksimum 4096 px
        // $config['max_height']  = '2160'; //tinggi maksimu 2160 px
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        for ($i = 0; $i < $total; $i++) {
            $nmfile = "file_" . time(); //nama file saya beri nama langsung dan diikuti fungsi time

            $_FILES['gambar']['name'] = $l['gambar']['name'][$i];
            $_FILES['gambar']['type'] = $l['gambar']['type'][$i];
            $_FILES['gambar']['tmp_name'] = $l['gambar']['tmp_name'][$i];
            $_FILES['gambar']['size'] = $l['gambar']['size'][$i];

            //print_r($_FILES['gambar']);
            // print_r($_FILES['gambar']['name']);

            if (!empty($_FILES['gambar']['name'])) {
                if ($this->upload->do_upload('gambar')) {
                    $pic[] = $this->upload->data();
                    $gambar = $pic[$i]['file_name'];
                    $gambarUpload = [
                        'laporan_id' => $id,
                        'user_id' => $token->userdata->id,
                        'gambar' => '/assets/img/' . $gambar
                    ];
                    $gambar1 = $this->bukti->insert_bukti_teknisi($gambarUpload);
                    // $newest_gambar_id = $this->bukti->get_newest_bukti();
                    // $data['bukti_laporan_id'] = $newest_gambar_id;

                } else {
                    $eror = $this->upload->display_errors();
                    $this->response([
                        'statusCode' => 500,
                        'message' => strip_tags($eror . " atau file terlalu besar.")
                    ], 500);
                }
            } else {
                $eror = $this->upload->display_errors();
                $this->response([
                    'statusCode' => 500,
                    'message' => strip_tags($eror . " atau file terlalu besar.")
                ], 500);
            }
        }
        $notifikasi = $this->notifikasi->insert_notif($notif);
        if ($notifikasi && $gambar1) {
            $data = [
                'keterangan_teknisi' => $ket_teknisi,
                'status_id' => 6
            ];
            $update = $this->laporan->update_laporan($id, $data);
            if($update){
                
            $dapet_exp=40;
            $update_user = $this->user->add_exp($dapet_exp, $laporan['uId']);
            $this->response([
                'statusCode' => 200,
                'data' => $notifikasi,
                'gambar' => $gambarUpload,
                'message' => 'Notifikasi successfully insert'
            ], 200);    
                
            }
            
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan.',
            ], 500);
        }
    }

    public function finish_admin_post()
    {
        $this->_auth();
        $id = $this->get('id');
        $laporan = $this->laporan->get_laporan_by_id($id);
        $ket_admin = $this->post('ket_admin');
        $notif = [
            'laporan_id' => $id,
            'pesan' => 'Laporan ' . $laporan['kode_laporan'] . ' selesai diperbaiki!',
            'keterangan' => 'Laporan anda mengenai "' . $laporan['jenis_kerusakan'] . '" ' . $ket_admin,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $notifikasi = $this->notifikasi->insert_notif($notif);
        if ($notifikasi) {
            $data = [
                'keterangan_admin' => $ket_admin,
                'status_id' => 7
            ];
            $update = $this->laporan->update_laporan($id, $data);
            $this->response([
                'statusCode' => 200,
                'data' => $notifikasi,
                'message' => 'Notifikasi successfully insert'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan.',
            ], 500);
        }
    }

    public function tolak_post()
    {
        $this->_auth();
        $token = $this->user_data;
        $id = $this->get('id');
        $laporan = $this->laporan->get_laporan_by_id($id);
        $ket_teknisi = $this->post('ket_teknisi');
        if($ket_teknisi == null){
            $keterangan = "di tolak oleh teknisi";
        }else{
            $keterangan = $ket_teknisi;
        }
        $notif = [
            'laporan_id' => $id,
            'pesan' => 'Laporan ' . $laporan['kode_laporan'] . ' di tolak oleh teknisi!',
            'keterangan' => 'Laporan anda mengenai "' . $laporan['jenis_kerusakan'] . '" ' . $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $pic = array();
        $total = count($_FILES['gambar']['name']);
        $l = $_FILES;
        $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '/eror_api/assets/img/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp'; //type yang dapat diakses bisa anda sesuaikan
        $config['max_size'] = '2048000000'; //maksimum besar file 2M
        $config['max_width']  = '0'; //lebar maksimum 4096 px
        $config['max_height']  = '0'; //tinggi maksimu 2160 px
        // $config['file_name'] = $_FILES['gambar']['name']; //nama yang terupload nantinya$config['max_width']  = '4096'; //lebar maksimum 4096 px
        // $config['max_width']  = '4096'; //lebar maksimum 4096 px
        // $config['max_height']  = '2160'; //tinggi maksimu 2160 px
        for ($i = 0; $i < $total; $i++) {
            $nmfile = "file_" . time(); //nama file saya beri nama langsung dan diikuti fungsi time

            $_FILES['gambar']['name'] = $l['gambar']['name'][$i];
            $_FILES['gambar']['type'] = $l['gambar']['type'][$i];
            $_FILES['gambar']['tmp_name'] = $l['gambar']['tmp_name'][$i];
            $_FILES['gambar']['size'] = $l['gambar']['size'][$i];

            //print_r($_FILES['gambar']);


            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            // print_r($_FILES['gambar']['name']);

            if (!empty($_FILES['gambar']['name'])) {
                if ($this->upload->do_upload('gambar')) {
                    $pic[] = $this->upload->data();
                    $gambar = $pic[$i]['file_name'];
                    $gambarUpload = [
                        'laporan_id' => $id,
                        'user_id' => $token->userdata->id,
                        'gambar' => '/assets/img/' . $gambar
                    ];
                    $gambar1 = $this->bukti->insert_bukti_teknisi($gambarUpload);
                    // $newest_gambar_id = $this->bukti->get_newest_bukti();
                    // $data['bukti_laporan_id'] = $newest_gambar_id;

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
        $notifikasi = $this->notifikasi->insert_notif($notif);
        if ($notifikasi && $gambar1) {
            $data = [
                'keterangan_teknisi' => $ket_teknisi,
                'status_id' => 7
            ];
            $update = $this->laporan->update_laporan($id, $data);
            $dapet_exp=20;
            $update_user = $this->user->add_exp($dapet_exp, $laporan['uId']);
            $this->response([
                'statusCode' => 200,
                'data' => $notifikasi,
                'gambar' => $gambarUpload,
                'message' => 'Notifikasi successfully insert'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 500,
                'message' => 'Terjadi kesalahan.',
            ], 500);
        }
    }
}
