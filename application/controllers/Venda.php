<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Venda extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('venda_model', 'venda');
        $this->load->model('pessoa_model', 'pessoa');
        $this->load->model('comissao_model', 'comissao');
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('venda/index');
    }

    public function getPersons()
    {
        $data = $this->venda->getListPersons();
        echo json_encode($data);
    }

    public function getProduct()
    {
        $data = $this->venda->getListProducts();
        echo json_encode($data);
    }

    public function ajax_list()
    {
        $this->load->helper('url');
        $list = $this->venda->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $venda) {
            $no++;
            $dataCadastro = new DateTime($venda->data);

            $row = array();
            $row[] = $venda->id;
            $row[] = $dataCadastro->format('d/m/Y');
            $row[] = $venda->nome_pessoa;
            $row[] = number_format($venda->vlr_total, 2, ',', '.');
            $row[] = $venda->observacao;

            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_venda(' . "'" . $venda->id . "'" . ')"><i class="glyphicon glyphicon-pencil"></i> </a>
                  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Deletar" onclick="delete_venda(' . "'" . $venda->id . "'" . ')"><i class="glyphicon glyphicon-trash"></i> </a>
                  <a class="btn btn-sm btn-info" href="' . site_url('venda/ajax_print/') . $venda->id . '" title="Imprimir"><i class="glyphicon glyphicon-print"></i> </a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->venda->count_all(true),
            "recordsFiltered" => $this->venda->count_filtered(),
            "data" => $data,
        );
        // saida formato json
        echo json_encode($output);
    }

    public function ajax_edit($id)
    {
        if ($id != null && is_numeric($id)) {
            $data = $this->venda->get_by_id($id);
            echo json_encode($data);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => "Parâmetro inválido" . $id)));
        }
    }

    // valida data
    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    // monta ptodutos
    private function _getProdutosNoPost()
    {
        $_arrayProdutos = NULL;
        for ($i = 0; $i < sizeof($this->input->post('produto_id')); $i++) {
            $_produto = [];
            $_produto['produto_id'] = $this->input->post('produto_id')[$i];
            $_produto['vendedor_id'] = $this->input->post('vendedor_id')[$i];
            $_produto['qtd'] = $this->input->post('qtd')[$i];
            $_produto['vlr'] = $this->input->post('vlr')[$i];
            $_produto = (object) $_produto;
            $_arrayProdutos[$i] = $_produto;
        }
        return $_arrayProdutos;
    }

    private function _geraComissao($id_venda, $arrayComissao)
    {
        $this->comissao->delete_by_id_venda($id_venda);

        for ($i = 0; $i < sizeof($arrayComissao); $i++) {
            $this->comissao->save($arrayComissao[$i]);
        }
    }

    public function ajax_add()
    {
        $_produtos = $this->_getProdutosNoPost();

        if (is_numeric($this->input->post('id_pessoa')) && $this->input->post('vlr_total') != NULL &&
                (is_float($this->input->post('vlr_total')) || is_numeric($this->input->post('vlr_total')))) {

            $vendaExistente = $this->venda->get_by_id($this->input->post('id'));
            $pessoaExistente = $this->venda->getPessoa($this->input->post('id_pessoa'));

            if ($vendaExistente != null || $pessoaExistente == null) {
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('status' => 'ERROR', 'Venda' => $vendaExistente, 'pessoa' => $pessoaExistente)));
            } else {
                $vcto = DateTime::createFromFormat('d/m/Y', $this->input->post('data_vencimento'));
                $data = array(
                    'pessoa_id' => $this->input->post('id_pessoa'),
                    'vlr_total' => $this->input->post('vlr_total'),
                    'observacao' => $this->input->post('observacao'),
                );
                $insert = $this->venda->save($data);

                // insere itens se possuir
                if ($_produtos != NULL && count($_produtos) > 0) {
                    
                    /* para comissão */
                    $_arrayComissao = NULL;
                    $_venda = $this->venda->get_by_id($insert);

                    for ($i = 0; $i < count($_produtos); $i++) {
                        $dataItem = array(
                            'venda_id' => $insert,
                            'produto_id' => $_produtos[$i]->produto_id,
                            'vendedor_id' => $_produtos[$i]->vendedor_id,
                            'qtde' => $_produtos[$i]->qtd,
                            'vlr' => $_produtos[$i]->vlr,
                        );
                        $insertItem = $this->venda->saveItem($dataItem);

                        /* monta array de comissão */
                        if ($insert && $insertItem > 0) {
                            $_pessoa = $this->pessoa->get_by_id($_produtos[$i]->vendedor_id);
                            $comissao = [];

                            $comissao['pessoa_id'] = $_pessoa->id;
                            $comissao['venda_id'] = $_venda->id;
                            $comissao['qtde'] = $_produtos[$i]->qtd;
                            $comissao['vlr'] = $_produtos[$i]->vlr;
                            $comissao['vlr_total'] = $_produtos[$i]->vlr * $_produtos[$i]->qtd;
                            $comissao['comissao_perc'] = $_pessoa->comissao_perc;
                            $comissao['comissao_vlr'] = ($_pessoa->comissao_perc / 100) * ($_produtos[$i]->vlr * $_produtos[$i]->qtd);
                            $comissao['observacao'] = $_venda->observacao;
                            $_arrayComissao[$i] = $comissao;
                        }
                    }
                    $this->_geraComissao($insert, $_arrayComissao);
                }

                echo json_encode(array("status" => TRUE));
            }
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => 'Post Inválido')));
        }
    }

    public function ajax_update()
    {
        $_produtos = $this->_getProdutosNoPost();
        if ($this->input->post('id') != null && is_numeric($this->input->post('id')) && is_numeric($this->input->post('id_pessoa')) &&
                $this->input->post('vlr_total') != NULL && (is_float($this->input->post('vlr_total')) || is_numeric($this->input->post('vlr_total')))) {

            $vcto = DateTime::createFromFormat('d/m/Y', $this->input->post('data_vencimento'));
            $data = array(
                'pessoa_id' => $this->input->post('id_pessoa'),
                'vlr_total' => $this->input->post('vlr_total'),
                'observacao' => $this->input->post('observacao'),
            );

            $this->venda->update(array('id' => $this->input->post('id')), $data);
            $this->venda->delete_itens($this->input->post('id'));

            // insere itens se possuir
            if ($_produtos != NULL && count($_produtos) > 0) {
                
                /* para comissão */
                $_arrayComissao = NULL;
                $_venda = $this->venda->get_by_id($this->input->post('id'));

                for ($i = 0; $i < count($_produtos); $i++) {
                    $dataItem = array(
                        'venda_id' => $this->input->post('id'),
                        'produto_id' => $_produtos[$i]->produto_id,
                        'vendedor_id' => $_produtos[$i]->vendedor_id,
                        'qtde' => $_produtos[$i]->qtd,
                        'vlr' => $_produtos[$i]->vlr,
                    );
                    $_updateItem = $this->venda->saveItem($dataItem);

                    /* Monta array de comissão */
                    if ($_updateItem > 0) {
                        $_pessoa = $this->pessoa->get_by_id($_produtos[$i]->vendedor_id);
                        $comissao = [];

                        $comissao['pessoa_id'] = $_pessoa->id;
                        $comissao['venda_id'] = $_venda->id;
                        $comissao['qtde'] = $_produtos[$i]->qtd;
                        $comissao['vlr'] = $_produtos[$i]->vlr;
                        $comissao['vlr_total'] = $_produtos[$i]->vlr * $_produtos[$i]->qtd;
                        $comissao['comissao_perc'] = $_pessoa->comissao_perc;
                        $comissao['comissao_vlr'] = ($_pessoa->comissao_perc / 100) * ($_produtos[$i]->vlr * $_produtos[$i]->qtd);
                        $comissao['observacao'] = $_venda->observacao;
                        $_arrayComissao[$i] = $comissao;
                    }
                }
                $this->_geraComissao($this->input->post('id'), $_arrayComissao);
            }

            echo json_encode(array("status" => TRUE));
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => 'Post Inválido')));
        }
    }

    //busca itens da venda
    public function getItens($id)
    {
        if ($id != null && is_numeric($id)) {
            $itens = $this->venda->getItensVenda($id);
            echo json_encode($itens);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => "Parâmetro inválido" . $id)));
        }
    }

    public function ajax_delete($id)
    {
        if (is_numeric($id)) {
            $this->venda->delete_itens($id);
            $this->venda->delete_by_id($id);
            echo json_encode(array("status" => TRUE));
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('status' => 'ERROR', 'mensagem' => "Parâmetro inválido" . $id)));
        }
    }

    // imprime
    public function ajax_print($id)
    {
        // carrega fpdf
        $this->load->library('pdf');

        // Instanciação de classe herdada
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times', '', 12);

        $venda = $this->venda->getAllInformations($id);
        if ($venda != null) {
            $emissao = new DateTime($venda->data);

            $itens = $this->venda->getItensVenda($venda->id);

            //Cliente
            $pdf->Cell(0, 10, 'Cod. #' . $venda->id, 0, 1, 'R');
            $pdf->Line(10, 45, 200, 45);
            $pdf->SetXY(10, 30);
            $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', html_entity_decode('Emissão: ')) . $emissao->format('d/m/Y'));
            $pdf->SetXY(50, 30);
            $pdf->Cell(0, 10, 'Cliente: ' . iconv('UTF-8', 'windows-1252', html_entity_decode($venda->nome_pessoa)));
            $pdf->SetXY(10, 36);
            $pdf->Cell(0, 10, 'CPF: ' . $venda->cpf);
            $pdf->Ln(12);

            //Produtos
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 6, 'ITENS ', 0, 0, 'C');
            $pdf->Ln(6);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(65, 5, 'PRODUTO', 'LRBT', 0, 'C');
            $pdf->Cell(60, 5, 'QUANTIDADE', 'LRBT', 0, 'C');
            $pdf->Cell(65, 5, 'VALOR', 'LRBT', 0, 'C');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', '', 12);
            if ($itens != NULL) {
                for ($i = 0; $i < sizeof($itens); $i++) {
                    $pdf->Cell(65, 5, iconv('UTF-8', 'windows-1252', html_entity_decode($itens[$i]->nome_produto)), 'LRBT', 0, 'L');
                    $pdf->Cell(60, 5, $itens[$i]->qtde, 'RBT', 0, 'C');
                    $pdf->Cell(65, 5, number_format($itens[$i]->vlr, 1, ',', '.') . $i, 'RBT', 1, 'C');
                }
            }

            // Total
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 20, 'Total: ' . number_format($venda->vlr_total, 2, ',', '.'), 0, 0, 'R');

            $pdf->Ln(15);
            $text = str_repeat(iconv('UTF-8', 'windows-1252', html_entity_decode($venda->observacao)), 1);
            $pdf->WordWrap($text, 500);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Write(5, iconv(mb_detect_encoding('Observação'), 'windows-1252', 'Observação') . ": ");
            $pdf->SetFont('Arial', '', 12);
            $pdf->Write(5, $text);

            // Desenha
            $pdf->Output();
        } else {
            //Informação se não existir venda
            $pdf->SetFont('Arial', '', 15);
            $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', html_entity_decode('Não foi possível buscar os dados desse venda.')), 0, 1, 'C');

            $pdf->Output();
        }
    }

}
