<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class City extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('city_model', 'city');
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('city/index');
    }

    public function ajax_list()
    {
        $list = $this->city->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $city) {
            $no++;
            $row = array();
            $row[] = $city->nome;

            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_city(' . "'" . $city->id . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> </a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Deletar" onclick="delete_city(' . "'" . $city->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i> </a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->city->count_all(),
            "recordsFiltered" => $this->city->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function ajax_edit($id)
    {
        $data = $this->city->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add()
    {
        $data = array(
            'nome' => $this->input->post('nome'),
        );
        $insert = $this->city->save($data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        $data = array(
            'nome' => $this->input->post('nome'),
        );
        $this->city->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        $this->city->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

}
