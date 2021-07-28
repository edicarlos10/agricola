<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Comissao_model extends CI_Model
{

    var $table = 'comissao';
    var $column_order = array('pessoa.nome, COUNT( comissao.venda_id ) qtde, SUM( comissao.comissao_vlr ) vlr, SUM( comissao.comissao_vlr ) / COUNT( comissao.venda_id ) ticket');
    var $column_search = array('pessoa.nome');
    var $order = array('vlr' => 'desc');

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query()
    {
        $this->db->select('pessoa.nome, COUNT( comissao.venda_id ) qtde, SUM( comissao.comissao_vlr ) vlr, SUM( comissao.comissao_vlr ) / COUNT( comissao.venda_id ) ticket');
        $this->db->from($this->table);
        $this->db->join('pessoa', 'pessoa.id = comissao.pessoa_id', 'left');
        $this->db->group_by('comissao.pessoa_id');
        $this->db->order_by('vlr', 'desc', false);

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
    
    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $query = $this->db->get();

        return $query->row();
    }
    
    public function save($data)
    {
        $this->db->insert($this->table, $data);
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
    
    public function delete_by_id_venda($idVenda)
    {
        $this->db->where('venda_id', $idVenda);
        $this->db->delete($this->table);
    }
    
    public function getComissao($inicio, $fim)
    {
        $_select = 'comissao.pessoa_id, comissao.data, pessoa.nome, COUNT( comissao.venda_id ) qtde, SUM( comissao.comissao_vlr ) vlr, SUM( comissao.comissao_vlr ) / COUNT( comissao.venda_id ) ticket';

        $this->db->select($_select);
        $this->db->join('pessoa', 'pessoa.id = comissao.pessoa_id');
        $this->db->group_by('comissao.pessoa_id');
        $this->db->order_by('vlr', 'desc', false);

        $this->db->from($this->table);
        $this->db->where('comissao.data >= ', date('Y-m-d', strtotime(substr(implode("-", array_reverse(explode("/", $inicio))), 0, 10))) . ' 23:59:59');
        $this->db->where('comissao.data <= ', date('Y-m-d', strtotime(substr(implode("-", array_reverse(explode("/", $fim))), 0, 10))) . ' 23:59:59');
        $query = $this->db->get();

        return $query->result();
    }

}
