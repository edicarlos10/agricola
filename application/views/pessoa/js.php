<script type="text/javascript">

    var save_method;
    var table;

    $(document).ready(function () {
        jQuery.support.cors = true;

        $('#colaborador').change(function () {
            cb = $(this);
            cb.val(cb.prop('checked'));
        });

        $.ajax({
            url: "<?php echo site_url('pessoa/get_info') ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                if (data !== null && data.visto === '0') {
                    $('[name="codeTagInfo"]').text(data.descricao);
                    $('#modal_info').modal('show');
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $('#modal_info').modal('hide');
            }
        });

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
                "url": "<?php echo site_url('pessoa/ajax_list') ?>",
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

        $('#datetimepicker2').datepicker({
            clearBtn: true,
            autoclose: true,
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            todayBtn: true,
            todayHighlight: true,
        });

    });

    function add_pessoa()
    {
        // limpa o css de validação
        clearClass('dvNome', 'inputError2Status', 'spanNome');
        clearClass('dvCpf', 'inputError3Status', 'spanCpf');

        save_method = 'add';
        $('#form')[0].reset(); // reseta form nas modals
        $('.form-group').removeClass('has-error');
        $('.help-block').empty(); // lima string erro
        $('#modal_form').modal('show');
        $('.modal-title').text('Adicionar cliente');
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

    function edit_pessoa(id)
    {
        // limpa o css de validação
        clearClass('dvNome', 'inputError2Status', 'spanNome');
        clearClass('dvCpf', 'inputError3Status', 'spanCpf');

        // mostra preloader e esconde campos
        $('#group').addClass('hidden');
        $('#loader-1').removeClass('hidden');

        $('#modal_form').modal('show');
        $('.modal-title').text('Editar Cliente');

        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('pessoa/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {

                $('[name="id"]').val(data.id);
                $('[name="nome"]').val(data.nome);
                $('[name="cpf"]').val(data.cpf);
                $('[name="data_nascimento"]').val(data.data_nascimento);
                $('[name="telefone"]').val(data.telefone);
                $('[name="endereco"]').val(data.endereco);
                $('[name="comissaoPo"]').val(data.comissao_perc);
                if(data.is_colaborador === '1'){
                    $('#colaborador').prop('checked', true);
                }

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

    // valida cpf
    function checkCPF(strCPF) {
        var soma = 0;
        var resto;
        if (strCPF === "00000000000") {
            return false;
        }

        for (var i = 1; i <= 9; i++) {
            soma = soma + parseInt(strCPF.substring(i - 1, i)) * (11 - i);
        }
        resto = (soma * 10) % 11;

        if ((resto === 10) || (resto === 11)) {
            resto = 0;
        }
        if (resto !== parseInt(strCPF.substring(9, 10))) {
            return false;
        }

        soma = 0;
        for (var i = 1; i <= 10; i++) {
            soma = soma + parseInt(strCPF.substring(i - 1, i)) * (12 - i);
        }
        resto = (soma * 10) % 11;

        if ((resto === 10) || (resto === 11)) {
            resto = 0;
        }
        if (resto !== parseInt(strCPF.substring(10, 11))) {
            return false;
        }
        return true;
    }

    function save()
    {
        //limpa class de erros
        clearClass('dvNome', 'inputError2Status', 'spanNome');
        clearClass('dvCpf', 'inputError3Status', 'spanCpf');
        clearClass('datetimepicker2', 'inputErrorData', 'spanData');

        //verifica se o nome esta preenchido
        if ($('[name="nome"]').val().length === 0) {
            addClassError('dvNome', 'inputError2Status', 'spanNome');
            return;
        }

        //cpf
        if ($('[name="cpf"]').val().length > 0 && !checkCPF($('[name="cpf"]').val())) {
            addClassError('dvCpf', 'inputError3Status', 'spanCpf');
            return;
        }

        $('#btnSave').text('salvando...');
        $('#btnSave').attr('disabled', true);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('pessoa/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('pessoa/ajax_update') ?>";
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
                    alert('Já está cadastrado, por favor informe outro nome.');
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

    function delete_pessoa(id)
    {
        if (confirm('Você tem certeza que deseja deletar esse registro?'))
        {

            $.ajax({
                url: "<?php echo site_url('pessoa/ajax_delete') ?>/" + id,
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

    function visto()
    {
        $.ajax({
            url: "<?php echo site_url('pessoa/visto') ?>",
            type: "POST",
            dataType: "JSON",
            success: function (data)
            {
                $('#modal_info').modal('hide');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $('#modal_info').modal('hide');
            }
        });
    }

</script>