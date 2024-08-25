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


class Import_karyakar extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('import_karyakar_model');
        $this->load->model('sevak_model');
        
        // $this->session = $this->session;
    }

    public function index(){
          $data = [];
          $file_lines = file('C:\Users\limba\OneDrive\Desktop\Book2.csv', FILE_IGNORE_NEW_LINES);
      
          foreach ($file_lines as $line){
              $data[] = str_getcsv($line);
          }
      
          $keys = array_shift($data);
          $result = [];
          foreach ($data as $values) {
              $x = array_combine($keys, $values);
              $result [] = $x;
          }
          // var_dump($result);
          // die;
          $shibir_users = [];
          $id_sequence = 1;
          foreach($result as $value){
                    $shibir_user = [];
                    // $shibir_user['sevak_id'] = 'KR' . str_pad($id_sequence, 3, '0', STR_PAD_LEFT);
                    $nirdeshak = $this->sevak_model->get_sevak_id($value['Nirdeshak']);
                    $shibir_user['nirdeshak'] = $nirdeshak['sevak_id']?? '';
                    $nirikshak = $this->sevak_model->get_sevak_id($value['Nirikshak']);
                    $shibir_user['nirikshak'] = $nirikshak['sevak_id']?? '';
                    $sanchalak = $this->sevak_model->get_sevak_id($value['Sanchalak']);
                    $shibir_user['sanchalak'] = $sanchalak['sevak_id']?? '';
                    $shibir_user['mandal_name'] = $value['Mandal'];
                    $this->import_karyakar_model->insert_users($shibir_user);
                    // $id_sequence++;
                    var_dump($shibir_user);
          }

          // $this->import_karyakar_model->insert_users($shibir_users);
          die;
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
