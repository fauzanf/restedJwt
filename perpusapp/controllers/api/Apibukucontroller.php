<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/Restdata.php';

class Apibukucontroller extends Restdata{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('mymodel');
    //mengecek token pada class Restdata, di mana jika token invalid maka akan melakukan exit
    $this->cektoken();
  }

  function buku_get()
  {
    $id = (int) $this->get('id',TRUE);
    //jika user menambahkan id maka akan di select berdasarkan id, jika tidak maka akan di select seluruhnya
    $data = ($id) ? $this->mymodel->selectbukuwhere($id) : $this->mymodel->selectbuku();


    if ($data) {
      //mengembalikan respon http ok 200 dengan data dari select di atas
      $this->response($data,Restdata::HTTP_OK);
    }else {
      $this->notfound('Buku Tidak Di Temukan');

    }
  }

  function buku_post(){
    $data = [
      'judul'=>$this->post('judul',TRUE),
      'pengarang'=>$this->post('pengarang',TRUE),
      'thn_terbit'=>$this->post('thn_terbit',TRUE)
    ];

    $this->form_validation->set_rules('judul','JUDUL','trim|max_length[50]|required');
    $this->form_validation->set_rules('pengarang','PENGARANG','trim|max_length[30]|required');
    $this->form_validation->set_rules('thn_terbit','TAHUN TERBIT','trim|required|numeric');

    if ($this->form_validation->run()==false) {
      //mengembalikan respon bad request dengan validasi error
      $this->badreq($this->validation_errors());
    }else {
      //jika berhasil di masukan maka akan di respon kembali sesuai dengan data yang di masukan
      if ($this->mymodel->insertbuku($data)) {
        $this->response($data,Restdata::HTTP_CREATED);
      }
    }
  }


  function buku_put(){

    $id = (int) $this->get('id',TRUE);

    //mendapatkan data json yang kemudian dilakukan json decode
    $data = json_decode(file_get_contents('php://input'),TRUE);

    $this->form_validation->set_data($data);
    $this->form_validation->set_rules('judul','JUDUL','trim|max_length[50]');
    $this->form_validation->set_rules('pengarang','PENGARANG','trim|max_length[30]');
    $this->form_validation->set_rules('thn_terbit','TAHUN TERBIT','trim|numeric');

    if (!$this->form_validation->run()) {
      //mengembalikan respon bad request dengan validasi error
      $this->badreq($this->validation_errors());
    }else {
      if ($this->mymodel->updatebuku($id,$data)) {
        $this->response($data,Restdata::HTTP_OK);
      }else {
        $this->badreq('gagal update buku');
      }
    }
    

  }


  function buku_delete(){

    $id = (int) $this->get('id',TRUE);

    if ($this->mymodel->deletebuku($id)) {
      $this->response([
        'id'=>$id,
        'status'=>'deleted'
      ],Restdata::HTTP_OK);
    }else {
        $this->badreq('Failed To Delete ID '.$id);
    }

  }

}
