<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/Restdata.php';

class Apipinjamcontroller extends Restdata{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('mymodel');
    //mengecek token pada class Restdata, di mana jika token invalid maka akan melakukan exit
    //$this->cektoken();
  }

  function pinjam_post()
  {
    $allid_buku     = implode(',',array_column($this->mymodel->selectidbuku(),'id'));
    $allid_anggota  = implode(',',array_column($this->mymodel->selectidanggota(),'id'));

    $data = [
      'id_anggota'=>$this->post('id_anggota',TRUE),
      'id_buku'=>$this->post('id_buku',TRUE),
      'tgl_pinjam'=>date("Y-m-d"),
      'denda'=>$this->post('denda',TRUE)
    ];

    $this->form_validation->set_rules('id_anggota','ID ANGGOTA','trim|required|in_list['.$allid_anggota.']');
    $this->form_validation->set_rules('id_buku','ID BUKU','trim|required|in_list['.$allid_buku.']');
    $this->form_validation->set_rules('denda','DENDA','trim');

    $this->form_validation->set_message('in_list','salah memasukan %s');

    if ($this->form_validation->run()==false) {
      //mengembalikan respon bad request dengan validasi error
      $this->badreq($this->validation_errors());
    }else {
      if ($this->mymodel->insertpinjam($data)) {
        $this->response($data,Restdata::HTTP_CREATED);
      }
    }

  }

  function pinjam_get(){
    $idpinjam = (int) $this->get('id',TRUE);
    //jika user menambahkan id maka akan di select berdasarkan id, jika tidak maka akan di select seluruhnya
    $data = ($idpinjam) ? $this->mymodel->selectpinjamwhere($idpinjam) : $this->mymodel->selectpinjam();


    if ($data) {
      //mengembalikan respon http ok 200 dengan data dari select di atas
      $this->response($data,Restdata::HTTP_OK);
    }else {
      $this->notfound('Peminjaman Tidak Di Temukan');

    }
  }

  function pinjam_put(){
    $id = (int) $this->get('id',TRUE);

    //mendapatkan data json yang kemudian dilakukan json decode
    $data = json_decode(file_get_contents('php://input'),TRUE);

    $allid_buku     = implode(',',array_column($this->mymodel->selectidbuku(),'id'));
    $allid_anggota  = implode(',',array_column($this->mymodel->selectidanggota(),'id'));


    $this->form_validation->set_data($data);
    $this->form_validation->set_rules('id_anggota','ID ANGGOTA','trim|in_list['.$allid_anggota.']');
    $this->form_validation->set_rules('id_buku','ID BUKU','trim|in_list['.$allid_buku.']');
    $this->form_validation->set_rules('denda','DENDA','trim');
    $this->form_validation->set_message('in_list','salah memasukan %s');

    if ($this->form_validation->run() == FALSE) {
      //mengembalikan respon bad request dengan validasi error
      $this->badreq($this->validation_errors());
    }else {
      if ($this->mymodel->updatepinjam($id,$data)) {
        $this->response($data,Restdata::HTTP_OK);
      }else {
        $this->badreq('gagal update Peminjaman');
      }
    }

  }

  function pinjam_delete(){
    $id = (int) $this->get('id',TRUE);

    if ($this->mymodel->deletepinjam($id)) {
      $this->response([
        'id'=>$id,
        'status'=>'deleted'
      ],Restdata::HTTP_OK);
    }else {
        $this->badreq('Failed To Delete ID '.$id);
    }
  }

}
