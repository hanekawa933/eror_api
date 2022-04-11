<?php
defined('BASEPATH') or exit('No direct script access allowed');
class test extends CI_Controller

{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Laporan_Model', 'laporan');
        // $this->load->helper('date');
    }
    public function dateDifference($start_date, $end_date)
    {
        // calulating the difference in timestamps 
        $diff = strtotime($start_date) - strtotime($end_date);

        // 1 day = 24 hours 
        // 24 * 60 * 60 = 86400 seconds
        return ceil(abs($diff / 86400));
    }

    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');
        $laporan = $this->laporan->get_all_laporan();
        foreach ($laporan as $a) {
            $tgl = $a['tanggal_lapor'];
            $start_date =  date('d F y  ', strtotime($tgl));
            $end_date = date('h:i:s', time() + 120);
            echo 'tanggal lapor : ' . $start_date;
            echo '<br>';
            echo 'tanggal sekarang :' . $end_date;
            $diff = $this->dateDifference($start_date, $end_date);
            echo '<br>';
            // echo json_encode($diff);

            echo "Difference between two dates: " . $diff . " Days ";
            echo '<br>';
            echo '<br>';
        }
    }
}
