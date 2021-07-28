<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Planos</title>
        <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css') ?>" rel="stylesheet">
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
            <div class="panel-heading">Planos</div>
            <div class="panel-body">

                <button class="btn btn-success" onclick="add_plan()"><i class="glyphicon glyphicon-plus"></i> Novo</button>
                <button class="btn btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Atualizar</button>
                <br />
                <br />
                <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th style="width:125px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>Descrição</th>
                            <th>Valor</th>
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
        <script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/jqueryFormatCurrency/js/jquery.formatCurrency-1.4.0.min.js') ?>"</script>
        <script src="<?php echo base_url('assets/jqueryFormatCurrency/js/jquery.formatCurrency.pt-BR.js') ?>" </script>

        <?= $this->load->view('plans/js', null, true) ?>

        <!-- Bootstrap modal -->
        <div class="modal fade" id="modal_form" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Dados dos planos</h3>
                    </div>
                    <div class="modal-body form">
                        <form action="#" id="form" class="form-horizontal">
                            <input type="hidden" value="" name="id"/> 
                            <div class="form-body">
                                <div class="form-group">
                                    <div class="col-md-8">
                                        <label for="nome">Descrição</label>
                                        <input name="nome" class="form-control" type="text">
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="vlr">Valor</label>
                                        <input name="vlr" class="form-control" type="number">
                                        <span class="help-block"></span>
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