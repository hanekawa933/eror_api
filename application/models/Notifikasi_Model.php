<?php

class Notifikasi_Model extends CI_Model
{
    public function get_all_notifikasi()
    {
        return $this->db->get('notifikasi')->result_array();
    }

    public function get_by_id_notifikasi($id)
    {
        return $this->db->get_where('notifikasi', ['id' => $id])->row_array();
    }

    public function get_notifikasi_by_user_login($id)
    {
        $this->db->select('notifikasi.id as nId, laporan.id as lId, laporan.*, notifikasi.*');
        $this->db->from('notifikasi');
        $this->db->join('laporan', 'notifikasi.laporan_id = laporan.id', 'left outer');
        $this->db->where('pelapor_id', $id);
        $this->db->order_by('created_at', 'DESC');
        return $laporan = $this->db->get()->result_array();
    }

    public function insert_notif($data)
    {
        $this->db->insert("notifikasi", $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function get_by_id_laporan($id)
    {
        $this->db->where('id_laporan', $id);
        return $this->db->get('notifikasi')->result_array();
        // $sql = "select b.id as id_notif,a.status_id as status_id, a.kode_laporan as kode_laporan, b.pesan as pesan, b.keterangan as
        //  from laporan a, notifikasi b where a.id=b.id_laporan and b.id_laporan=$id";
        // $hsl = $this->db->query($sql);
        // return $hsl->row_array();
    }

    public function get_laporan_status($id_laporan)
    {
        $sql = "SELECT b.id as id_notifikasi, a.kode_laporan as kode_laporan, b.pesan as pesan, b.keterangan as keterangan, a.status_id as status_laporan
        from laporan a, notifikasi b
         where a.id=b.id_laporan and b.id_laporan=$id_laporan and a.status_id=b.status_id";
        $hsl = $this->db->query($sql);
        return $hsl->row_array();
    }
}
