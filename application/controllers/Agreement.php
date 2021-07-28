<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Agreement extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('agreement_model', 'agreement');
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('agreement/index');
    }

    public function ajax_list()
    {
        $list = $this->agreement->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $agreement) {
            $no++;
            $row = array();
            $row[] = $agreement->nome_cidade;
            $row[] = $agreement->nome_cliente;
            $row[] = $agreement->dia_plano;
            $row[] = $agreement->nro_boleto;
            $row[] = $agreement->vlr_lancamento;
            $row[] = $agreement->nome_plano . " -> R$ " . $agreement->vlr_plano;

            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_agreement(' . "'" . $agreement->id_lancamento . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> </a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Deletar" onclick="delete_agreement(' . "'" . $agreement->id_lancamento . "'" . ')"><i class="glyphicon glyphicon-trash"></i> </a>';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->agreement->count_all(true),
            "recordsFiltered" => $this->agreement->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function getPersons()
    {
        $data = $this->agreement->getListPersons();
        echo json_encode($data);
    }

    public function getPlans()
    {
        $data = $this->agreement->getListPlans();
        echo json_encode($data);
    }

    public function ajax_edit($id)
    {
        $data = $this->agreement->get_by_id($id);
        $data->cliente_plano = $this->agreement->getClientePlano($data->cliente_plano_id);
        $data->vcto = date('d/m/Y', strtotime($data->vcto));
        echo json_encode($data);
    }

    public function ajax_add()
    {
        // verifica se o boleto ja foi add
        $_boleto = $this->agreement->get_by_boleto($this->input->post('nro_boleto'));
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
        $insertClientesPlanos = $this->agreement->saveClientePlano($clientes_planos);

        //Insere na tabela de lancamentos
        $vcto = DateTime::createFromFormat('d/m/Y', $this->input->post('vcto'));
        $lancamento = array(
            'cliente_plano_id' => $insertClientesPlanos,
            'vlr' => $this->input->post('vlr'),
            'vcto' => $vcto->format('Y-m-d'),
        );
        $insert = $this->agreement->save($lancamento);

        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        // verifica se o boleto ja foi add
        $registro = $this->agreement->get_by_id($this->input->post('id'));
        if ($registro != null && $registro->nro_boleto != $this->input->post('nro_boleto')) {
            $boleto = $this->agreement->get_by_boleto($this->input->post('nro_boleto'));
            if ($boleto != null) {
                echo json_encode(array("status" => FALSE));
            }
        }

        // insere na tabela clientes_planos
        $clientes_planos = array(
            'id_cliente' => $this->input->post('person'),
            'id_plano' => $this->input->post('id_plano'),
            'nro_boleto' => $this->input->post('nro_boleto'),
            'dia_plano' => $this->input->post('dia_plano'),
        );
        $this->agreement->updateClientePlano(array('id' => $this->input->post('id_cliente_plano')), $clientes_planos);

        // insere na tabela de lancamentos
        $vcto = DateTime::createFromFormat('d/m/Y', $this->input->post('vcto'));
        $lancamento = array(
            'vlr' => $this->input->post('vlr'),
            'vcto' => $vcto->format('Y-m-d'),
        );
        $this->agreement->update(array('id' => $this->input->post('id_lancamento')), $lancamento);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)
    {
        $this->agreement->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

}
