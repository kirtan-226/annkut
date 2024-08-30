<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sevak_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function add_sevak($data = [])
    {
        $this->db->insert('annkut_sevak', $data);
    }

    public function update_sevak($data = [])
    {
        $this->db->where('sevak_id',$data['sevak_id']);
        $this->db->update('annkut_sevak', $data);
    }

    public function delete_sevak(array $data)
    {
        $this->db->set('deleted_at', 'NOW()', FALSE);
        $this->db->where('sevak_id', $data['sevak_id']);
        $this->db->update('annkut_sevak');
    }

    public function get_sevak_by_mandal($name)
    {
        // var_dump($name);die;
        $this->db->select('*');  // Correct the spelling if it's 'mandal' instead of 'madal'
        $this->db->where('mandal', $name);
        $query = $this->db->get('annkut_sevak');  // Perform the query and store the result in $query
        $mandal = $query->result_array();  // Call result_array() on the query result object
        return $mandal;
    }

    public function check_admin($id)
    {
          $this->db->select('*');
          $this->db->where('sevak_id',$id);
          $query = $this->db->get('annkut_sevak');
          $mandal = $query->row_array();
          return $mandal;
    }

    public function get_sevak_name($id)
    {
        $this->db->select('name');
        $this->db->where('sevak_id', $id);
        $query = $this->db->get('annkut_sevak'); // Use get() instead of from()
        $result = $query->result_array();
        return $result;
    }
    
    public function get_sevak_role($id)
    {
        $this->db->select('role');
        $this->db->where('sevak_id', $id);
        $query = $this->db->get('annkut_sevak');  // Use get() instead of from()
        $result = $query->row_array();
        return $result;
    }
    
    public function get_role($id)
    {
        $this->db->select('role');
        $this->db->where('id', $id);
        $query = $this->db->get('roles'); // Use get() instead of from()
        $result = $query->row_array();
        return $result;
    }
    
    public function get_all_users()
    {
        $this->db->select('*');
        $query = $this->db->get('annkut_Sevak'); // Use get() instead of from()
        $result = $query->result_array();
        return $result;
    }
    
    public function get_all_mandal()
    {
        $this->db->select('*');
        // $this->db->where('id', $id);
        $query = $this->db->get('mandal'); // Use get() instead of from()
        $result = $query->result_array();
        return $result;
    }

    public function get_sevak_mandal($id)
    {
        $this->db->select('mandal');  // Correct the spelling if it's 'mandal' instead of 'madal'
        $this->db->where('sevak_id', $id);
        $query = $this->db->get('annkut_sevak');  // Perform the query and store the result in $query
        $mandal = $query->row_array();  // Call result_array() on the query result object
        return $mandal;
    }

    public function check_id($id)
    {
        $this->db->select('mandal');  // Correct the spelling if it's 'mandal' instead of 'madal'
        $this->db->where('id', $id);
        $query = $this->db->get('annkut_sevak');  // Perform the query and store the result in $query
        $mandal = $query->row_array();  // Call result_array() on the query result object
        return $mandal;
    }

    public function get_sevak_id($name)
    {
        $this->db->select('sevak_id');  // Correct the spelling if it's 'mandal' instead of 'madal'
        $this->db->where('name', $name);
        $query = $this->db->get('annkut_sevak');  // Perform the query and store the result in $query
        $mandal = $query->row_array();  // Call result_array() on the query result object
        return $mandal;
    }


    public function get_sevak($mandal)
    {
        $this->db->select('*');  
        $this->db->where('mandal', $mandal);  
        // $this->db->where('deleted_at IS NULL'); 
        $query = $this->db->get('annkut_sevak');  
        $result = $query->result_array(); 
        return $result;
    }
    
    public function get_all_sevak()
    {
        $this->db->select('*');  
        // $this->db->where('mandal', $mandal);  
        // $this->db->where('deleted_at IS NULL'); 
        $query = $this->db->get('annkut_sevak');  
        $result = $query->result_array(); 
        return $result;
    }
    

    public function get_sevak_details($id)
    {
        $this->db->select('*');
        $this->db->where('sevak_id', $id);
        // $this->db->where('deleted_at IS NULL'); 
        $query = $this->db->get('annkut_sevak');  // Execute the query
        $mandal = $query->row_array();  // Get the result as an array
        return $mandal;
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
        $this->db->where('shibir_id',$data['shibir_id']);
        $this->db->where('password',$data['password']);
        $x = $this->db->get('shibir_users',$check)->row_array();
        
        $this->db->where('shibir_id',$data['shibir_id']);
        $this->db->where('phone_number',$data['phone_number']);
        $y = $this->db->get('shibir_users',$check)->row_array();
        // var_dump($x);die;
        if(isset($y) && !empty($y)){
            if(!isset($x) && empty($x)){
                $this->db->where('shibir_id',$data['shibir_id']);
                $this->db->where('phone_number',$data['phone_number']);
                // $this->db->where('deleted_at',null);
                $this->db->update('shibir_users',$data);
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
