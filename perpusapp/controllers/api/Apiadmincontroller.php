<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/Restdata.php';

class Apiadmincontroller extends Restdata{

  public function __construct(){
    parent::__construct();
    $this->load->model('mymodel');
  }

  //method untuk melakukan penambahan admin (post)
  function admin_post(){
    $data = [
      'nama'=>$this->post('nama'),
      'password'=>password_hash($this->post('password'),PASSWORD_DEFAULT)
    ];

    $this->form_validation->set_rules('nama','Nama','trim|max_length[50]|is_unique[admin.nama]');
    $this->form_validation->set_rules('password','Password','trim|min_length[8]');

    if ($this->form_validation->run()==false) {

      $this->badreq($this->validation_errors());

    }else {
      if ($this->mymodel->insertadmin($data)) {

        $this->response([
          'message'=>'Admin Berhasil Di Buat'
        ],HTTP_OK);

      }else {

        $this->response([
          'message'=>'Admin Gagal Di Buat'
        ],HTTP_BAD_REQUEST);

      }
    }
  }




}
