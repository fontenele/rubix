$(document).ready(function() {

    var selectedIndex;

    $('#menus-tree').jstree({
        "themes" : {
            "icons" : false
        },
        "plugins" : [ "themes", "html_data", "ui", "crrm" ]

    }).bind("select_node.jstree", function (event, data) {

        $('#int_cod').val('');
        $('#int_cod_pai').val('');
        $('#str_label').val('');
        $('#int_posicao').val('');
        $('#str_target').val('');
        $('#str_tipo').val('');

        if(data.rslt.obj.parent().parent().data('id') > 0) {
            $('#btn-add-item').attr('disabled', 'disabled');
        }else{
            if($('#btn-add-item').attr('disabled') == 'disabled') {
                $('#btn-add-item').removeAttr('disabled');
            }
        }

        selectedIndex = data.rslt.obj.data("id");

        if(selectedIndex) {
            $.getJSON('carregarMenuItem/' + selectedIndex,
                function(data, status) {
                    if(status == 'success') {
                        $('#int_cod').val(data.cod);
                        $('#int_menu_clone').val(data.codMenu);
                        $('#int_cod_pai').val(data.codPai);
                        $('#str_label').val(data.label);
                        $('#int_posicao').val(data.posicao);
                        $('#str_target').val(data.target);
                        $('#str_tipo').val(data.tipo);
                    }
                });
        }
    }).bind("loaded.jstree", function (event, data) {
        $('#menus-tree').jstree('open_all');
    }).bind("refresh.jstree", function (event, data) {
        $('#menus-tree').jstree('open_all');
    });

    $('#menus-tree').on('click', function() {
        if(!selectedIndex) {
            $('#menus-tree').jstree("deselect_all");
            $('#int_cod').val('');
            $('#int_cod_pai').val('');
            $('#str_label').val('');
            $('#int_posicao').val('');
            $('#str_target').val('');
            $('#str_tipo').val('');
        }
        selectedIndex = null;
    });

    $('#btn-add-item').on('click', function() {
        if( $(this).attr('disabled') != 'disabled' && $('#int_menu').val()) {
            // detectar se tem item selecionado por .class do que esta selecionado
            // fazer o create com um nome default para que ele altere posteriormente
            selectedIndex = $('#menus-tree').jstree("get_selected").data('id');
            //.create ( node , position , js , callback , skip_rename )
            if(selectedIndex) {

                $('#int_cod').val('');
                $('#int_cod_pai').val(selectedIndex);
                $('#str_label').val('Novo item');
                $('#int_posicao').val(99999);
                $('#str_target').val('');
                $('#str_tipo').val('item');

                $.post('/gerencial/menus/add', {
                    int_cod_pai: selectedIndex,
                    str_label: 'Novo item',
                    int_posicao: 9999,
                    int_menu: $('#int_menu').val(),
                    str_target: 'home',
                    str_tipo: 'item'
                }, function(data, status) {
                    if(status == 'success') {
                        var _novo = $('#menus-tree').jstree("create", "#menu" + selectedIndex, "last", "Novo item", false, true);
                        _novo.attr('data-id', data.cod);
                        _novo.attr('id', 'menu' + data.cod);
                    }
                }, 'json');
            }else{
                var _novo = $('#menus-tree').jstree("create", -1);
                console.log(selectedIndex, _novo);
            }
        }
        return false;
    });

    $('#submit_menus_items').on('click', function(event) {
        selectedIndex = $('#menus-tree').jstree("get_selected").data('id');

        if(selectedIndex) {
            $('#menus_items').submit();
        }else{
            return false;
        }
    });

    $('#btn-remove-item').on('click', function(event) {

        selectedIndex = $('#menus-tree').jstree("get_selected").data('id');

        if(selectedIndex) {
            $.get('delete/' + selectedIndex, //@s
                function(data, status) {
                    if(status == 'success') {
                        $('#mod-excluir-body').html(data);
                        $('#mod-excluir form').attr('action', $('#mod-excluir form').data('action-original') + selectedIndex);
                        $('#mod-excluir').modal('show');
                    }
                }
                );
        }

        return false;
    });

    $('#int_menu').on('change', function() {
        if($(this).val()) {
            window.location = "/gerencial/menus/" + $(this).val();
        }else{
            window.location = "/gerencial/menus";
        }

        return false;
    });

    $('#btn-gerar-menu').on('click', function() {
        window.location = "/gerencial/menus/gerar";
        return false;
    });

});