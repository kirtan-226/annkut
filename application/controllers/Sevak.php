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
        $this->load->model('mandal_model');
        // $this->session = $this->session;
    }

    public function add_sevak() {
        // Make sure no output is sent before this point
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);
        $mandal = $this->sevak_model->get_sevak_mandal($data['id']);
        
        if(!isset($mandal) || empty($mandal)){
            $response['message'] = 'Entered Wrong Data';
            $response['status'] = 'false';
            return $response;
        }
        $mandal_short_form = strtoupper(substr($mandal['mandal'], 0, 2));

        do {
            // Generate a random 3-digit number
            $random_number = rand(100, 999);

            // Create the unique ID
            $sevak_id = $mandal_short_form . $random_number;

            $x = $this->sevak_model->check_id($sevak_id);
        } while(isset($x) || !empty($x));

        $sevak['sevak_id'] = $sevak_id;

        $sevak['filled_form'] = 0; 
        $sevak['is_changed'] = 'no'; 
        $sevak['name'] = $data['name'];
        $sevak['mandal'] = $mandal['mandal'];
        $sevak['sevak_target'] = $data['sevak_target'] ?? '';
        $sevak['filled_form'] = $data['filled_form'] ?? '';
        $sevak['phone_number'] = $data['phone_number'] ?? '';
        $sevak['password'] = 'pramukh@0712';
        $target = $this->mandal_model->get_mandal_target($mandal['mandal']);
        // var_dump($target);die;
        $target['mandal_target'] = intval($target['mandal_target']) + intval($sevak['sevak_target']);
        $target = $this->mandal_model->update_mandal($target);
        $this->sevak_model->add_sevak($sevak);
        $response['message'] = 'Sevak Added Successfully';
        $response['status'] = 'true';
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function assign_mandal(){
        $mandals = $this->sevak_model->get_all_mandal();
        $users = $this->sevak_model->get_all_users();
    
        foreach($users as &$user){
            foreach($mandals as $mandal){
                if(in_array($user['sevak_id'], [$mandal['sanchalak'], $mandal['nirikshak'], $mandal['nirdeshak']])){
                    $user['mandal'] = $mandal['mandal_name'];
                    break;
                }
            }
            $this->sevak_model->update_sevak($user);
        }
    }

    public function get_sevak($data = []) {
    // Ensure no output is sent before this point
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);
    
        $new_sevak_array = [];
        $name = '';
    
        $admin = $this->sevak_model->check_admin($data['id']);
        
        if ($admin['role'] == 7) {
            // If the user is an admin, retrieve all sevaks
            $sevaks = $this->sevak_model->get_all_sevak();
            $name = $this->sevak_model->get_sevak_name($data['id']);
            
            foreach ($sevaks as $sevak_item) {
                $role = $this->sevak_model->get_role($sevak_item['role']);
                // $new_sevak_array[] = [
                //     'sevak_id' => $sevak_item['sevak_id'],
                //     'role' => $role['role']
                // ];
                $sevak_item['role'] = $role['role'];
                $new_sevak_array[] = $sevak_item;
            }
        } else {
            // Non-admin users
            $mandals = $this->mandal_model->get_rolewise_mandal($data['id']);
            if(isset($mandals) && !empty($mandals)){
                foreach ($mandals as $mandal) {
                    $mandal_details = $this->mandal_model->get_mandal_details($mandal['mandal_name']);
                    
                    if (in_array($data['id'], [
                        $mandal_details['nirdeshak'], 
                        $mandal_details['nirikshak'], 
                        $mandal_details['sanchalak'], 
                        $mandal_details['sanyojak']
                    ])) {
                        // var_dump($mandal);die;
                        $sevak_list = $this->sevak_model->get_sevak($mandal['mandal_name']);
                        $name = $this->sevak_model->get_sevak_name($data['id']);
                        // var_dump($sevak_list);die;
                        foreach ($sevak_list as $sevak_item) {
                            $role = $this->sevak_model->get_role($sevak_item['role']);
                            // $new_sevak_array[] = [
                            //     'sevak_id' => $sevak_item['sevak_id'],
                            //     'role' => $role['role']
                            // ];
                            $sevak_item['role'] = $role['role'];
                            $new_sevak_array[] = $sevak_item;
                        }
                    }
                }
            }
            else{
                $sevak = $this->sevak_model->get_sevak_details($data['id']);
                $new_sevak_array = $sevak;
            }
        }
        // var_dump($new_sevak_array);die;
        $response = [
            'Sanchalak Name' => $name[0]['name'] ?? '',
            'sevak' => $new_sevak_array,
            'status' => 'true'
        ];
    
        header('Content-Type: application/json');
        echo json_encode($response);
    }


    public function edit_sevak($data =[]) {
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);
        $this->sevak_model->update_sevak($data);
    }

    public function delete_sevak($data =[]) {
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);
        $this->sevak_model->update_sevak($data);
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
