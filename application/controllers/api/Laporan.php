<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(APPPATH . '../');
$dotenv->load();

class Laporan extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Laporan_Model', 'laporan');
        $this->load->model('Kategori_Model', 'kategori');
        $this->load->model('Bukti_Laporan_Model', 'bukti');
        $this->load->model('Status_model', 'status');
        $this->load->model('User_Model', 'user');
        $this->load->library(array('upload', 'Pdf'));
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
    public function dateDifference($start_date, $end_date)
    {
        // calulating the difference in timestamps 
        $diff = strtotime($start_date) - strtotime($end_date);

        // 1 day = 24 hours 
        // 24 * 60 * 60 = 86400 seconds
        return ceil(abs($diff / 86400));
    }


    public function user_get()
    {
        $this->_auth();
        $token = $this->user_data;
        $laporan = $this->laporan->get_laporan_by_user_login($token->userdata->id);
        foreach ($laporan as $a => $res) {
            $now = date('d F y');
            $tgl_lapor = date('d F y  ', strtotime($res['tanggal_lapor']));
            $beda = $this->dateDifference($tgl_lapor, $now);
            $since = $beda . ' hari yang lalu';
            $laporan[$a]['date_diff'] = $since;
        }
        if ($laporan) {
            $this->response([
                'statusCode' => 200,
                'data' => $laporan,
                'message' => 'Data successfully fetched...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 200,
                'message' => 'No data found...',
            ], 200);
        }
    }

    public function index_get()
    {
        $this->_auth();
        $query = $this->get('query');

        if ($query == "admin" || $query == "teknisi" || !$query) {
            $laporan = $this->laporan->get_laporan_by_query($query);
            foreach ($laporan as $key => $res) {
                $now = date('d F y');
                $tgl_lapor = date('d F y  ', strtotime($res['tanggal_lapor']));
                $beda = $this->dateDifference($tgl_lapor, $now);
                $since = $beda . ' hari yang lalu';

                $laporan[$key]['date_diff'] = $since;
            }

            if ($laporan) {
                $this->response([
                    'statusCode' => 200,
                    'data' => $laporan,
                    'message' => 'Data successfully fetched...'
                ], 200);
            } else {
                $this->response([
                    'statusCode' => 500,
                    'message' => 'Something went wrong...',
                ], 500);
            }
        } else {
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
        $data = array();
        $laporan = $this->laporan->get_laporan_by_id($id);

        $now = date('d F y');
        $tgl_lapor = date('d F y  ', strtotime($laporan['tanggal_lapor']));
        $beda = $this->dateDifference($tgl_lapor, $now);
        $since = $beda . ' hari yang lalu';
        $laporan['date_diff'] = $since;

        if ($laporan) {
            $this->response([
                'statusCode' => 200,
                'data' => $laporan,
                'message' => 'Successfully fetched single laporan...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 404,
                'data' => [],
                'message' => 'No laporen were to be found...'
            ], 404);
        }
    }

    public function history_get()
    {
        $this->_auth();
        $token = $token = $this->user_data;
        $query = $this->get('query');

        if ($query == "admin" || $query == "teknisi" || $query == "user" || !$query) {
            $laporan = $this->laporan->get_laporan_history_query($query, $token->userdata->id);
            foreach ($laporan as $key => $res) {
                $now = date('d F y');
                $tgl_lapor = date('d F y  ', strtotime($res['tanggal_lapor']));
                $beda = $this->dateDifference($tgl_lapor, $now);
                $since = $beda . ' hari yang lalu';

                $laporan[$key]['date_diff'] = $since;
            }

            if ($laporan) {
                $this->response([
                    'statusCode' => 200,
                    'data' => $laporan,
                    'message' => 'Data successfully fetched...'
                ], 200);
            } else {
                $this->response([
                    'statusCode' => 200,
                    'message' => 'No data were to be found...',
                ], 200);
            }
        } else {
            $this->response([
                'statusCode' => 404,
                'data' => [],
                'message' => 'No query were to be found...',
            ], 404);
        }
    }

    public function kategori_get()
    {
        $this->_auth();
        $idCategory = $this->get('category');
        $idStatus = $this->get('status');
        $query = $this->get('query');
        $token = $this->user_data;

        if ($query == "admin" || $query == "teknisi" || $query == "user" || !$query || $query == "admin_web" || $query == "teknisi_web") {
            if ($idCategory) {
                if ($idStatus) {
                    $laporan = $this->laporan->get_laporan_by_kategori_status_and_query($query, $idStatus, $idCategory, $token->userdata->id);

                    foreach ($laporan as $key => $res) {
                        $now = date('d F y');
                        $tgl_lapor = date('d F y  ', strtotime($res['tanggal_lapor']));
                        $beda = $this->dateDifference($tgl_lapor, $now);
                        $since = $beda . ' hari yang lalu';

                        $laporan[$key]['date_diff'] = $since;
                    }
                    if ($laporan) {
                        $this->response([
                            'statusCode' => 200,
                            'data' => $laporan,
                            'message' => 'Data successfully fetched...'
                        ], 200);
                    } else {
                        $this->response([
                            'statusCode' => 200,
                            'message' => 'No data were to be found...',
                        ], 200);
                    }
                } else {
                    $laporan = $this->laporan->get_laporan_by_kategori_and_query($query, $idCategory, $token->userdata->id);

                    foreach ($laporan as $key => $res) {
                        $now = date('d F y');
                        $tgl_lapor = date('d F y  ', strtotime($res['tanggal_lapor']));
                        $beda = $this->dateDifference($tgl_lapor, $now);
                        $since = $beda . ' hari yang lalu';

                        $laporan[$key]['date_diff'] = $since;
                    }
                    if ($laporan) {
                        $this->response([
                            'statusCode' => 200,
                            'data' => $laporan,
                            'message' => 'Data successfully fetched...'
                        ], 200);
                    } else {
                        $this->response([
                            'statusCode' => 200,
                            'message' => 'No data were to be found...',
                        ], 200);
                    }
                }
            } else {
                $this->response([
                    'statusCode' => 404,
                    'message' => 'No category were to be found...',
                ], 404);
            }
        } else {
            $this->response([
                'statusCode' => 404,
                'message' => 'No query were to be found...',
            ], 404);
        }
    }

    public function user_post()
    {
        $this->_auth();
        $token = $this->user_data;
        $data = [
            'pelapor_id' => $token->userdata->id,
            'jenis_kerusakan' => $this->post('jenis_kerusakan'),
            'lokasi' => $this->post('lokasi'),
            'keterangan' => $this->post('keterangan'),
            'tanggal_lapor' => date('Y-m-d H:i:s'),
            'tanggal_pengecekan' => date('Y-m-d H:i:s'),
            'kategori_id' => $this->post('kategori_id'),
            'status_id' => 1
        ];
        $kategori = $this->kategori->get_by_id_kategori($data['kategori_id']);
        $kd_laporan = '#RP-' . rand(10000, 99999) . $kategori['kd_kategori'];

        $data['kode_laporan'] = $kd_laporan;

        $pic = array();
        $total = count($_FILES['gambar']['name']);
        $l = $_FILES;
        $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '/eror_api/assets/img/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|mp3|mp4|mkv|avi|mov'; //type yang dapat diakses bisa anda sesuaikan
        $config['max_size'] = '2048000000'; //maksimum besar file 2M
        $config['max_width']  = '0'; //lebar maksimum 4096 px
        $config['max_height']  = '0'; //tinggi maksimu 2160 px
        // $config['file_name'] = $_FILES['gambar']['name']; //nama yang terupload nantinya$config['max_width']  = '4096'; //lebar maksimum 4096 px
        // $config['max_width']  = '4096'; //lebar maksimum 4096 px
        // $config['max_height']  = '2160'; //tinggi maksimu 2160 px
        $laporan = $this->laporan->insert_laporan($data);
        $id_newest_laporan = $this->laporan->get_laporan_terbaru();
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
                        'laporan_id' => $id_newest_laporan,
                        'gambar' => '/assets/img/' . $gambar
                    ];
                    $gambar1 = $this->bukti->insert_bukti($gambarUpload);
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
        if ($laporan && $gambar1) {
            if ($total > 0) {
                $dapet_exp = $total * 10;
            }
            $update_user = $this->user->add_exp($dapet_exp, $data['pelapor_id']);
            $this->response([
                'statusCode' => 200,
                'data' => $laporan,
                'gambar' => $gambarUpload,
                'message' => 'Successfully create data laporan...'
            ], 200);
        }
    }

    public function index_post()
    {
        $this->_auth();
        $data = [
            'pelapor_id' => $this->post('pelapor_id'),
            'jenis_kerusakan' => $this->post('jenis_kerusakan'),
            'lokasi' => $this->post('lokasi'),
            'keterangan' => $this->post('keterangan'),
            'tanggal_lapor' => date('Y-m-d H:i:s'),
            'kategori_id' => $this->post('kategori_id'),
            'status_id' => 1
        ];
        $kategori = $this->kategori->get_by_id_kategori($data['kategori_id']);
        $kd_laporan = '#RP-' . rand(10000, 99999) . $kategori['kd_kategori'];

        $data['kode_laporan'] = $kd_laporan;

        $pic = array();
        $total = count($_FILES['gambar']['name']);
        $l = $_FILES;
        $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '/eror_api/assets/img/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|mp3|mp4|mkv|avi|mov'; //type yang dapat diakses bisa anda sesuaikan
        $config['max_size'] = '2048000000'; //maksimum besar file 2M
        $config['max_width']  = '0'; //lebar maksimum 4096 px
        $config['max_height']  = '0'; //tinggi maksimu 2160 px
        // $config['file_name'] = $_FILES['gambar']['name']; //nama yang terupload nantinya$config['max_width']  = '4096'; //lebar maksimum 4096 px
        // $config['max_width']  = '4096'; //lebar maksimum 4096 px
        // $config['max_height']  = '2160'; //tinggi maksimu 2160 px
        $laporan = $this->laporan->insert_laporan($data);
        $id_newest_laporan = $this->laporan->get_laporan_terbaru();
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
                        'laporan_id' => $id_newest_laporan,
                        'gambar' => '/assets/img/' . $gambar
                    ];
                    $gambar1 = $this->bukti->insert_bukti($gambarUpload);
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
        if ($laporan && $gambar1) {
            if ($total > 0) {
                $dapet_exp = $total * 10;
            }
            $update_user = $this->user->add_exp($dapet_exp, $data['pelapor_id']);
            $this->response([
                'statusCode' => 200,
                'data' => $laporan,
                'gambar' => $gambarUpload,
                'message' => 'Successfully create data laporan...'
            ], 200);
        }
    }

    public function update_put()
    {
        $this->_auth();
        $data = [
            'pelapor_id' => $this->put('pelapor_id'),
            'jenis_kerusakan' => $this->put('jenis_kerusakan'),
            'lokasi' => $this->put('lokasi'),
            'keterangan' => $this->put('keterangan'),
            'keterangan_admin' => $this->put('keterangan_admin'),
            'keterangan_teknisi' => $this->put('keterangan_teknisi'),
            'tanggal_lapor' => date('Y-m-d H:i:s'),
            'tanggal_pengecekan' => date('Y-m-d H:i:s'),
            'kategori_id' => $this->put('kategori_id'),
            'status_id' => $this->put('status_id')
        ];

        $id = $this->get('id');

        $laporan_update = $this->laporan->update_laporan($id, $data);
        if ($laporan_update) {
            $this->response([
                'statusCode' => 200,
                'message' => 'Successfully update laporan...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 200,
                'message' => 'Failed to update laporan maybe because laporan with that id doesn\'t exists...'
            ], 200);
        }
    }

    public function delete_delete()
    {
        $this->_auth();

        $id = $this->get('id');
        $laporan = $this->laporan->delete_cascade_laporan($id);
        if ($laporan) {
            $this->response([
                'statusCode' => 200,
                'message' => 'Successfully delete laporan...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 200,
                'message' => 'Failed to delete maybe because kategori with that id doesn\'t exists...'
            ], 200);
        }
    }

    public function status_get()
    {
        $this->_auth();
        $status = $this->status->get_all_status();
        if ($status) {
            $this->response([
                'statusCode' => 200,
                'data' => $status,
                'message' => 'Successfully get data status...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 404,
                'message' => 'No data were to be found...'
            ], 404);
        }
    }
    
    public function sum_laporan_get(){
        $this->_auth();
        $laporan = $this->laporan->get_every_laporan_count();
        if ($laporan) {
            $this->response([
                'statusCode' => 200,
                'data' => $laporan,
                'message' => 'Successfully get data status...'
            ], 200);
        } else {
            $this->response([
                'statusCode' => 404,
                'message' => 'No data were to be found...'
            ], 404);
        }
    }

    public function cetak_pdf_get()
    {
        $laporan_id = $this->get('id_laporan');
        // $bukti_laporan = $this->bukti->get_bukti_by_laporan_id($laporan_id);
        $laporan = $this->laporan->get_laporan_by_id($laporan_id);
        $gambar = $laporan['gambar'];
        // $count = count($gambar);
        // $data = [];
        // $no = 0;


        error_reporting(0); // AGAR ERROR MASALAH VERSI PHP TIDAK MUNCUL
        $pdf = new FPDF('P', 'mm', 'Legal');
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 10, '', 0, 1);
        $pdf->Cell(0, 5, '', 0, 1);

        $pdf->Image('./assets/img/EROR.png', 50, 23, 11);
        $pdf->Cell(0, 2, 'ELECTRONIC - REQUEST FOR REPAIR (E-ROR)', 0, 1, 'C');
        $pdf->Cell(0, 10, 'ISTANA KEPRESIDENAN YOGYAKARTA', 0, 2, $pdf->setX(63));
        $pdf->Cell(10, 7, '', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 1, 'Pelapor          : ' . $laporan['nama_lengkap'], 0, 1, $pdf->setX(20));
        $pdf->Cell(0, 10, 'Kode/Status   : ' . $laporan['kode_laporan'] . '/' . $laporan['status'], 0, 1, $pdf->setX(20));
        $pdf->Cell(0, 1, 'Kerusakan     : ' . $laporan['jenis_kerusakan'], 0, 1, $pdf->setX(20));
        $pdf->Cell(0, 10, 'Lokasi            : ' . $laporan['lokasi'], 0, 1, $pdf->setX(20));
        $pdf->Cell(0, 1, 'Tahun            : ' . date("d F Y", strtotime($laporan['tanggal_lapor'])), 0, 1, $pdf->setX(20));

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, '', 0, 1);
        $pdf->Cell(0, 5, 'Foto Laporan Kerusakan', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Keterangan    :', 0, 1, $pdf->setX(20));
        $gambar = array_slice($laporan['gambar'], 0, 3);
        $count = count($gambar);
        $cellPosition = 0;
        $cellNextPosition = 0;

        if ($gambar) {
            foreach ($gambar as $wew) {
                // $pdf->Cell($cellPosition += 60, 70, '', 1, 0, $pdf->setX(20));
                $pdf->Image('.' . $wew['gambar'], 30 + $cellNextPosition, 100, 45, 45);
                $cellNextPosition += 60;
            }
        }


        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 80, '', 0, 1);
        $pdf->Cell(0, 5, 'Foto Laporan Perbaikan', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Keterangan    :', 0, 1, $pdf->setX(20));
        $gambar_teknisi = $laporan['gambarTeknisi'];
        $gambarT = array_slice($gambar_teknisi, 0, 3);
        $count = count($gambarT);
        $cellPosition1 = 0;
        $cellNextPosition1 = 0;
        if ($gambarT) {
            foreach ($gambarT as $wewe) {
                // $pdf->Cell($cellPosition1 += 60, 70, '', 1, 0, $pdf->setX(20));
                $pdf->Image('.' . $wewe['gambar'], 33 + $cellNextPosition1, 208, 35, 35);
                $cellNextPosition += 60;
            }
        }
        // $pdf->Cell(60, 70, '', 1, 0, $pdf->setX(20));
        // $pdf->Cell(120, 70, '', 1, 0, $pdf->setX(20));
        // $pdf->Cell(180, 70, '', 1, 1, $pdf->setX(20));



        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 70, '', 0, 1);
        $pdf->Cell(0, 15, 'Keterangan Admin    :', 0, 1, 'C');
        $pdf->Cell(200, 50, '' . $laporan['keterangan_admin'] . '', 1, 1, $pdf->setX(10));

        $nama = 'Laporan ' . $laporan['kode_laporan'] . '.pdf';
        $pdf->Output('D', $nama);
    }
    // public function testing_get(){
    //     $laporan_id=104;
    //     $laporan=$this->laporan->get_laporan_by_id($laporan_id);
    //     $gambar=$laporan['gambar']

    //     echo  'erorsetneg.com/eror_api/'.$gambar;
    // }
}
