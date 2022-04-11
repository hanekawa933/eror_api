<?php

class Kategori_Model extends CI_Model
{
    public function get_all_kategori()
    {
        return $this->db->get('kategori')->result_array();
    }

    public function get_by_id_kategori($id)
    {
        return $this->db->get_where('kategori', array('id' => $id))->row_array();
    }

    public function insert_kategori($data)
    {
        $this->db->insert('kategori', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function update_kategori($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('kategori', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_kategori($id)
    {
        $this->db->delete('kategori', array('id' => $id));

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
