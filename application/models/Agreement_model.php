<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Agreement_model extends CI_Model
{

    var $table = 'lancamento';
    var $column_order = array('clientes.nome, clientes_planos.id', 'planos.vlr', 'nro_boleto', 'dia_plano'); //set column field database for datatable orderable
    var $column_search = array('clientes.nome', 'planos.vlr', 'nro_boleto', 'dia_plano'); //set column field database for datatable searchable just firstname , lastname , address are searchable
    var $order = array('id_lancamento' => 'desc'); // default order 

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query()
    {
        $this->db->select('lancamento.id id_lancamento, lancamento.vlr vlr_lancamento, lancamento.vcto, lancamento.pgto, clientes.id, clientes.nome nome_cliente, clientes.cidade_id, clientes.is_negativado, cidades.id id_cidade, cidades.nome nome_cidade, planos.id, planos.vlr vlr_plano, planos.nome nome_plano, clientes_planos.id clientes_planos_id, clientes_planos.nro_boleto, clientes_planos.dia_plano, clientes_planos.is_ativo');
        $this->db->from($this->table);
        $this->db->join('clientes_planos', 'clientes_planos.id = lancamento.cliente_plano_id', 'left');
        $this->db->join('clientes', 'clientes.id = clientes_planos.id_cliente', 'left');
        $this->db->join('cidades', 'cidades.id = clientes.cidade_id', 'left');
        $this->db->join('planos', 'planos.id = clientes_planos.id_plano', 'left');
        $this->db->where('clientes.is_negativado', 0);

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

    public function getListPersons()
    {
        $this->db->select('clientes.id, clientes.nome');
        $this->db->from('clientes');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    public function getListPlans()
    {
        $this->db->select('planos.id, planos.nome, planos.vlr');
        $this->db->from('planos');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
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
    
    public function getClientePlano($id)
    {
//        $this->db->select('clientes_planos.id clientes_planos_id, clientes_planos.nro_boleto, clientes_planos.dia_plano, clientes_planos.dia_plano, clientes_planos.id_cliente, clientes_planos.id_plano, clientes.id, clientes.nome nome_cliente, planos.id, planos.nome nome_plano, planos.vlr');       
        $this->db->from("clientes_planos");
//        $this->db->join('clientes', 'clientes.id = clientes_planos.id_cliente', 'inner');
//        $this->db->join('planos', 'planos.id = clientes_planos.id_plano', 'inner');
        $this->db->where('clientes_planos.id', $id);
        $query = $this->db->get();

        return $query->row();
    }
    
    public function get_by_boleto($boleto)
    {
        $this->db->from("clientes_planos");
        $this->db->where('nro_boleto', $boleto);
        $query = $this->db->get();

        return $query->row();
    }

    public function save($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
    
    public function saveClientePlano($data)
    {
        $this->db->insert("clientes_planos", $data);
        return $this->db->insert_id();
    }

    public function updateClientePlano($where, $data)
    {
        $this->db->update("clientes_planos", $data, $where);
        return $this->db->affected_rows();
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
