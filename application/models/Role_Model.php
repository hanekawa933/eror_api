<?php

class Role_Model extends CI_Model
{
    public function get_all_role()
    {
        return $this->db->get('role')->result_array();
    }
}
