<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Vendas</title>
        <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/select2/css/select2.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css') ?>" rel="stylesheet">       
    </head> 
    <body>
        <?= $this->load->view('menu/menu', null, true) ?>

        <div class="panel panel-primary">
            <div class="panel-heading">Venda</div>
            <div class="panel-body">

                <button class="btn btn-success" onclick="add_venda()"><i class="glyphicon glyphicon-plus"></i> Novo</button>
                <br />
                <br />
                <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Emissão</th>
                            <th>Cliente</th>
                            <th>Vlr. Total</th>
                            <th>Observação</th>
                            <th style="width:125px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>

                    <tfoot>
                        <tr>

                            <th colspan="3" style="text-align:right">Total:</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <script src="<?php echo base_url('assets/jquery/jquery.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.js') ?>"></script>
        <script src="<?php echo base_url('assets/select2/js/select2.js') ?>"></script>
        <script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ?>"></script>

        <?= $this->load->view('venda/js', null, true) ?>

        <!-- Bootstrap modal -->
        <div class="modal fade" id="modal_form" role="dialog">
            <div class="modal-dialog modal-lg" style="max-width : 100% !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Dados da Venda</h3>
                    </div>
                    <div class="modal-body form">
                        <form action="#" id="form" class="form-horizontal">
                            <input type="hidden" value="" name="id"/> 
                            <div class="loader hidden" id="loader-1"></div> 

                            <div id="group" class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                                <ul class="nav nav-tabs" id="myTabs" role="tablist"> 
                                    <li role="presentation" class="active">
                                        <a href="#geral" id="geral-tab" role="tab" data-toggle="tab" aria-controls="geral" aria-expanded="true">Geral</a>
                                    </li> 
                                    <li role="presentation" class="">
                                        <a href="#produto" role="tab" id="produto-tab" data-toggle="tab" aria-controls="produto" aria-expanded="false">Produto</a>
                                    </li> 
                                </ul> 

                                <!-- GERAL -->
                                <div class="tab-content" id="myTabContent"> 
                                    <div class="tab-pane fade active in" role="tabpanel" id="geral" aria-labelledby="geral-tab"> 
                                        <br>
                                        <div class="form-body">                                
                                            <div class="form-group" >
                                                <div class="col-md-8">
                                                    <label for="id_pessoa">Cliente</label>
                                                    <select id="selectPerson" name="id_pessoa" class="form-control js-example-basic-single">
                                                        <option value="">Selecionar</option>                                        
                                                    </select>
                                                </div>     
                                              
                                                <div class="col-md-4">
                                                    <label for="vlr_total">Total</label>
                                                    <input id="vlr_total" name="vlr_total" readonly="1" class="form-control" type="text" maxlength="11" >
                                                    <span class="help-block"></span>
                                                </div> 
                                                <div id="dvObservacao" class="col-md-12 ">
                                                    <label for="observacao">Observação</label>
                                                    <textarea name="observacao" class="form-control" type="text" maxlength="255" style="resize:none;"></textarea>
                                                </div>
                                            </div>                              
                                        </div>
                                    </div> 

                                    <!-- PRODUTO -->
                                    <div class="tab-pane fade" role="tabpanel" id="produto" aria-labelledby="produto-tab">
                                        <br>
                                        <a class="btn btn-success" onclick="add_item()"><i class="glyphicon glyphicon-plus"></i> Novo</a>
                                        <br>
                                        <br>
                                        <div class="form-body">                                
                                            <div class="form-group" id="dvProdutos">
                                                <!--                                                <div id="linha">                                                   
                                                                                                    <div class="col-md-5">
                                                                                                        <label for="nome_produto[]">Produto</label>
                                                                                                        <select id="selectProduct" name="nome_produto[]" class="form-control js-example-basic-single">
                                                                                                            <option value="">Selecionar</option>                                        
                                                                                                        </select>
                                                                                                    </div>     
                                                                                                    <div class="col-md-2">
                                                                                                        <label for="qtd[]">Qtd.</label>
                                                                                                        <input name="qtd[]" class="form-control" type="number" maxlength="11"  value="1">
                                                                                                        <span class="help-block"></span>
                                                                                                    </div> 
                                                                                                    <div class="col-md-2">
                                                                                                        <label for="vlr[]">Valor</label>
                                                                                                        <input name="vlr[]" class="form-control" type="number" maxlength="11" min="0">
                                                                                                        <span class="help-block"></span>
                                                                                                    </div> 
                                                                                                    <div class="col-md-2">
                                                                                                        <label for="total[]">Total</label>
                                                                                                        <input name="total[]" class="form-control" type="number" maxlength="11"  readonly="1">
                                                                                                        <span class="help-block"></span>
                                                                                                    </div> 
                                                                                                    <div class="col-md-1" style="padding-top: 27px;">
                                                                                                        <a class="btn btn-sm btn-danger" title="Remover">
                                                                                                            <i class="glyphicon glyphicon-trash"></i> 
                                                                                                        </a>
                                                                                                    </div>   
                                                                                                </div>     -->
                                            </div>     
                                            <div class="form-group">
                                                <div class="col-md-4">
                                                    <label for="vlr_total" class="form-control-label">Total Produtos</label>
                                                    <input type="number" name="vlr_total" value="" id="vlr_total" class="form-control" readonly="1" step="any">
                                                </div>            
                                            </div>
                                        </div>     
                                    </div> 
                                </div> 
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Salvar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- modal Bootstrap  -->
        <script>

        </script>
       
    </body>
</html>