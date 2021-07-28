<script type="text/javascript">

    var save_method;
    var table;
    var listPerson = new Array;
    var listVendor = new Array;
    var listProduct = new Array;

    $(document).ready(function () {

        //lista de clientes
        getListPerson();
        getListProduct();

        jQuery.support.cors = true;

        //inicializa select2
        $('.js-example-basic-single').select2({
            placeholder: "Selecione um Item",
            allowClear: true,
            width: '100%'
        });

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
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(), data;

                var intVal = function (i) {
                    if (typeof i === 'string') {
                        return parseFloat(i.replace('.', '').replace(',', '.').replace(/[^0-9.]/g, ''));
                    } else if (typeof i === 'number') {
                        return i;
                    }
                    return 0;
                };

                // Total over this page
                pageTotal = api
                        .column(3, {page: 'current'})
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                // Update footer
                $(api.column(3).footer()).html(
                        pageTotal.toLocaleString('pt-br', {style: 'currency', currency: 'BRL'})
                        );
            },
            "language": {
                url: "<?= base_url() ?>assets/datatables/js/locales/portuguese-brasil.json"
            },
            "processing": true,
            "serverSide": true,
            "order": [],
            // carrega os dados
            "ajax": {
                "url": "<?php echo site_url('venda/ajax_list') ?>",
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

    function add_item() {
        itemToAppend();
        $('.js-example-basic-single').select2({
            placeholder: "Selecione um item",
            allowClear: true,
            width: '100%'
        });
    }

    // lista de produtos
    function getListProduct()
    {
        $.ajax({
            url: "<?php echo site_url('venda/getProduct/') ?>",
            type: "GET",
            dataType: 'JSON',
            success: function (data)
            {
                listProduct = data;
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível buscar os produtos cadastradas');
            }
        });
    }

    // lista de vendedoras
    function getlistSalesPeople(vendedor_id)
    {
        var linha = '';
        if (vendedor_id !== undefined) {
            linha += '<input type="hidden" value="' + vendedor_id + '" name="vendedor_id[]"/>';
        } else {
            linha += '<input type="hidden" value="" name="vendedor_id[]"/>';
        }

        linha += '<div class="col-md-2 select-editable">';
        linha += '<label for="nome_vendedor[]">Vend.</label>';

        var selectVendedor = '';
        if (vendedor_id !== undefined) {
            selectVendedor = '<select onchange="getValVendor(this);" id="selectVendedor" name="nome_vendedor[]' + vendedor_id + '" class="form-control js-example-basic-single">';
        } else {
            selectVendedor = '<select onchange="getValVendor(this);" id="selectVendedor" name="nome_vendedor[]" class="form-control js-example-basic-single">';
        }

        // cria o select
        $.each(listVendor, function (key, value) {
            selectVendedor += '<option></option>';
            selectVendedor += '<option value=' + value.id + '>' + value.nome + '</option>';

        });

        linha += selectVendedor;
        linha += '</select></div>';

        return linha;
    }

    // lista de pessoas
    function getListPerson()
    {
        $.ajax({
            url: "<?php echo site_url('venda/getPersons/') ?>",
            type: "GET",
            dataType: 'JSON',
            success: function (data)
            {
                listPerson = data;

                //Preenche lista de vendedor
                $.each(data, function (key, value) {
                    if (value.is_colaborador === '1') {
                        listVendor.push(value);
                    }
                });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Não foi possível buscar os clientes cadastradas');
            }
        });
    }

    function add_venda()
    {
        // limpa o css de validação
        clearClass('datetimepicker2', 'inputErrorData', 'spanData');

        // limpa itens na aba de produtos e o id hidden da venda quando edita
        $('input[name="id"]').val('');
        $('#dvProdutos').empty();

        // mostra preloader e esconde campos
        $('#group').addClass('hidden');
        $('#loader-1').removeClass('hidden');

        // constroi o html dos selects
        htmlToAppend('selectPerson', listPerson);
//        htmlToAppend('selectProduct', listProduct);

        save_method = 'add';
        $('#form')[0].reset(); // reseta form nas modals
        $('.form-group').removeClass('has-error');
        $('.help-block').empty(); // lima string erro
        $('#modal_form').modal('show');
        $('.modal-title').text('Venda');

        $('#group').removeClass('hidden');
        $('#loader-1').addClass('hidden');
    }

    //envento que chama métodos de soma e multiplicação
    function mat(sel) {
        var valor = $(sel).closest('#linha').find('input[name="vlr[]"]').val();
        var quantidade = $(sel).closest('#linha').find('input[name="qtd[]"]').val();
        $(sel).closest('#linha').find('input[name="total[]"]').val(valor !== '' && quantidade !== '' ? multiplica(quantidade, valor) : '');
        if (valor !== '' && quantidade !== '') {
            somaTotal();
        }
    }

    // multiplica quantidade x valor
    function multiplica(quantidade, valor) {
        var resultado = parseFloat(quantidade) * parseFloat(valor);
        return resultado.toFixed(2);
    }

    //somatorio geral
    function somaTotal() {
        $('#dvProdutos').each(function () {
            var totalPoints = 0;
            $(this).find('input[name="total[]"]').each(function () {
                totalPoints += parseFloat($(this).val() !== '' ? $(this).val() : 0);
            });
            $('[name="vlr_total"]').val(totalPoints.toFixed(2));
        });
    }

    // Pega o id do select, e busca o valor na lista de produto
    function getval(sel) {
        var valor;
        var produto_id;
        $.each(listProduct, function (key, value) {
            if (value.id === sel.value) {
                valor = value.vlr;
                produto_id = value.id;
                return false;
            }
        });
        $(sel).closest('#linha').find('input[name="produto_id[]"]').val(valor !== undefined ? produto_id : '');
        $(sel).closest('#linha').find('input[name="vlr[]"]').val(valor !== undefined ? valor : '');
        $(sel).closest('#linha').find('input[name="qtd[]"]').val(valor !== undefined ? 1 : '');
        $(sel).closest('#linha').find('input[name="total[]"]').val(valor !== undefined ? multiplica(1, valor) : '');
        somaTotal();
    }

    // Pega o id do select de vendedor, e seta para ser enviado no post
    function getValVendor(sel) {
        var vendedor_id;
        $.each(listVendor, function (key, value) {
            if (value.id === sel.value) {
                vendedor_id = value.id;
                return false;
            }
        });

        $(sel).closest('#linha').find('input[name="vendedor_id[]"]').val(vendedor_id !== undefined ? vendedor_id : '');

    }

    // Deleta a linha
    function delProduto(botao) {
        var _linha = $(botao).closest('#linha');
        _linha.remove();
        somaTotal();
    }

    // Cria a linha de itens
    function itemToAppend(produto_id, qtd, vlr, vendedor_id) {
        var linha = '<div id="linha" class="produto col-md-12">';
        if (produto_id !== undefined) {
            linha += '<input type="hidden" value="' + produto_id + '" name="produto_id[]"/>';
        } else {
            linha += '<input type="hidden" value="" name="produto_id[]"/>';
        }

        linha += '<div class="col-md-2 select-editable">';
        linha += '<label for="nome_produto[]">Produto</label>';

        var selectProduto = '';
        if (produto_id !== undefined) {
            selectProduto = '<select onchange="getval(this);" id="selectProduct" name="nome_produto[]' + produto_id + '" class="form-control js-example-basic-single">';
        } else {
            selectProduto = '<select onchange="getval(this);" id="selectProduct" name="nome_produto[]" class="form-control js-example-basic-single">';
        }

        // cria o select
        $.each(listProduct, function (key, value) {
            selectProduto += '<option></option>';
            selectProduto += '<option value=' + value.id + '>' + value.nome + '</option>';
        });

        linha += selectProduto;
        linha += '</select></div>';
        linha += getlistSalesPeople(vendedor_id);
        if (qtd !== undefined) {
            linha += '<div class="col-md-2"><label for="qtd[]">Qtd.</label><input value="' + qtd + '" oninput="mat(this);" name="qtd[]" class="form-control" type="number" maxlength="11" ><span class="help-block"></span></div>';
        } else {
            linha += '<div class="col-md-2"><label for="qtd[]">Qtd.</label><input oninput="mat(this);" name="qtd[]" class="form-control" type="number" maxlength="11" ><span class="help-block"></span></div>';
        }

        //valor
        if (vlr !== undefined) {
            linha += '<div class="col-md-2"><label for="vlr[]">Valor</label><input value="' + vlr + '" oninput="mat(this);" name="vlr[]" class="form-control" type="text" maxlength="11" min="0"><span class="help-block"></span></div>';
        } else {
            linha += '<div class="col-md-2"><label for="vlr[]">Valor</label><input oninput="mat(this);" name="vlr[]" class="form-control" type="text" maxlength="11" min="0"><span class="help-block"></span></div>';
        }

        //total 
        if (vlr !== undefined && qtd !== undefined) {
            linha += '<div class="col-md-2"><label for="total[]">Total</label><input value="' + (parseFloat(vlr) * parseFloat(qtd)).toFixed(2) + '" name="total[]" class="form-control" type="text" maxlength="11" readonly="1"><span class="help-block"></span></div>';
        } else {
            linha += '<div class="col-md-2"><label for="total[]">Total</label><input name="total[]" class="form-control" type="text" maxlength="11" readonly="1"><span class="help-block"></span></div>';
        }

        //delete
        linha += '<div class="col-md-2" style="padding-top: 27px;"><a onclick="delProduto(this)" class="btn btn-sm btn-danger" title="Remover"><i class="glyphicon glyphicon-trash"></i></a></div>';
        linha += '</div>';

        $('#dvProdutos').append(linha);

        // se estiver editando seta no select2
        if (produto_id !== undefined) {
            $('[name="nome_produto[]' + produto_id + '"]').val(produto_id);
        }

        // se estiver editando seta no select2
        if (vendedor_id !== undefined) {
            $('[name="nome_vendedor[]' + vendedor_id + '"]').val(vendedor_id);
        }
    }

    // constroi o html dos selects
    function htmlToAppend(id, list)
    {
        var htmlToAppend;

        if (id === 'selectPerson') {
            htmlToAppend = '<select id="selectPerson" name="id_pessoa" class="form-control js-example-basic-single">';
            $.each(list, function (key, value) {
                htmlToAppend += '<option></option>';
                htmlToAppend += '<option value=' + value.id + '>' + value.nome + '</option>';
            });
        }
        htmlToAppend += "</select>";

        // seta o select construido
        $('#' + id).empty().html(htmlToAppend);
    }

    // remove as classes de validação
    function clearClass(dv, inputError, span) {
        $('#' + dv).removeClass('has-error has-feedback');
        $('#' + inputError).addClass('sr-only');
        $('#' + span).addClass('sr-only');
    }

    //add as classes de validaçãos
    function addClassError(dv, inputError, span) {
        $('#' + dv).addClass('has-error has-feedback');
        $('#' + span).removeClass('sr-only');
        $('#' + inputError).removeClass('sr-only');
    }

    function edit_venda(id)
    {
        // limpa o css de validação
        clearClass('datetimepicker2', 'inputErrorData', 'spanData');

        // limpa itens na aba de produtos
        $('#dvProdutos').html('');

        // mostra preloader e esconde campos
        $('#group').addClass('hidden');
        $('#loader-1').removeClass('hidden');

        $('#modal_form').modal('show');
        $('.modal-title').text('Editar Venda');
        $('#selectPerson').val(null).trigger('change');// limpa o select

        // constroi o html dos selects
        htmlToAppend('selectPerson', listPerson);

        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: "<?php echo site_url('venda/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                $('[name="id_pessoa"]').val(data.pessoa_id).trigger('change');
                $('[name="id"]').val(data.id);
                $('[name="vlr_total"]').val(data.vlr_total);
                $('[name="observacao"]').val(data.observacao);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                // remove preloader e mostra campos
                $('#group').removeClass('hidden');
                $('#loader-1').addClass('hidden');

                alert('Não foi possível buscar os dados, tente novamente');
            }
        });

        $.ajax({
            url: "<?php echo site_url('venda/getItens/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                // value = item
                if (data !== null) {
                    $.each(data, function (key, value) {
                        itemToAppend(value.produto_id, value.qtde, value.vlr, value.vendedor_id);
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                // remove preloader e mostra campos
                $('#group').removeClass('hidden');
                $('#loader-1').addClass('hidden');

                alert('Não foi possível buscar os dados, tente novamente');
            }
        });

        // remove preloader e mostra campos
        $('#group').removeClass('hidden');
        $('#loader-1').addClass('hidden');
    }

    function reload_table()
    {
        table.ajax.reload(null, false); //recarrega datatable  
    }

    // valida campos obrigatórios
    function validation()
    {
        //verifica se o nome esta preenchido
        if (parseFloat($('[name="vlr_total"]').val()) === 0) {
            alert('Não foi adicionado nenhum produto');
            return false;
        }

        return true;
    }

    function save()
    {
        // valida campos vazios
        if (validation()) {

            $('#btnSave').text('salvando...');
            $('#btnSave').attr('disabled', true);
            var url;

            if (save_method === 'add') {
                url = "<?php echo site_url('venda/ajax_add') ?>";
            } else {
                url = "<?php echo site_url('venda/ajax_update') ?>";
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
    }

    function delete_venda(id)
    {
        if (confirm('Você tem certeza que deseja deletar esse registro?'))
        {

            $.ajax({
                url: "<?php echo site_url('venda/ajax_delete') ?>/" + id,
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