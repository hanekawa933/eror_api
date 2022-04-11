<?php

class Bukti_Laporan_Model extends CI_Model{
    public function insert_bukti($data){
        $this->db->insert('bukti_laporan', $data);

        if($this->db->affected_rows() > 0){
            return $data;
        }else{
            return false;
        }
    }

    public function insert_bukti_teknisi($data)
    {
        $this->db->insert('bukti_teknisi', $data);

        if ($this->db->affected_rows() > 0) {
            return $data;
        } else {
            return false;
        }
    }

    public function get_bukti_by_laporan_id($id){
    	return $this->db->get_where('bukti_laporan', ['laporan_id' => $id])->result_array();
    }
}
