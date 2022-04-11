<?php

class Exp_model extends CI_Model
{
    public function get_all_exp()
    {
        return $this->db->get('pangkat')->result_array();
    }

    public function get_by_id_exp($id)
    {
        return $this->db->get_where('pangkat', array('id' => $id))->row_array();
    }

    public function insert_exp($data)
    {
        $this->db->insert('pangkat', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_level($id1, $user_exp)
    {
        $sql = "SELECT a.nama_lengkap as nama, a.current_exp as exp ,b.nama as level from user a, pangkat b where a.id=$user_exp and b.id=$id1";
        return $this->db->query($sql)->row_array();
    }

    public function update_exp($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('exp', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_faq($id)
    {
        $this->db->delete('faq', array('id' => $id));

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
