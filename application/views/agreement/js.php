<script type="text/javascript">

    var save_method; //for save method string
    var table;
    var listPerson = new Array;
    var listPlan = new Array;

    $(document).ready(function () {

        //funções que buscam lista de pessoas e planos
        getListPerson();
        getListPlan();

        jQuery.support.cors = true;

        //inicializa select2
        $('.js-example-basic-single').select2({width: '100%'});

        $('#datetimepicker2').datepicker({
            clearBtn: true,
            autoclose: true,
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            todayBtn: true,
            todayHighlight: true,
        });

        //datatables
        table = $('#table').DataTable({
            "language": {
                url: "<?= base_url() ?>assets/datatables/js/locales/portuguese-brasil.json"
            },
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo site_url('agreement/ajax_list') ?>",
                "type": "POST"
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [-1], //last column
                    "orderable": false, //set not orderable
                },
            ],
        });

        //datepicker
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
        });

    });

    function getListPlan()
    {
        $.ajax({
            url: "<?php echo site_url('agreement/getPlans/') ?>",
            type: "GET",
            dataType: 'JSON',
            success: function (data)
            {
                listPlan = data;
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível buscar os planos cadastradas');
            }
        });
    }
    function getListPerson()
    {
        $.ajax({
            url: "<?php echo site_url('agreement/getPersons/') ?>",
            type: "GET",
            dataType: 'JSON',
            success: function (data)
            {
                listPerson = data;
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível buscar os clientes cadastradas');
            }
        });
    }

    function clearValidation()
    {
        // limpa o css de validação
        $('#dvNBoleto').removeClass('has-error has-feedback');
        $('#inputErrorBoleto').addClass('sr-only');
        $('#spanBoleto').addClass('sr-only');

        $('#dvPValor').removeClass('has-error has-feedback');
        $('#inputErrorPValor').addClass('sr-only');
        $('#spanPValor').addClass('sr-only');

        $('#datetimepicker2').removeClass('has-error has-feedback');
        $('#inputErrorData').addClass('sr-only');
        $('#spanData').addClass('sr-only');
    }

    function add_agreement()
    {
        // limpa o css de validação
        clearValidation();

        // mostra preloader e esconde campos
        $('#group').addClass('hidden');
        $('#loader-1').removeClass('hidden');

        // constroi o html dos selects
        htmlToAppend('selectPerson', listPerson);
        htmlToAppend('selectPlans', listPlan);

        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Adicionar novo Contrato'); // Set Title to Bootstrap modal title

        $('#group').removeClass('hidden');
        $('#loader-1').addClass('hidden');
    }

    // constroi o html dos selects
    function htmlToAppend(id, list)
    {
        var htmlToAppend;

        // verifica se é o select de planos ou pessoas
        if (id === 'selectPlans') {
            htmlToAppend = '<select id="selectPlans" name="id_plano" class="form-control js-example-basic-single">';
            $.each(list, function (key, value) {
                htmlToAppend += '<option value=' + value.id + '>' + value.nome + " - R$" + value.vlr + '</option>';
            });
        } else {
            htmlToAppend = '<select id="selectPerson" name="person" class="form-control js-example-basic-single">';
            $.each(list, function (key, value) {
                htmlToAppend += '<option value=' + value.id + '>' + value.nome + '</option>';
            });
        }
        htmlToAppend += "</select>";

        // seta o select construido
        $('#' + id).empty().html(htmlToAppend);
    }

    function edit_agreement(id)
    {
        // limpa a validação
        clearValidation();

        // mostra preloader e esconde campos
        $('#group').addClass('hidden');
        $('#loader-1').removeClass('hidden');

        $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
        $('.modal-title').text('Editar Contrato'); // Set title to Bootstrap modal title  
        $('#selectPerson').val(null).trigger('change');// limpa o select
        $('#selectPlans').val(null).trigger('change');// limpa o select

        // constroi o html dos selects
        htmlToAppend('selectPerson', listPerson);
        htmlToAppend('selectPlans', listPlan);

        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('agreement/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                // seta os valores nos inputs 
                $('[name="person"]').val(data.cliente_plano.id_cliente).trigger('change');
                $('[name="id_plano"]').val(data.cliente_plano.id_plano).trigger('change');
                $('[name="vcto"]').val(data.vcto);
                $('[name="vlr"]').val(data.vlr);
                $('[name="nro_boleto"]').val(data.cliente_plano.nro_boleto);
                $('[name="dia_plano"]').val(data.cliente_plano.dia_plano);
                $('[name="id_lancamento"]').val(data.id);
                $('[name="id_cliente_plano"]').val(data.cliente_plano.id);

                // remove preloader e mostra campos
                $('#group').removeClass('hidden');
                $('#loader-1').addClass('hidden');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $('#group').removeClass('hidden');
                $('#loader-1').addClass('hidden');

                alert('Não foi possível buscar os dados, tente novamente');
            }
        });
    }

    function reload_table()
    {
        table.ajax.reload(null, false); //reload datatable ajax 
    }

    // valida campos obrigatórios
    function validation()
    {
        //verifica se o nome esta preenchido
        if ($('[name="nro_boleto"]').val().length === 0) {
            $('#dvNBoleto').addClass('has-error has-feedback');
            $('#spanBoleto').removeClass('sr-only');
            $('#inputErrorBoleto').removeClass('sr-only');
            return false;
        } else {
            $('#dvNBoleto').removeClass('has-error has-feedback');
            $('#spanBoleto').addClass('sr-only');
            $('#inputErrorBoleto').addClass('sr-only');
        }

        // verifica se o valor ta preenchido
        if ($('[name="vlr"]').val().length === 0) {
            $('#dvPValor').addClass('has-error has-feedback');
            $('#spanPValor').removeClass('sr-only');
            $('#inputErrorPValor').removeClass('sr-only');
            return false;
        } else {
            $('#dvPValor').removeClass('has-error has-feedback');
            $('#spanPValor').addClass('sr-only');
            $('#inputErrorPValor').addClass('sr-only');
        }

        // verifica se a data ta preenchida
        if ($('[name="vcto"]').val().length === 0) {
            $('#datetimepicker2').addClass('has-error has-feedback');
            $('#spanData').removeClass('sr-only');
            $('#inputErrorData').removeClass('sr-only');
            return false;
        } else {
            $('#datetimepicker2').removeClass('has-error has-feedback');
            $('#spanData').addClass('sr-only');
            $('#inputErrorData').addClass('sr-only');
        }
        return true;
    }

    function save()
    {
        // valida campos vazios
        if (validation()) {
            $('#btnSave').text('salvando...'); //change button text
            $('#btnSave').attr('disabled', true); //set button disable 
            var url;

            if (save_method == 'add') {
                url = "<?php echo site_url('agreement/ajax_add') ?>";
            } else {
                url = "<?php echo site_url('agreement/ajax_update') ?>";
            }

            // ajax adding data to database
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form').serialize(),
                dataType: "JSON",
                success: function (data)
                {
                    if (data.status) //if success close modal and reload ajax table
                    {
                        $('#modal_form').modal('hide');
                        reload_table();
                    }

                    $('#btnSave').text('Salvar'); //change button text
                    $('#btnSave').attr('disabled', false); //set button enable 
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Não foi possível salvar, confira se o boleto informado já existe e tente novamente');
                    $('#btnSave').text('Salvar'); //change button text
                    $('#btnSave').attr('disabled', false); //set button enable 

                }
            });
        }
    }

    function delete_agreement(id)
    {
        if (confirm('Você tem certeza que deseja deletar esse registro?'))
        {
            // ajax delete data to database
            $.ajax({
                url: "<?php echo site_url('agreement/ajax_delete') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data)
                {
                    //if success reload ajax table
                    $('#modal_form').modal('hide');
                    reload_table();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Não foi possível deletar os dados, tente novamente');
                }
            });

        }
    }

</script>