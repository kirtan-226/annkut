<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mandal_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    public function get_mandal_details($mandal)
    {
        $this->db->select('*');
        $this->db->where('mandal_name', $mandal);
        $query = $this->db->get('mandal');
        $mandal = $query->row_array();
        return $mandal;
    }
    
    public function get_all_mandal()
    {
        $this->db->select('*');
        $query = $this->db->get('mandal');
        $mandal = $query->result_array();
        return $mandal;
    }
    
    public function get_mandal_target($mandal)
    {
         $this->db->select('mandal_target, mandal_name');
        $this->db->where('mandal_name', $mandal);
        $query = $this->db->get('mandal');
        $target = $query->row_array();
        return $target;
    }
    
    public function update_mandal($mandal)
    {
        // var_dump($mandal);
        $this->db->where('mandal_name', $mandal['mandal_name']);
        $query = $this->db->update('mandal',$mandal);
    }

    public function get_rolewise_mandal($id)
    {
        $this->db->select('mandal_name');
        $this->db->group_start()
                 ->where('nirdeshak', $id)
                 ->or_where('nirikshak', $id)
                 ->or_where('sanchalak', $id)
                 ->or_where('sanyojak', $id)
                 ->group_end();
                 
        $query = $this->db->get('mandal');
        return $query->result_array();
    }

}
