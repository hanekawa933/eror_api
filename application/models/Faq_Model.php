<?php

class Faq_Model extends CI_Model
{
    public function get_all_faq()
    {
        return $this->db->get('faq')->result_array();
    }

    public function get_by_id_faq($id)
    {
        return $this->db->get_where('faq', array('id' => $id))->row_array();
    }

    public function insert_faq($data)
    {
        $this->db->insert('faq', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function update_faq($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('faq', $data);

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
