<script type="text/javascript">

    var save_method; //for save method string
    var table;

    $(document).ready(function () {
        jQuery.support.cors = true;

        // seleceiona o mes
        var d = new Date(), n = d.getMonth(), y = d.getFullYear();
        $('#mes option:eq(' + n + ')').prop('selected', true);
        $('#mes').select2({width: '25%'});

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
            "aLengthMenu": [[10, 25, 50, 75, -1], [10, 25, 50, 75, "Todos"]],
            "iDisplayLength": -1,
            // lingua do datatable
            "language": {
                url: "<?= base_url() ?>assets/datatables/js/locales/portuguese-brasil.json"
            },
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo site_url('prevision/ajax_list') ?>",
                "type": "POST"
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [-1], //last column
                    "orderable": false, //set not orderable
                },
            ],
//            // botões de ações da tabela
//            dom:'lfBrtip',
//            buttons: [
//                {extend: 'colvis', text: 'Coluna'}
//            ]
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

    //função que negativa cliente
    function negativeCustomer(idCliente, nroBoleto) {
        $.ajax({
            url: "<?php echo site_url('prevision/negativeCustomer/') ?>",
            type: "POST",
            data: {
                idCliente: idCliente, nroBoleto: nroBoleto
            },
            dataType: 'JSON',
            success: function (data)
            {
                reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível negativar o cliente');
            }
        });
        
    }

    // limita o input a quantidade máxima determinada no html
    function maxLengthCheck(object) {
        if (object.value.length > object.maxLength)
            object.value = object.value.slice(0, object.maxLength)
    }

    function down()
    {
        $('#modal_baixar').modal('show'); // show bootstrap modal
    }

    function add_prevision()
    {
        $.ajax({
            url: "<?php echo site_url('prevision/getPersons/') ?>",
            type: "GET",
            dataType: 'JSON',
            success: function (data)
            {
                $.each(data, function (key, value)
                {
                    $('#selectPerson').append('<option value=' + value.id + '>' + value.nome + '</option>');
                });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível buscar os clientes cadastradas');
            }
        });

        $.ajax({
            url: "<?php echo site_url('prevision/getPlans/') ?>",
            type: "GET",
            dataType: 'JSON',
            success: function (data)
            {
                $.each(data, function (key, value)
                {
                    $('#selectPlans').append('<option value=' + value.id + '>' + value.nome + " - R$" + value.vlr + '</option>');
                });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível buscar os planos cadastradas');
            }
        });

        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Adicionar Previsão'); // Set Title to Bootstrap modal title
    }

    function edit_prevision(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('prevision/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {

                $('[name="id"]').val(data.id);
                $('[name="nome"]').val(data.nome);
                $('[name="vlr"]').val(data.vlr);
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Editar Previsão'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível buscar os dados, tente novamente');
            }
        });
    }

    function reload_table()
    {
        table.ajax.reload(null, false); //reload datatable ajax 
    }

    function save()
    {
        $('#btnSave').text('salvando...'); //change button text
        $('#btnSave').attr('disabled', true); //set button disable 
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('prevision/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('prevision/ajax_update') ?>";
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
                alert('Não foi possível salvar, confira o boleto informado e tente novamente');
                $('#btnSave').text('Salvar'); //change button text
                $('#btnSave').attr('disabled', false); //set button enable 
            }
        });
    }

    function delete_prevision(id)
    {
        if (confirm('Você tem certeza que deseja deletar esse registro?'))
        {
            // ajax delete data to database
            $.ajax({
                url: "<?php echo site_url('prevision/ajax_delete') ?>/" + id,
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