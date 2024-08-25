<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function check_user($data = [])
    {
        $this->db->select('*');
        $this->db->where('sevak_id',$data['sevak_id']);
        $this->db->where('password',$data['password']);
        // $this->db->where('deleted_at',null);
        $user = $this->db->get('annkut_sevak')->row_array();
        return $user;
    }

    public function reset_password($data = [])
    {     $this->db->where('shibir_id',$data['shibir_id']);
          $this->db->where('deleted_at',null);
          $user = $this->db->update('shibir_users',$data)->result_array();
          return $user;
    }

    public function forgot_password($data = [])
    {
        // var_dump($data);die;
        $check =[];
        $this->db->where('sevak_id',$data['sevak_id']);
        $this->db->where('password',$data['password']);
        $x = $this->db->get('annkut_sevak',$check)->row_array();
        
        $this->db->where('sevak_id',$data['sevak_id']);
        $this->db->where('phone_number',$data['phone_number']);
        $y = $this->db->get('annkut_sevak',$check)->row_array();
        // var_dump($x);die;
        if(isset($y) && !empty($y)){
            if(!isset($x) && empty($x)){
                $this->db->where('sevak_id',$data['sevak_id']);
                $this->db->where('phone_number',$data['phone_number']);
                // $this->db->where('deleted_at',null);
                $this->db->update('annkut_sevak',$data);
                // var_dump($this->db->last_query());die;
                $response = $this->db->affected_rows();
            }
            else{
                $response = 3;
            }
        }
        else{
            $response = 2;
        }
        // $rows_affected = $this->db->affected_rows();
        
        
        return $response;
    }
}
