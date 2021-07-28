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
    <body style="background-color: #e7e7e7 !important;">
        <?= $this->load->view('menu/menu', null, true) ?>
        <div class="col-md-12">
            <div class="col-sm-3 col-xs-12">
                <div class="card hidden-print">
                    <div class="card-block">
                        <div class="col-xs-12">                                                    
                            <div class="form-group date" <label for="iniciodata" class="form-control-label">
                                Data Inicial</label><input placeholder="Ex: <?= Date('d/m/Y') ?>" type="text" name="iniciodata" id="iniciodata" class="form-control">
                            </div>
                            <div class="form-group date" <label for="finaldata" class="form-control-label">
                                Data Final</label><input placeholder="Ex: <?= Date('d/m/Y') ?>" type="text" name="finaldata" id="finaldata" class="form-control">
                            </div>
                            <button type="button" name="buttonFiltrar" id="buttonFiltrar" class="btn btn-block btn-primary" accesskey="F"><i class="fa fa-lg fa-fw fa-search"></i> <u>F</u>iltrar</button>
                            <button type="button" name="buttonClear" id="buttonClear" class="btn btn-block btn-default p-t-5" accesskey="L"><i class="fa fa-lg fa-fw fa-trash"></i> <u>L</u>impar</button>
                        </div>
                    </div>
                </div>
            </div>    
            <div class="col-sm-9 col-xs-12">
                <div class="panel">
                    <div class="panel-body">
                        <table id="table" class="table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Profissional</th>
                                    <th>Faturamento</th>
                                    <th>N° de Atendimentos</th>
                                    <th>Ticket Médio</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> 

        <script src="<?php echo base_url('assets/jquery/jquery.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.js') ?>"></script>
        <script src="<?php echo base_url('assets/select2/js/select2.js') ?>"></script>
        <script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ?>"></script>

        <?= $this->load->view('comissao/js', null, true) ?>

    </body>
</html>