<script type="text/javascript">

    var save_method; //for save method string
    var table;
    var listCity = new Array;

    $(document).ready(function () {
        jQuery.support.cors = true;

        // função que busca a lista de cidades cadastradas
        getListCity();

        //inicializa select2
        $('.js-example-basic-single').select2({width: '100%'});

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
                "url": "<?php echo site_url('person/ajax_list') ?>",
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
            todayHighlight: true,
        });

    });

    function showDebtors(idCliente)
    {
        $.ajax({
            url: "<?php echo site_url('person/showDebtors/') ?>",
            type: "POST",
            data: {
                idCliente: idCliente
            },
            dataType: 'JSON',
            success: function (data)
            {
                console.log(data);
                $('#nome').html('<strong>Nome:</strong> ' +data[0].nome_cliente);
                $('#cidade').html('<strong>Cidade:</strong> ' +data[0].nome_cidade);
                $('#boleto').html('<strong>N° Boleto:</strong> ' +data[0].nro_boleto);
                $('#plano').html('<strong>Plano R$:</strong> ' +data[0].vlr);
                $('#modal_details').modal('show'); // show bootstrap modal
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível carregar os dados, tente novamente');
            }
        });
    }

    function negative()
    {
        table = $("#table").DataTable(); // get api instance

        // load data using api
        table.ajax.url("<?php echo site_url('person/ajax_list/1') ?>").load();
    }

    function getListCity()
    {
        $.ajax({
            url: "<?php echo site_url('person/getCitys/') ?>",
            type: "GET",
            dataType: 'JSON',
            success: function (data)
            {
                listCity = data;
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível buscar as cidades cadastradas');
            }
        });
    }

    // constroi o html dos selects
    function htmlToAppend(id, list)
    {
        var htmlToAppend;

        // verifica se é o select de planos ou pessoas

        if (id === 'selectCity') {
            htmlToAppend = '<select id="selectCity" name="cidade_id" class="form-control js-example-basic-single">';
            $.each(list, function (key, value) {
                htmlToAppend += '<option value=' + value.id + '>' + value.nome + '</option>';
            });
        }
        htmlToAppend += "</select>";

        // seta o select construido
        $('#' + id).empty().html(htmlToAppend);
    }

    function add_person()
    {
        $('#dvCheckBox').addClass('hidden');

        // limpa o css de validação
        $('#dvNome').removeClass('has-error has-feedback');
        $('#inputError2Status').addClass('sr-only');
        $('#spanNome').addClass('sr-only');

        // constroi o html dos selects
        htmlToAppend('selectCity', listCity);

        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Adicionar cliente'); // Set Title to Bootstrap modal title
    }

    function edit_person(id)
    {
        $('#dvCheckBox').removeClass('hidden');

        // limpa o css de validação
        $('#dvNome').removeClass('has-error has-feedback');
        $('#inputError2Status').addClass('sr-only');
        $('#spanNome').addClass('sr-only');

        // mostra preloader e esconde campos
        $('#group').addClass('hidden');
        $('#loader-1').removeClass('hidden');

        $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
        $('.modal-title').text('Editar Cliente'); // Set title to Bootstrap modal title
        $('#selectCity').val(null).trigger('change');// limpa o select

        htmlToAppend('selectCity', listCity);// constroi o html dos selects

        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('person/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {

                //marca ou não o checkbox
                if (data.is_negativado === '1') {
                    $('[name="check"]').attr('checked', true);
                } else {
                    $('[name="check"]').attr('checked', false);
                }

                $('[name="id"]').val(data.id);
                $('[name="nome"]').val(data.nome);
//                $('[name="cidade_id"]').val(data.cidade_id);
                $('[name="cidade_id"]').val(data.cidade_id).trigger('change');

                // remove preloader e mostra campos
                $('#group').removeClass('hidden');
                $('#loader-1').addClass('hidden');

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                // remove preloader e mostra campos
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

    function save()
    {
        //verifica se o nome esta preenchido
        if ($('[name="nome"]').val().length === 0) {
            $('#dvNome').addClass('has-error has-feedback');
            $('#spanNome').removeClass('sr-only');
            $('#inputError2Status').removeClass('sr-only');
            return;
        }

        $('#btnSave').text('salvando...'); //change button text
        $('#btnSave').attr('disabled', true); //set button disable 
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('person/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('person/ajax_update') ?>";
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
                alert('Não foi possível salvar, tente novamente');
                $('#btnSave').text('Salvar'); //change button text
                $('#btnSave').attr('disabled', false); //set button enable 
            }
        });
    }

    function delete_person(id)
    {
        if (confirm('Você tem certeza que deseja deletar esse registro?'))
        {
            // ajax delete data to database
            $.ajax({
                url: "<?php echo site_url('person/ajax_delete') ?>/" + id,
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