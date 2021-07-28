<script type="text/javascript">

    var save_method;
    var table;

    $(document).ready(function () {
        jQuery.support.cors = true;

        //datatables
        table = $('#table').DataTable({
            "language": {
                url: "<?= base_url() ?>assets/datatables/js/locales/portuguese-brasil.json"
            },
            "processing": true,
            "serverSide": true,
            "order": [],
            // carrega os dados
            "ajax": {
                "url": "<?php echo site_url('produto/ajax_list') ?>",
                "type": "POST"
            },
            //propriedades das colunas.
            "columnDefs": [
                {
                    "targets": [-1], //ultima columa
                    "orderable": false, //set false para nao ordenar
                },
            ],
        });
    });

    function add_produto()
    {
        // limpa o css de validação
        clearClass('dvNome', 'inputError2Status', 'spanNome');
        clearClass('dvVlr', 'inputError3Status', 'spanVlr');

        save_method = 'add';
        $('#form')[0].reset(); // reseta form nas modals
        $('.form-group').removeClass('has-error');
        $('.help-block').empty(); // lima string erro
        $('#modal_form').modal('show');
        $('.modal-title').text('Adicionar produto');
    }

    function clearClass(dv, inputError, span) {
        $('#' + dv).removeClass('has-error has-feedback');
        $('#' + inputError).addClass('sr-only');
        $('#' + span).addClass('sr-only');
    }

    function addClassError(dv, inputError, span) {
        $('#' + dv).addClass('has-error has-feedback');
        $('#' + span).removeClass('sr-only');
        $('#' + inputError).removeClass('sr-only');
    }

    function edit_produto(id)
    {
        // limpa o css de validação
        clearClass('dvNome', 'inputError2Status', 'spanNome');
        clearClass('dvVlr', 'inputError3Status', 'spanVlr');

        // mostra preloader e esconde campos
        $('#group').addClass('hidden');
        $('#loader-1').removeClass('hidden');

        $('#modal_form').modal('show');
        $('.modal-title').text('Editar Produto');

        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('produto/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {

                $('[name="id"]').val(data.id);
                $('[name="nome"]').val(data.nome);
                $('[name="vlr"]').val(data.vlr);
                $('[name="observacao"]').val(data.observacao);

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
        table.ajax.reload(null, false); //recarrega datatable  
    }

    function save()
    {
        //limpa class de erros
        clearClass('dvNome', 'inputError2Status', 'spanNome');
        clearClass('dvVlr', 'inputError3Status', 'spanVlr');

        //verifica se o nome esta preenchido
        if ($('[name="nome"]').val().length === 0) {
            addClassError('dvNome', 'inputError2Status', 'spanNome');
            return;
        }

        //vlr
        if ($('[name="vlr"]').val().length === 0) {
            addClassError('dvVlr', 'inputError3Status', 'spanVlr');
            return;
        }

        $('#btnSave').text('salvando...');
        $('#btnSave').attr('disabled', true);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('produto/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('produto/ajax_update') ?>";
        }

        // ajax add dados na base de dados
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                if (data.status)
                {
                    $('#modal_form').modal('hide');
                    reload_table();
                } else {
                    alert('Altere algum campo para salvar!');
                }

                $('#btnSave').text('Salvar');
                $('#btnSave').attr('disabled', false);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível salvar, tente novamente');
                $('#btnSave').text('Salvar');
                $('#btnSave').attr('disabled', false);
            }
        });
    }

    function delete_produto(id)
    {
        if (confirm('Você tem certeza que deseja deletar esse registro?'))
        {

            $.ajax({
                url: "<?php echo site_url('produto/ajax_delete') ?>/" + id,
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