<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Person extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('person_model', 'person');
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('person/index');
    }

    public function ajax_list($negative = null)
    {

        $list = $this->person->get_datatables($negative);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $person) {
            $no++;
            $row = array();
            $row[] = $person->nome_cliente;
            $row[] = $person->nome_cidade;

            if ($negative != null) {
                //add html for action
                $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_person(' . "'" . $person->id . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> </a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Deletar" onclick="delete_person(' . "'" . $person->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i> </a>
            <a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Detalhes da dívida" onclick="showDebtors(' . "'" . $person->id . "'" . ')"><i class="glyphicon glyphicon-eye-open"></i> </a>';
            } else {
                $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_person(' . "'" . $person->id . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> </a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Deletar" onclick="delete_person(' . "'" . $person->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i> </a>';
            }
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->person->count_all(true),
            "recordsFiltered" => $this->person->count_filtered($negative),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }
    
    public function showDebtors()
    {
        $data = $this->person->getListInfoCustomer($this->input->post('idCliente'));
        echo json_encode($data);
    }

    public function getCitys()
    {
        $data = $this->person->getListCity();
        echo json_encode($data);
    }

    public function ajax_edit($id)
    {
        $data = $this->person->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add()
    {
        $data = array(
            'nome' => $this->input->post('nome'),
            'cidade_id' => $this->input->post('cidade_id'),
        );
        $insert = $this->person->save($data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        $data = array(
            'nome' => $this->input->post('nome'),
            'cidade_id' => $this->input->post('cidade_id'),
            'is_negativado' => $this->input->post('check') ? 1 : 0,
        );
        $this->person->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        $this->person->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

}
