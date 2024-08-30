<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (isset($_SERVER['HTTP_ORIGIN'])) {
          // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one you want to allow, and if so:
          header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
          header('Access-Control-Allow-Credentials: true');
          header('Access-Control-Max-Age: 86400');    // cache for 1 day
      }
      
      // Access-Control headers are received during OPTIONS requests
      if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
          if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
              header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
      
          if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
              header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
      
          exit(0);
      }


class Seva extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('seva_model');
        $this->load->model('sevak_model');
        $this->load->model('mandal_model');
        // $this->session = $this->session;
    }

    public function add_seva($data =[]) {
        // Make sure no output is sent before this point
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);
        // var_dump($data);die;
        $this->seva_model->add_seva($data);
        $sevak = $this->sevak_model->get_sevak_details($data['sevak_id']);
        $sevak['filled_form'] = $sevak['filled_form'] + 1;
        $this->sevak_model->update_sevak($sevak);
        $response['status'] = 'true';
        $response['message'] = 'Seva Added Successfully';
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function get_seva($data =[]) {
          // Make sure no output is sent before this point
          $postData = file_get_contents("php://input");
          $data = json_decode($postData, true);
          
          $seva = $this->seva_model->get_seva($data['sevak_id']);
          $name = $this->sevak_model->get_sevak_name($data['sevak_id']);
          unset($seva['created_at']);
          unset($seva['updated_at']);
          unset($seva['deleted_at']);
          $response = [
              'name' => $name[0]['name'] ?? '',
              'seva' => $seva ?? '',
              'status' => 'true'
          ];
    
        //   $response['name'] = $name[0]['name'];
        //   $response['seva']= $seva;
        //   $response['status'] = 'true';
          
          header('Content-Type: application/json');
          echo json_encode($response);
      }
      
    public function get_seva_count(){
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);
        
        $mandals = $this->mandal_model->get_rolewise_mandal($data['sevak_id']);
        // var_dump($mandals,'kirtan');die;
        $role = $this->sevak_model->get_sevak_role($data['sevak_id']);
        // var_dump($role);die;
        $sevaks = [];
        
        if($role['role'] != 6 && $role['role'] != 7){
            // var_dump($mandals);die;
            $mandal_array = [];
            foreach($mandals as $key => $mandal){
                $mandal_target = 0;
                $mandal_filled_form = 0;
                $sevaks = $this->sevak_model->get_sevak_by_mandal($mandal['mandal_name']);
                // var_dump($sevaks);die;
                foreach($sevaks as $sevak){
                    $mandal_target += $sevak['sevak_target'];
                    $mandal_filled_form += $sevak['filled_form'];
                }
                
                $mandal_array[$key]['mandal_target'] = $mandal_target;
                $mandal_array[$key]['mandal_filled_form'] = $mandal_filled_form;
                $mandal_array[$key]['mandal_name'] = $mandal['mandal_name'];
            }
        }
        if($role['role'] == 7){
            $mandal_array = [];
            $mandals = $this->mandal_model->get_all_mandal();
            // var_dump($mandals);die;
            foreach($mandals as $key => $mandal){
                $mandal_target = 0;
                $mandal_filled_form = 0;
                $sevaks = $this->sevak_model->get_sevak_by_mandal($mandal['mandal_name']);
                // var_dump($sevaks);die;
                foreach($sevaks as $sevak){
                    $mandal_target += $sevak['sevak_target'];
                    $mandal_filled_form += $sevak['filled_form'];
                }
                
                $mandal_array[$key]['mandal_target'] = $mandal_target;
                $mandal_array[$key]['mandal_filled_form'] = $mandal_filled_form;
                $mandal_array[$key]['mandal_name'] = $mandal['mandal_name'];
            }
        }
        $response = $mandal_array;
        header('Content-Type: application/json');
        echo json_encode($response);
       
        
    }

}
