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
        <link href="<?php echo base_url('assets/datatables/css/buttons.dataTables.min.css') ?>" rel="stylesheet">
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
            <div class="panel-heading">Previsões</div>
            <div class="panel-body">

                <!--<button class="btn btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Atualizar</button>-->
                <div class="row">
                    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                        <button class="btn btn-success" onclick=" down()"><i class="glyphicon glyphicon-arrow-down"></i> Baixar</button>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                        <div class="form-inline">
                            <span>A partir</span>
                            <select id='mes' class="form-control">
                                <option value='1'>Janeiro</option>
                                <option value='2'>Fevereiro</option>
                                <option value='3'>Março</option>
                                <option value='4'>Abril</option>
                                <option value='5'>Maio</option>
                                <option value='6'>Junho</option>
                                <option value='7'>Julho</option>
                                <option value='8'>Agosto</option>
                                <option value='9'>Setembro</option>
                                <option value='10'>Outubro</option>
                                <option value='11'>Novembro</option>
                                <option value='12'>Dezembro</option>
                            </select> 
                            <input name="coluna" class="form-control" type="number" min="1" max="6" maxlength="1" placeholder="1 a 6" oninput="maxLengthCheck(this)"> 
                            <button class="btn btn-info" onclick=""><i class="glyphicon glyphicon-refresh"></i> Filtrar</button>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                        <button class="btn btn-primary pull-right"><i class="glyphicon glyphicon-repeat"></i> Gerar Previsão</button>
                    </div>
                </div>


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
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
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
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
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
        <script src="<?php echo base_url('assets/datatables/js/dataTables.buttons.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/buttons.colVis.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/buttons.flash.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/buttons.html5.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/buttons.print.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/jszip.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/pdfmake.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/vfs_fonts.js') ?>"></script>

        <?= $this->load->view('prevision/js', null, true) ?>

        <!-- modal baixar -->
        <div class="modal fade" id="modal_baixar" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Informe os Boletos a baixar</h3>
                    </div>
                    <div class="modal-body form">
                        <form action="#" id="form_baixar" class="form-horizontal">
                            <div class="form-body">
                                <div class="form-group">

                                    <div class="col-md-12">
                                        <label for="boleto_baixar">Nº Boleto</label>
                                        <input name="boleto_baixar" class="form-control" type="text" placeholder="Separe cada boleto por virgula"> 
                                        <span class="help-block"></span>
                                    </div>  

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnSave" class="btn btn-primary">Baixar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- End Bootstrap modal -->
        <div id="fb-root"></div>
    </body>
</html>