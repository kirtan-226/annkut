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
        // $this->session = $this->session;
    }

    public function add_seva($data =[]) {
        // Make sure no output is sent before this point
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);  
           
        $this->seva_model->add_seva($data);
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

    


}
