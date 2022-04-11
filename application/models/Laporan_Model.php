<?php

class Laporan_Model extends CI_Model
{
    private function _get_join_bukti_laporan($id)
    {
        return $this->db->get_where('bukti_laporan', ['laporan_id' => $id])->result_array();
    }

    private function _get_join_bukti_teknisi($id)
    {
        return $this->db->get_where('bukti_teknisi', ['laporan_id' => $id])->result_array();
    }

    public function get_all_laporan()
    {
        $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
        $this->db->from('laporan');
        $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
        $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
        $this->db->join('status', 'status.id = laporan.status_id', 'left outer');

        $this->db->order_by('laporan.tanggal_lapor', 'DESC');
        $laporan = $this->db->get()->result_array();

        foreach ($laporan as $key => $res) {
            $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
            $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
        }
        return $laporan;
    }

    public function get_laporan_by_id($laporan_id)
    {
        $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
        $this->db->from('laporan');
        $this->db->where('laporan.id', $laporan_id);
        $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
        $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
        $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
        $this->db->join('bukti_laporan', 'bukti_laporan.laporan_id = laporan.id', 'left outer');
        $this->db->join('bukti_teknisi', 'bukti_teknisi.laporan_id = laporan.id', 'left outer');
        $this->db->order_by('laporan.tanggal_lapor', 'DESC');
        $laporan = $this->db->get()->row_array();
        $laporan['gambar'] = $this->_get_join_bukti_laporan($laporan['lId']);
        $laporan['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan['lId']);
        return $laporan;
    }

    public function get_laporan_by_user_login($pelapor_id)
    {
        $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
        $this->db->from('laporan');
        $this->db->where('laporan.pelapor_id', $pelapor_id);
        $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
        $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
        $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
        $this->db->order_by('laporan.tanggal_lapor', 'DESC');
        $laporan = $this->db->get()->result_array();
        foreach ($laporan as $key => $res) {
            $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
            $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
        }
        return $laporan;
    }

    public function get_laporan_by_kategori_id($kategori_id)
    {
        $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
        $this->db->from('laporan');
        $this->db->where('laporan.kategori_id', $kategori_id);
        $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
        $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
        $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
        $this->db->order_by('laporan.tanggal_lapor', 'DESC');
        $laporan = $this->db->get()->result_array();
        foreach ($laporan as $key => $res) {
            $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
            $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
        }
        return $laporan;
    }

    public function get_laporan_terbaru()
    {
        $row = $this->db->select("*")->limit(1)->order_by('id', "DESC")->get("laporan")->row();
        return $row->id;
    }

    public function get_by_id_laporan($id)
    {
        return $this->db->get_where('laporan', ['id' => $id])->row_array();
    }

    public function insert_laporan($data)
    {
        $this->db->insert('laporan', $data);

        if ($this->db->affected_rows() > 0) {
            return $data;
        } else {
            return false;
        }
    }

    public function update_laporan($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('laporan', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }



    public function delete_laporan($id)
    {
        $this->db->delete('laporan', ['id' => $id]);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function delete_cascade_laporan($id)
    {
        $this->db->delete('bukti_laporan', ['laporan_id' => $id]);
        $this->db->delete('bukti_teknisi', ['laporan_id' => $id]);
        $this->db->delete('notifikasi', ['laporan_id' => $id]);
        $this->db->delete('laporan', ['id' => $id]);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_laporan_by_query($query)
    {
        if ($query == "admin") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where_in('status_id', [1, 2, 3, 6, 7]);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        } else if ($query == "teknisi") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where_in('status_id', [3, 4, 5, 6]);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        } else {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        }
    }

    public function get_laporan_by_kategori_and_query($query, $kategori, $id)
    {
        if ($query == "admin") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where('kategori_id', $kategori);
            $this->db->where_in('status_id', [1,2,3,6,7]);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        }else if($query == "admin_web"){
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where('kategori_id', $kategori);
            $this->db->where_in('status_id', 1);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;            
        } else if ($query == "teknisi") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where('kategori_id', $kategori);
            $this->db->where_in('status_id', [3, 4, 5, 6]);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        } else if ($query == "teknisi_web") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where('kategori_id', $kategori);
            $this->db->where_in('status_id', [3, 4, 5]);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        }else if ($query == "user") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where('pelapor_id', $id);
            $this->db->where('kategori_id', $kategori);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        } else {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, bukti_laporan.id as bId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*, bukti_laporan.*');
            $this->db->from('laporan');
            $this->db->where('kategori_id', $kategori);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        }
    }

    public function get_laporan_by_kategori_status_and_query($query, $status, $kategori, $id)
    {
        if ($query == "admin") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where('status_id', $status);
            $this->db->where('kategori_id', $kategori);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        } else if ($query == "teknisi") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where('status_id', $status);
            $this->db->where('kategori_id', $kategori);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        } else if ($query == "user") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where('pelapor_id', $id);
            $this->db->where('status_id', $status);
            $this->db->where('kategori_id', $kategori);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        } else {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, bukti_laporan.id as bId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*, bukti_laporan.*');
            $this->db->from('laporan');
            $this->db->where('status_id', $status);
            $this->db->where('kategori_id', $kategori);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        }
    }

    public function get_laporan_history_query($query, $id)
    {
        if ($query == "admin") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where_in('status_id', [2, 3, 6, 7]);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        } else if ($query == "teknisi") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where_in('status_id', [4, 5, 6, 7]);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        } else if ($query == "user") {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*');
            $this->db->from('laporan');
            $this->db->where('pelapor_id', $id);
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        } else {
            $this->db->select('laporan.id as lId, user.id as uId, kategori.id as kId, status.id as sId, bukti_laporan.id as bId, kategori.nama as kategori, status.nama as status, laporan.*, user.*, kategori.*, status.*, bukti_laporan.*');
            $this->db->from('laporan');
            $this->db->join('user', 'user.id = laporan.pelapor_id', 'left outer');
            $this->db->join('kategori', 'kategori.id = laporan.kategori_id', 'left outer');
            $this->db->join('status', 'status.id = laporan.status_id', 'left outer');
            $this->db->order_by('laporan.tanggal_lapor', 'DESC');
            $laporan = $this->db->get()->result_array();
            foreach ($laporan as $key => $res) {
                $laporan[$key]['gambar'] = $this->_get_join_bukti_laporan($laporan[$key]['lId']);
                $laporan[$key]['gambarTeknisi'] = $this->_get_join_bukti_teknisi($laporan[$key]['lId']);
            }
            return $laporan;
        }
    }

    public function get_laporan_count_by_kategori_and_query($query, $kategori, $id)
    {
        if ($query == "admin") {
            $this->db->select('*');
            $this->db->from('laporan');
            $this->db->where('kategori_id', $kategori);
            $this->db->where_in('status_id', [1, 7]);
            $laporan_count = $this->db->count_all_results();
            return $laporan_count;
        } else if ($query == "teknisi") {
            $this->db->select('*');
            $this->db->from('laporan');
            $this->db->where('kategori_id', $kategori);
            $this->db->where_in('status_id', [3, 4, 5]);
            $laporan_count = $this->db->count_all_results();
            return $laporan_count;
        } else if ($query == "user") {
            $this->db->select('*');
            $this->db->from('laporan');
            $this->db->where('pelapor_id', $id);
            $this->db->where('kategori_id', $kategori);
            $laporan_count = $this->db->count_all_results();
            return $laporan_count;
        } else {
            $this->db->select('*');
            $this->db->from('laporan');
            $this->db->where('kategori_id', $kategori);
            $laporan_count = $this->db->count_all_results();
            return $laporan_count;
        }
    }
    
    public function get_every_laporan_count(){
        $data = [];
        $this->db->select('*');
        $this->db->from('kategori');
        $kategori = $this->db->get()->result_array();
        
        $this->db->select('*');
        $this->db->from('laporan');
        $this->db->where('DATE(tanggal_lapor)', date('Y-m-d', strtotime("now")));
        $today = $this->db->count_all_results();
        
        $this->db->select('*');
        $this->db->from('laporan');
        $this->db->where('DATE(tanggal_lapor)', date('Y-m-d', strtotime("-1 days")));
        $yesterday = $this->db->count_all_results();
        
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('DATE(created_at)', date('Y-m-d', strtotime("now")));
        $todayUser = $this->db->count_all_results();
        
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('DATE(created_at)', date('Y-m-d', strtotime("-1 days")));
        $yesterdayUser = $this->db->count_all_results();
        
        $this->db->select('*');
        $this->db->from('laporan');
        $count = $this->db->count_all_results();
        
        $data[] = ['id' => 999, 'nama' => 'semua', 'kd_kategori' => 'SM', 'icon' => '/assets/svg/building.svg', 'count' => $count, 'yesterday' => $yesterday, 'today' => $today];
        
        foreach($kategori as $key=>$res){
            $this->db->select('*');
            $this->db->from('laporan');
            $this->db->where('kategori_id', $res["id"]);
            $ct = $this->db->count_all_results();
            
            $this->db->select('*');
            $this->db->from('laporan');
            $this->db->where('kategori_id = '.$res["id"].' AND DATE(tanggal_lapor)=', date('Y-m-d', strtotime("now")));
            $tod = $this->db->count_all_results();
        
            $this->db->select('*');
            $this->db->from('laporan');
            $this->db->where('kategori_id = '.$res["id"].' AND DATE(tanggal_lapor)=', date('Y-m-d', strtotime("-1 days")));
            $yes = $this->db->count_all_results();
            
            $kategori[$key]['yesterday'] = $yes;
            $kategori[$key]['today'] = $tod;
            $kategori[$key]['count'] = $ct;
            $data[] = $kategori[$key];
        }
    
        
        
        
        $this->db->select('*');
        $this->db->from('user');
        $countRes = $this->db->count_all_results();
        
        $result = [['nama' => 'User', 'jumlah' => $countRes, 'yesterday' => $yesterdayUser, 'today' => $todayUser], ['nama' => 'Laporan', 'jumlah' => $count, 'yesterday' => $yesterday, 'today' => $today]];
        $combined = ['laporan' => $data, 'count' => $result];
        return $combined;
    }
}
