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
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head> 
    <body>
        <?= $this->load->view('menu/menu', null, true) ?>

        <div class="panel panel-primary">
            <div class="panel-heading">Contratos</div>
            <div class="panel-body">

                <button class="btn btn-success" onclick="add_agreement()"><i class="glyphicon glyphicon-plus"></i> Novo</button>
                <button class="btn btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Atualizar</button>
                <br />
                <br />
                <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Cidade</th>
                            <th>Cliente</th>
                            <th>Dia</th>
                            <th>N° Boleto</th>
                            <th>Vlr</th>
                            <th>Plano</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>Cidade</th>
                            <th>Cliente</th>
                            <th>Dia</th>
                            <th>N° Boleto</th>
                            <th>Vlr</th>
                            <th>Plano</th>
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

        <?= $this->load->view('agreement/js', null, true) ?>

        <!-- Bootstrap modal -->
        <div class="modal fade" id="modal_form" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Adicionando novo Contrato</h3>
                    </div>
                    <div class="modal-body form">
                        <form action="#" id="form" class="form-horizontal">
                            <input type="hidden" value="" name="id_lancamento"/> 
                            <input type="hidden" value="" name="id_cliente_plano"/> 
                            <div class="form-body">
                                <div class="loader hidden" id="loader-1"></div>                                
                                <div class="form-group" id="group">
                                    <div class="col-md-6">
                                        <label for="person">Cliente</label>
                                        <select id="selectPerson" name="person" class="form-control js-example-basic-single">
                                            <option value="">Selecionar</option>                                        
                                        </select>
                                    </div>                  
                                    <div class="col-md-6">
                                        <label for="id_plano">Plano</label>
                                        <select id="selectPlans" name="id_plano" class="form-control js-example-basic-single">
                                            <option value="">Selecionar</option>                                        
                                        </select>
                                    </div>    
                                    <div id="dvNBoleto" class="col-md-6">
                                        <label for="nro_boleto">Nº Boleto <span id="inputErrorBoleto" class="sr-only">(campo obrigatório)</span></label>
                                        <input id="nro_boleto" name="nro_boleto" class="form-control" type="number" minlength="2" maxlength="11" aria-describedby="inputErrorBoleto">
                                        <span id="spanBoleto" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>                                        
                                    </div>  
                                    <div id="dvPValor" class="col-md-6">
                                        <label for="vlr">Primeiro Valor <span id="inputErrorPValor" class="sr-only">(campo obrigatório)</span></label>
                                        <input name="vlr" class="form-control" type="text"  aria-describedby="inputErrorPValor">
                                        <span id="spanPValor" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>                                        
                                    </div>                                     
                                    <div class="col-md-4">
                                        <label for="dia_plano">Dia</label>
                                        <input name="dia_plano" class="form-control" type="text" maxlength="11">
                                        <span class="help-block"></span>
                                    </div> 
                                    <div class='col-md-8'>
                                        <label for="vcto">Vencimento <span id="inputErrorData" class="sr-only">(campo obrigatório)</span></label>
                                        <div class='input-group date' id='datetimepicker2'>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                            <input id="vcto" name="vcto" type='text' class="form-control" />
                                            <span id="spanData" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
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
        <!-- End Bootstrap modal -->       
    </body>
</html>