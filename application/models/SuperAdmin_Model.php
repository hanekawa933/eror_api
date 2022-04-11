<?php 

class SuperAdmin_Model extends CI_Model{

    public function get_all_superadmin() {
        return $this->db->get('superadmin')->result_array();
    }

    public function create_superadmin($data){
        // $data = array(
        //     'username' => $username,
        //     'password' => $password,
        //     'nama_lengkap' => $nama_lengkap,
        //     'created_at' => $created_at,
        //     'updated_at' => $updated_at
        // );

        $this->db->insert('superadmin', $data);

        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function login_superadmin($username){
        $query = $this->db->get_where('superadmin', ['username' => $username])->row_array();
        return $query;
    }

    public function get_user_login_superadmin($id){
        $query = $this->db->get_where('superadmin', ['id' => $id])->row_array();
        return $query;
    }

    public function update_superadmin($id, $data){
        $this->db->where('id', $id);
        $this->db->update('superadmin', $data);

        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function delete_superadmin($id){
        $this->db->where('id', $id);
        $this->db->delete('superadmin');

        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
}