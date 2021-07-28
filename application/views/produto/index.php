<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Produtos</title>
        <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/select2/css/select2.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css') ?>" rel="stylesheet">       
    </head> 
    <body>
        <?= $this->load->view('menu/menu', null, true) ?>

        <div class="panel panel-primary">
            <div class="panel-heading">Produto</div>
            <div class="panel-body">

                <button class="btn btn-success" onclick="add_produto()"><i class="glyphicon glyphicon-plus"></i> Novo</button>
                <br />
                <br />
                <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produto</th>
                            <th>Valor</th>
                            <th>Observação</th>
                            <th style="width:125px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Produto</th>
                            <th>Valor</th>
                            <th>Observação</th>
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

        <?= $this->load->view('produto/js', null, true) ?>

        <!-- Bootstrap modal -->
        <div class="modal fade" id="modal_form" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Dados do Produto</h3>
                    </div>
                    <div class="modal-body form">
                        <form action="#" id="form" class="form-horizontal">
                            <input type="hidden" value="" name="id"/> 
                            <div class="form-body">
                                <div class="loader hidden" id="loader-1"></div>                      
                                <div id="group">
                                    <div class="form-group">
                                        <div id="dvNome" class="col-md-8 ">
                                            <label for="nome">Nome</label>
                                            <input name="nome" class="form-control" type="text" aria-describedby="inputError2Status">
                                            <span id="spanNome" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                            <span id="inputError2Status" class="sr-only">*Campo Obrigatório</span>
                                        </div>
                                        <div id="dvCpf" class="col-md-4">
                                            <label for="vlr">Valor</label>
                                            <input name="vlr" class="form-control" type="text" aria-describedby="inputError3Status">
                                            <span id="spanVlr" class="sr-only glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                                            <span id="inputError3Status" class="sr-only">*Campo Obrigatório</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div id="dvObservacao" class="col-md-12 ">
                                            <label for="observacao">Observação</label>
                                            <textarea name="observacao" class="form-control" type="text" maxlength="255" style="resize:none;"></textarea>
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

    </body>
</html>