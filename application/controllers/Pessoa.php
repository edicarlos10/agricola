<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pessoa extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pessoa_model', 'pessoa');
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('pessoa/index');
    }

    public function ajax_list()
    {

        $list = $this->pessoa->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $pessoa) {
            $no++;
            $data_nascimento = new DateTime($pessoa->data_nascimento);

            $row = array();
            $row[] = $pessoa->id;
            $row[] = $pessoa->nome;
            $row[] = $pessoa->cpf;
            $row[] = $pessoa->data_nascimento != null ? $data_nascimento->format('d/m/Y') : NULL;
            $row[] = $pessoa->telefone;
            $row[] = $pessoa->endereco;

            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_pessoa(' . "'" . $pessoa->id . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> </a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Deletar" onclick="delete_pessoa(' . "'" . $pessoa->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i> </a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->pessoa->count_all(true),
            "recordsFiltered" => $this->pessoa->count_filtered(),
            "data" => $data,
        );
        // saida formato json
        echo json_encode($output);
    }

    public function ajax_edit($id)
    {
        $data = $this->pessoa->get_by_id($id);
        $data->data_nascimento = date('d/m/Y', strtotime($data->data_nascimento));
        echo json_encode($data);
    }

    public function ajax_add()
    {
        if (!empty($this->input->post('cpf')) && !is_numeric($this->input->post('cpf'))) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => 'Parâmetro inválido' . $this->input->post('cpf'))));
        }

        $exist = $this->pessoa->getNome($this->input->post('nome'));
        $nasc = DateTime::createFromFormat('d/m/Y', $this->input->post('data_nascimento'));
        if ($exist != null) {
            echo json_encode(array("status" => FALSE));
        } else {
            $data = array(
                'nome' => $this->input->post('nome'),
                'cpf' => !empty($this->input->post('cpf')) ? $this->input->post('cpf') : NULL,
                'data_nascimento' => !empty($this->input->post('data_nascimento')) ? $nasc->format('Y-m-d') : NULL,
                'telefone' => $this->input->post('telefone'),
                'endereco' => $this->input->post('endereco'),
                'comissao_perc' => $this->input->post('comissaoPo'),
                'is_colaborador' => $this->input->post('colaborador') == true ? 1 : 0,
            );
            $insert = $this->pessoa->save($data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_update()
    {
        if (!empty($this->input->post('cpf')) && !is_numeric($this->input->post('cpf'))) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => 'Parâmetro inválido' . $this->input->post('cpf'))));
        }

        $nasc = DateTime::createFromFormat('d/m/Y', $this->input->post('data_nascimento'));
        if (empty($this->input->post('nome'))) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => 'Campo nome está vazio' . $this->input->post('nome'))));
        } else {
            $data = array(
                'nome' => $this->input->post('nome'),
                'cpf' => !empty($this->input->post('cpf')) ? $this->input->post('cpf') : NULL,
                'data_nascimento' => !empty($this->input->post('data_nascimento')) ? $nasc->format('Y-m-d') : NULL,
                'telefone' => $this->input->post('telefone'),
                'endereco' => $this->input->post('endereco'),
                'comissao_perc' => $this->input->post('comissaoPo'),
                'is_colaborador' => $this->input->post('colaborador') == true ? 1 : 0,
            );
            $this->pessoa->update(array('id' => $this->input->post('id')), $data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_delete($id)
    {
        if (is_numeric($id)) {
            $this->pessoa->delete_by_id($id);
            echo json_encode(array("status" => TRUE));
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => 'Parâmetro inválido' . $id)));
        }
    }

    public function get_info()
    {
        $data = $this->pessoa->getInformacao();
        echo json_encode($data);
    }

    public function visto()
    {
        $data = array(
            'visto' => '1'
        );
        $this->pessoa->marcaVisto(array('visto' => '0'), $data);
        echo json_encode(array("status" => TRUE));
    }

}
