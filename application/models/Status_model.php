<?php

class Status_Model extends CI_Model
{
    public function get_all_status(){
        return $this->db->get('status')->result_array();
    }
}
