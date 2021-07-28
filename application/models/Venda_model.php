<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Venda_model extends CI_Model
{

    var $table = 'venda';
    var $column_order = array('id', 'data', 'pessoa.nome', 'vlr_total', 'observacao');
    var $column_search = array('pessoa.nome', 'vlr_total');
    var $order = array('data' => 'desc');

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query()
    {
        $this->db->select('venda.id, venda.data, venda.vlr_total, venda.observacao, pessoa.id id_pessoa, pessoa.nome nome_pessoa');
        $this->db->from($this->table);
        $this->db->join('pessoa', 'pessoa.id = venda.pessoa_id', 'left');

        $i = 0;

        foreach ($this->column_search as $item) { // loop na coluna 
            if ($_POST['search']['value']) { 
                if ($i === 0) { // primeiro loop
                    $this->db->group_start(); // bloco
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //ultimo loop
                    $this->db->group_end(); //fecha bloco
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function getItensVenda($idVenda)
    {
        $this->db->select('item.id, item.venda_id, item.produto_id, item.qtde, item.vlr, item.vendedor_id, produto.nome nome_produto');
        $this->db->from('item');
        $this->db->join('produto', 'produto.id = item.produto_id', 'left');
        $this->db->where('venda_id', $idVenda);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    public function getListProducts()
    {
        $this->db->select('produto.id, produto.nome, produto.vlr');
        $this->db->from('produto');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    public function getListPersons()
    {
        $this->db->select('pessoa.id, pessoa.nome, pessoa.cpf, pessoa.is_colaborador, pessoa.comissao_perc');
        $this->db->from('pessoa');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    public function getPessoa($idPessoa)
    {
        $this->db->from("pessoa");
        $this->db->where('id', $idPessoa);
        $query = $this->db->get();

        return $query->row();
    }

    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function getAllInformations($id)
    {
        $this->db->select('venda.id, venda.data, venda.vlr_total, venda.observacao, pessoa.id id_pessoa, pessoa.nome nome_pessoa, pessoa.cpf cpf');
        $this->db->from($this->table);
        $this->db->join('pessoa', 'pessoa.id = venda.pessoa_id', 'left');
        $this->db->where('venda.id', $id);

        $query = $this->db->get();

        return $query->row();
    }

    public function save($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function saveItem($data)
    {
        $this->db->insert('item', $data);
        return $this->db->insert_id();
    }
    
     public function saveComissao($data)
    {
        $this->db->insert('comissao', $data);
        return $this->db->insert_id();
    }

    public function update($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }

    public function delete_by_id($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
    }

    public function delete_itens($idVenda)
    {
        $this->db->where('venda_id', $idVenda);
        $this->db->delete("item");
    }

}
