<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loginmodel extends CI_Model{

  public function __construct()
  {
    parent::__construct();
    $this->load->database();

  }

  public function is_valid($nama){
    $this->db->select('*');
    $this->db->from('admin');
    $this->db->where('nama',$nama);
    $query = $this->db->get();
    return $query->row();
  }

  public function is_valid_num($nama){
    $this->db->select('*');
    $this->db->from('admin');
    $this->db->where('nama',$nama);
    $query = $this->db->get();
    return $query->num_rows();
  }

}
