<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Clientes</title>
        <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/select2/css/select2.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css') ?>" rel="stylesheet">       
    </head> 
    <body>
        <?= $this->load->view('menu/menu', null, true) ?>

        <div class="panel panel-primary">
            <div class="panel-heading">Cliente</div>
            <div class="panel-body">

                <button class="btn btn-success" onclick="add_pessoa()"><i class="glyphicon glyphicon-plus"></i> Novo</button>
                <br />
                <br />
                <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>CPF</th>
                            <th>Data Nasc</th>
                            <th>Telefone</th>
                            <th>Endereço</th>
                            <th style="width:125px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>CPF</th>
                            <th>Data Nasc</th>
                            <th>Telefone</th>
                            <th>Endereço</th>
                            <th>Ações</th>
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

        <?= $this->load->view('pessoa/js', null, true) ?>

        <!-- Bootstrap modal -->
        <div class="modal fade" id="modal_form" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Dados do cliente</h3>
                    </div>
                    <div class="modal-body form">
                        <form action="#" id="form" class="form-horizontal">
                            <input type="hidden" value="" name="id"/> 

                            <div id="group" class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                                <ul class="nav nav-tabs" id="myTabs" role="tablist"> 
                                    <li role="presentation" class="active">
                                        <a href="#geral" id="geral-tab" role="tab" data-toggle="tab" aria-controls="geral" aria-expanded="true">Geral</a>
                                    </li> 
                                    <li role="presentation" class="">
                                        <a href="#comissao" role="tab" id="produto-tab" data-toggle="tab" aria-controls="comissao" aria-expanded="false">Comissão</a>
                                    </li> 
                                </ul> 

                                <div class="tab-content" id="myTabContent"> 
                                    <div class="tab-pane fade active in" role="tabpanel" id="geral" aria-labelledby="geral-tab"> 
                                        <br>
                                        <div class="form-body">
                                            <div class="loader hidden" id="loader-1"></div>                      
                                            <div id="group">
                                                <div class="form-group">
                                                    <div id="dvNome" class="col-md-8 col-xs-12">
                                                        <label for="nome">Nome</label>
                                                        <input name="nome" class="form-control" type="text" aria-describedby="inputError2Status">
                                                        <span id="spanNome" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                                        <span id="inputError2Status" class="sr-only">*Campo Obrigatório</span>
                                                    </div>
                                                    <div id="dvCpf" class="col-md-4 col-xs-12">
                                                        <label for="cpf">CPF</label>
                                                        <input name="cpf" class="form-control" type="text" aria-describedby="inputError3Status">
                                                        <span id="spanCpf" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                                        <span id="inputError3Status" class="sr-only">*Digite CPF válido</span>
                                                    </div>
                                                    <div id="dvEndereco" class="col-md-12 col-xs-12">
                                                        <label for="endereco">Endereço</label>
                                                        <input name="endereco" class="form-control" type="text" aria-describedby="inputError4Status">
                                                        <span id="spanEndereco" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                                        <span id="inputError4Status" class="sr-only">*Campo Obrigatório</span>
                                                    </div>
                                                    <div class='col-md-4 col-xs-12'>
                                                        <label for="data_nascimento">Data Nasc <span id="inputErrorData" class="sr-only">(campo obrigatório)</span></label>
                                                        <div class='input-group date' id='datetimepicker2'>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            <input id="data_nascimento" placeholder="<?= Date('d/m/Y') ?>" name="data_nascimento" type='text' class="form-control" />
                                                            <span id="spanData" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                                        </div>
                                                    </div>  
                                                    <div id="dvTelefone" class="col-md-6 col-xs-12">
                                                        <label for="telefone">Tel.</label>
                                                        <input name="telefone" class="form-control" type="text" aria-describedby="inputError5Status">
                                                        <span id="spanTelefone" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                                        <span id="inputError5Status" class="sr-only">*Campo Obrigatório</span>
                                                    </div>   

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Comissão -->
                                    <div class="tab-pane fade" role="tabpanel" id="comissao" aria-labelledby="comissao-tab">
                                        <br>
                                        <div class="form-body">                                
                                            <div class="form-group" id="dvComissao">
                                                <div id="dvComissaoPo" class="col-md-12 col-xs-12">
                                                    <label for="comissaoPo">Comissão em per.(%)</label>
                                                    <input placeholder="User ponto e não vírgula. Ex: 50.0" name="comissaoPo" class="form-control" type="text" >
                                                    <span id="spanComissaoPo" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                                </div>
                                                <div id="dvTipoColaborador" class="col-md-12 col-xs-12">
                                                    <label class="checkbox-inline"><input name="colaborador" id="colaborador" type="checkbox" value="">Colaborador</label>
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

        <!-- Info modal -->
        <div class="modal fade" id="modal_info" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Informações</h3>
                    </div>
                    <div class="modal-body form">
                        <code name="codeTagInfo"></code>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnVisto" onclick="visto()" class="btn btn-primary">Marcar como visto</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- modal Info  -->

    </body>
</html>