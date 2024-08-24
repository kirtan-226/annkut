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


class Sevak extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('sevak_model');
        
        // $this->session = $this->session;
    }

    public function add_sevak() {
        // Make sure no output is sent before this point
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);
        $mandal = $this->sevak_model->get_sevak_mandal($data['id']);
        $sevak['filled_form'] = 0; 
        $sevak['is_changed'] = 'no'; 
        $sevak['name'] = $data['name'];
        $sevak['mandal'] = $mandal['mandal'];
        $sevak['sevak_target'] = $data['sevak_target'] ?? '';
        $sevak['filled_form'] = $data['filled_form'] ?? '';
        $sevak['phone_number'] = $data['phone_number'] ?? '';
        $sevak['password'] = 'pramukh0712';
        $this->sevak_model->add_sevak($sevak);
        $response['message'] = 'Sevak Added Successfully';
        $response['status'] = 'true';
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function get_sevak($data =[]) {
        // Make sure no output is sent before this point
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);  
        $mandal = $this->sevak_model->get_sevak_mandal($data['id']);
        $sevak = $this->sevak_model->get_sevak($mandal['mandal']);
    $name = $this->sevak_model->get_sevak_name($data['id']);
    $new_sevak_array = [];

    foreach ($sevak as $sevak_item) {
        $new_sevak_item = $sevak_item;
        $new_sevak_item['sevak_id'] = $sevak_item['id'];
        unset($new_sevak_item['id']);
        $role = $this->sevak_model->get_role($sevak_item['role']);
        $new_sevak_item['role'] = $role['role'];
        $new_sevak_array[] = $new_sevak_item;
    }

    $response = [
        'Sanchalak Name' => $name[0]['name'] ?? '',
        'sevak' => $new_sevak_array, // Use the modified $new_sevak_array here
        'status' => 'true'
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
}

    public function filter_sevak(){
          $postData = file_get_contents("php://input");
          $data = json_decode($postData, true);
          if($data){
                // $data['name']

          }

          else{
                    $mandal = $this->sevak_model->get_mandal();
                    unset($mandal['created_at']);
                    unset($mandal['updated_at']);
                    unset($mandal['deleted_at']);
          }
    }



}
