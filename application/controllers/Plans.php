<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Plans extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('plans_model', 'plan');
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('plans/index');
    }

    public function ajax_list()
    {
        $list = $this->plan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $plan) {
            $no++;
            $row = array();
            $row[] = $plan->nome;
            $row[] = $plan->vlr;

            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_plan(' . "'" . $plan->id . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> </a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Deletar" onclick="delete_plan(' . "'" . $plan->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i> </a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->plan->count_all(),
            "recordsFiltered" => $this->plan->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function ajax_edit($id)
    {
        $data = $this->plan->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add()
    {
        $data = array(
            'nome' => $this->input->post('nome'),
            'vlr' => $this->input->post('vlr'),
        );
        $insert = $this->plan->save($data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        $data = array(
            'nome' => $this->input->post('nome'),
            'vlr' => $this->input->post('vlr'),
        );
        $this->plan->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        $this->plan->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

}
