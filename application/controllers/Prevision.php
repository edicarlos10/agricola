<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Prevision extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('prevision_model', 'prevision');
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('prevision/index');
    }

    public function ajax_list()
    {
        $list = $this->prevision->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $prevision) {
            $no++;
            $row = array();
            $row[] = $prevision->nome_cidade;
            $row[] = $prevision->nome_cliente;
            $row[] = $prevision->dia_plano;
            $row[] = $prevision->nro_boleto;
            $row[] = $prevision->vlr_lancamento;
            $row[] = $prevision->vlr;
            $row[] = $prevision->vlr;
            $row[] = $prevision->vlr;
            $row[] = $prevision->vlr;
            $row[] = $prevision->vlr;

            //add html for action
            $row[] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Enviar para lista devedores" onclick="negativeCustomer(' . "'" . $prevision->id_cliente . "'" . ', ' . "'" . $prevision->nro_boleto . "'" . ')"><i class="glyphicon glyphicon-share-alt"></i> </a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->prevision->count_all(true),
            "recordsFiltered" => $this->prevision->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function negativeCustomer()
    {
        $data = array(
            'is_negativado' => 1,
        );
        $this->prevision->negativise("clientes", array('id' => $this->input->post('idCliente')), $data);

        $data2 = array(
            'is_ativo' => 0,
        );
        $this->prevision->negativise("clientes_planos", array('id_cliente' => $this->input->post('idCliente'), 'nro_boleto' => $this->input->post('nroBoleto')), $data2);
        echo json_encode(array("status" => TRUE));
    }

    public function getPersons()
    {
        $data = $this->prevision->getListPersons();
        echo json_encode($data);
    }

    public function getPlans()
    {
        $data = $this->prevision->getListPlans();
        echo json_encode($data);
    }

    public function ajax_edit($id)
    {
        $data = $this->prevision->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add()
    {
        // verifica se o boleto ja foi add
        $_boleto = $this->prevision->get_by_boleto($this->input->post('nro_boleto'));
        if ($_boleto != null) {
            echo json_encode(array("status" => FALSE));
        }

        // insere na tabela clientes_planos
        $clientes_planos = array(
            'id_cliente' => $this->input->post('person'),
            'id_plano' => $this->input->post('id_plano'),
            'nro_boleto' => $this->input->post('nro_boleto'),
            'dia_plano' => $this->input->post('dia_plano'),
        );
        $insertClientesPlanos = $this->prevision->saveClientePlano($clientes_planos);

        //Insere na tabela de lancamentos
        $vcto = DateTime::createFromFormat('d/m/Y', $this->input->post('vcto'));
        $lancamento = array(
            'cliente_plano_id' => $insertClientesPlanos,
            'vlr' => $this->input->post('vlr'),
            'vcto' => $vcto->format('Y-m-d'),
        );
        $insert = $this->prevision->save($lancamento);

        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        $data = array(
            'id_cliente' => $this->input->post('id_cliente'),
            'id_plano' => $this->input->post('id_plano'),
            'nro_boleto' => $this->input->post('nro_boleto'),
            'dia_plano' => $this->input->post('dia_plano'),
        );
        $this->prevision->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        $this->prevision->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

}
