<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Person_model extends CI_Model
{

    var $table = 'clientes';
    var $column_order = array('clientes.nome', 'cidades.nome'); //set column field database for datatable orderable
    var $column_search = array('clientes.nome', 'cidades.nome'); //set column field database for datatable searchable just firstname , lastname , address are searchable
    var $order = array('clientes.nome' => 'asc'); // default order 

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query($negative = null)
    {
        $this->db->select('clientes.id, clientes.nome nome_cliente, clientes.cidade_id, clientes.is_negativado, cidades.id id_cidade, cidades.nome nome_cidade');
        $this->db->from($this->table);
        $this->db->join('cidades', 'cidades.id = clientes.cidade_id', 'left');
        if ($negative != null) {
            $this->db->where('clientes.is_negativado', 1);
        }

        $i = 0;

        foreach ($this->column_search as $item) { // loop column 
            if ($_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($negative = null)
    {
        $this->_get_datatables_query($negative);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($negative = null)
    {
        $this->_get_datatables_query($negative);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function getListInfoCustomer($idCliente)
    {
        $this->db->select('lancamento.id id_lancamento, lancamento.vlr vlr_lancamento, lancamento.vcto, lancamento.pgto, clientes.id id_cliente, clientes.nome nome_cliente, clientes.cidade_id, clientes.is_negativado, cidades.id id_cidade, cidades.nome nome_cidade, planos.id, planos.vlr, clientes_planos.id, clientes_planos.nro_boleto, clientes_planos.dia_plano, clientes_planos.is_ativo');
        $this->db->from('lancamento');
        $this->db->join('clientes_planos', 'clientes_planos.id = lancamento.cliente_plano_id', 'left');
        $this->db->join('clientes', 'clientes.id = clientes_planos.id_cliente', 'left');
        $this->db->join('cidades', 'cidades.id = clientes.cidade_id', 'left');
        $this->db->join('planos', 'planos.id = clientes_planos.id_plano', 'left');
        $this->db->where('clientes_planos.id_cliente', $idCliente);
        $this->db->where('clientes_planos.is_ativo', 0);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    public function getListCity()
    {
        $this->db->select('cidades.id, cidades.nome');
        $this->db->from('cidades');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
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

}
