<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Produto extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('produto_model', 'produto');
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('produto/index');
    }

    public function ajax_list()
    {

        $list = $this->produto->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $produto) {
            $no++;
            $row = array();
            $row[] = $produto->id;
            $row[] = $produto->nome;
            $row[] = $produto->vlr;
            $row[] = $produto->observacao;

            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_produto(' . "'" . $produto->id . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> </a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Deletar" onclick="delete_produto(' . "'" . $produto->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i> </a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->produto->count_all(true),
            "recordsFiltered" => $this->produto->count_filtered(),
            "data" => $data,
        );
        // saida formato json
        echo json_encode($output);
    }

    public function ajax_edit($id)
    {
        $data = $this->produto->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add()
    {
        if ($this->input->post('nome') != NULL && $this->input->post('vlr') != NULL && (is_float($this->input->post('vlr')) || is_numeric($this->input->post('vlr')))) {
            $data = array(
                'nome' => $this->input->post('nome'),
                'vlr' => $this->input->post('vlr'),
                'observacao' => $this->input->post('observacao'),
            );
            $insert = $this->produto->save($data);
            echo json_encode(array("status" => TRUE));
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => 'Post Inválido')));
        }
    }

    public function ajax_update()
    {
        if ($this->input->post('nome') != NULL && $this->input->post('vlr') != NULL && (is_float($this->input->post('vlr')) || is_numeric($this->input->post('vlr')))) {

            $produto = $this->produto->get_by_id($this->input->post('id'));
            if ($produto != null && strcmp($produto->nome, $this->input->post('nome')) == 0 && abs(($produto->vlr - $this->input->post('vlr')) / $this->input->post('vlr')) < 0.00001 && strcmp($produto->observacao, $this->input->post('observacao')) == 0) {

                echo json_encode(array("status" => false));
            } else {
                $data = array(
                    'nome' => $this->input->post('nome'),
                    'vlr' => $this->input->post('vlr'),
                    'observacao' => $this->input->post('observacao'),
                );
                $update = $this->produto->update(array('id' => $this->input->post('id')), $data);
                if ($update != null) {
                    echo json_encode(array("status" => TRUE));
                } else {
                    header('HTTP/1.1 500 Internal Server Error');
                    header('Content-Type: application/json; charset=UTF-8');
                    die(json_encode(array('status' => 'ERROR', 'mensagem' => 'Update ' . $update)));
                }
            }
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => 'Post inválido')));
        }
    }

    public function ajax_delete($id)
    {
        if (is_numeric($id)) {
            $this->produto->delete_by_id($id);
            echo json_encode(array("status" => TRUE));
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => 'Parâmetro inválido' . $id)));
        }
    }

}
