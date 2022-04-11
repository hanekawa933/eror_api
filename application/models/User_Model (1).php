<?php

class User_Model extends CI_Model
{

    public function get_all_user()
    {
        $this->db->select('user.id as uId, role.id as rId, user.*, role.*');
        $this->db->from('user');
        $this->db->join('role', 'user.role_id = role.id', 'left outer');
        return $this->db->get()->result_array();
    }

    public function get_by_email($email)
    {
        return $this->db->get_where('user', array('email' => $email))->row_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('user', array('id' => $id))->row_array();
    }


    public function create_user($data)
    {
        $this->db->insert('user', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insert_user($data)
    {
        $this->db->insert('user', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insert_forgot($data)
    {
        $this->db->insert('forgot_password', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function login_user($email)
    {
        $query = $this->db->get_where('user', ['email' => $email])->row_array();
        return $query;
    }

    public function get_user_login($id)
    {
        $query = $this->db->get_where('user', ['id' => $id])->row_array();
        return $query;
    }

    public function update_user($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('user', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function update_foto($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->set('foto_profile', $data['foto_profile']);
        $this->db->update('user');
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function add_exp($dapet_exp, $data)
    {
        // $this->db->set('current_exp', 'current_exp + 1');
        // $this->db->where('id', $data);
        // $update = $this->db->update('user');
        $sql = "UPDATE user set current_exp =current_exp +$dapet_exp where id=$data";
        $hsl = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_kode($email)
    {
        $this->db->where('email', $email);
        $this->db->delete('forgot_password');

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_check_kode($data)
    {
        $this->db->where($data);
        return $this->db->get('forgot_password')->row_array();
    }

    public function delete_used_kode($data)
    {
        $this->db->where($data);
        $this->db->delete('forgot_password');
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_user($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('user');

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
