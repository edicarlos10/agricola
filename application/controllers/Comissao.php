<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Comissao extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('comissao_model', 'comissao');
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('comissao/index');
    }

    public function ajax_list()
    {
        $startOfDay = $this->input->get('iniciodata');
        $endOfDay = $this->input->get('finaldata');
        $list = $this->comissao->getComissao($startOfDay, $endOfDay);
        $data = array();

        if ($startOfDay != null && $endOfDay != null) {
            foreach ($list as $comissao) {
                $row = array();
                $row[] = $comissao->nome;
                $row[] = number_format($comissao->vlr, 2, ',', '.');
                $row[] = $comissao->qtde;
                $row[] = number_format($comissao->ticket, 2, ',', '.');
                $data[] = $row;
            }
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $startOfDay != null && $endOfDay != null ? sizeof($list) : 0,
            "recordsFiltered" => $startOfDay != null && $endOfDay != null ? $this->comissao->count_filtered() : 0,
            "data" => $data,
        );

        // saida formato json
         echo json_encode($output);
    }

}
