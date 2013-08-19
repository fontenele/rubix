// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2011 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------

$(document).ready(function() {
    var booCarregouGaleriaImagens = false;

    // Configurar modal
    $('#galeriaFotos').modal({
        show: false
    }).css({ // Redimensionando modal para galeria
        width: '80%',
        'margin-left': function () {
            return -($(this).width() / 2);
        },
        top: '10px'
    }).on('shown', function() { // Evento que acontece quando o modal é apresentado
        $('.part-galeria').show();
        $('.part-cadastro').hide();
        $('#btn-galeria-fotos-add-imagem').hide();
        var selectedIndex;

        if(!booCarregouGaleriaImagens) {
            $("#tree-categorias-imagens").jstree({
                "plugins" : [ "themes", "html_data", "ui", "crrm" ]
            }).bind("select_node.jstree", function (event, data) {

                if(data) {
                    selectedIndex = data.rslt.obj.data("id");
                }else{
                    selectedIndex = $('#tree-categorias-imagens').jstree("get_selected").data('id');
                }

                $('#galeria-fotos-thumbs').html('');
                $('#galeria-fotos-imagem').html('');

                if(selectedIndex) {
                    $.getJSON('/galeria-fotos/carregarFotos/categoria/' + selectedIndex + '/thumbs/1',
                        function(data, status) {
                            $('#btn-galeria-fotos-add-imagem').show();

                            if(status == 'success') {
                                for(i in data) {
                                    var HTML = '<div class="galeria-fotos-thumbnail" data-id="';
                                    HTML+= data[i].int_cod;
                                    HTML+= '"><img src="';
                                    HTML+= data[i].url;
                                    HTML+= '" /></div>';
                                    $('#galeria-fotos-thumbs').append(HTML);
                                }

                                $('.galeria-fotos-thumbnail').on('click', function() {

                                    $('.galeria-fotos-thumbnail').removeClass('selected');
                                    $(this).addClass('selected');
                                    $.getJSON('/galeria-fotos/carregarFoto/id/' + $(this).data('id'),
                                        function(data, status) {
                                            $('#galeria-fotos-id').val(data.nomeOriginal);
                                            $('#galeria-fotos-imagem').html('<img src="' + data.url + '" />');
                                            $('#galeria-fotos-info-nome').html(data.nome);
                                            $('#galeria-fotos-info-tamanho').html(data.tamanho);
                                            $('#galeria-fotos-info-resolucao').html(data.resolucao);
                                        }
                                        );
                                });
                            }
                        });
                }else{
                    $('#btn-galeria-fotos-add-imagem').hide();
                }
            }).bind("loaded.jstree", function (event, data) {
                $('#tree-categorias-imagens').jstree('open_all');
            }).bind("refresh.jstree", function (event, data) {
                $('#tree-categorias-imagens').jstree('open_all');
            });

            // Salvar nova categoria
            $('#btn-galeria-fotos-add-categoria').on('click', function() {
                selectedIndex = $('#tree-categorias-imagens').jstree("get_selected").data('id');
                var novo;

                if(selectedIndex) {
                    _novo = $('#tree-categorias-imagens').jstree("create", "#tree-categorias-imagens" + selectedIndex, "last", "Nova categoria", false, false);

                    //_novo.attr('data-id', data.cod);
                    //_novo.attr('id', 'menu' + data.cod);
                }else{
                    _novo = $('#tree-categorias-imagens').jstree("create", -1, "last", "Nova categoria", false, false);
                }

                _novo.find('input').bind('blur', function(e) {
                    $.post("/categorias-imagens/salvarCategoria",
                        {
                            str_nome: $(this).val(),
                            int_cod_pai:selectedIndex
                        },
                        function(data, status){
                            if(status == 'success') {
                                _novo.attr('data-id', data.cod);
                                _novo.attr('id', 'menu' + data.cod);
                            }
                        },
                        'json'
                    );
                });
            });

            // Necessário para deselecionar os itens de categoria ao clicar fora das categorias
            $('#tree-categorias-imagens').on('click', function() {
                $('#btn-galeria-fotos-add-imagem').hide();

                if(!selectedIndex) {
                    $('#galeria-fotos-thumbs').html('');
                    $('#galeria-fotos-imagem').html('');
                    $('#tree-categorias-imagens').jstree("deselect_all");
                }else{
                    selectedIndex = null;
                }
            });

            // Mostrar tela de cadastro de imagem
            $('#btn-galeria-fotos-add-imagem').on('click', function() {
                selectedIndex = $('#tree-categorias-imagens').jstree("get_selected").data('id');

                if(selectedIndex) {
                    $('.part-galeria').hide();
                    $('.part-cadastro').show();
                }
            });

            // Mostrar tela de listagem de imagens
            $('#btn-galeria-fotos-voltar-listagem').on('click', function() {
                $('.part-galeria').show();
                $('.part-cadastro').hide();
            });

            // Salvar nova imagem
            $('#submit_imagens').on('click', function() {
                if($('#str_nome').val() && $('#galeria-fotos-img').val()) {

                    selectedIndex = $('#tree-categorias-imagens').jstree("get_selected").data('id');

                    var formData = new FormData($('#frm-imagens')[0]);
                    formData.append("int_categoria", selectedIndex);
                    formData.append("int_tamanho", '');
                    formData.append("dat_cadastro", '');
                    formData.append("str_nome_original", $('#galeria-fotos-img').val());

                    $.ajax({
                        type: "POST",
                        url: "/galeria-fotos/salvarFoto",
                        data: formData,
                        contentType:false,
                        processData:false,
                        success: function(data, status){
                            if(status == 'success') {
                                $('.part-galeria').show();
                                $('.part-cadastro').hide();
                                $('#frm-imagens')[0].reset();
                                $("#tree-categorias-imagens").trigger('select_node.jstree');
                            }
                        }

                    });
                }
                return false;
            });
        }

        booCarregouGaleriaImagens = true;
    });

});
var mySettings = {
    namespace : 'html',
    onShiftEnter: {
        keepDefault:false,
        replaceWith:'<br />\n'
    },
    onCtrlEnter: {
        keepDefault:false,
        openWith:'\n<p>',
        closeWith:'</p>'
    },
    onTab: {
        keepDefault:false,
        replaceWith:'    '
    },
    previewTemplatePath: '/css/markitup/templates/preview.html',
    resizeHandle: false,
    markupSet: [
    {
        name:'Colors',
        className:'colors',
        dropMenu: [

        {
            name:'Yellow',
            replaceWith:'#FCE94F',
            className:"col1-1"
        },
        {
            name:'Yellow',
            replaceWith:'#EDD400',
            className:"col1-2"
        },
        {
            name:'Yellow',
            replaceWith:'#C4A000',
            className:"col1-3"
        },
        {
            name:'Orange',
            replaceWith:'#FCAF3E',
            className:"col2-1"
        },
        {
            name:'Orange',
            replaceWith:'#F57900',
            className:"col2-2"
        },
        {
            name:'Orange',
            replaceWith:'#CE5C00',
            className:"col2-3"
        },
        {
            name:'Brown',
            replaceWith:'#E9B96E',
            className:"col3-1"
        },
        {
            name:'Brown',
            replaceWith:'#C17D11',
            className:"col3-2"
        },
        {
            name:'Brown',
            replaceWith:'#8F5902',
            className:"col3-3"
        },
        {
            name:'Green',
            replaceWith:'#8AE234',
            className:"col4-1"
        },
        {
            name:'Green',
            replaceWith:'#73D216',
            className:"col4-2"
        },
        {
            name:'Green',
            replaceWith:'#4E9A06',
            className:"col4-3"
        },
        {
            name:'Blue',
            replaceWith:'#729FCF',
            className:"col5-1"
        },
        {
            name:'Blue',
            replaceWith:'#3465A4',
            className:"col5-2"
        },
        {
            name:'Blue',
            replaceWith:'#204A87',
            className:"col5-3"
        },
        {
            name:'Purple',
            replaceWith:'#AD7FA8',
            className:"col6-1"
        },
        {
            name:'Purple',
            replaceWith:'#75507B',
            className:"col6-2"
        },
        {
            name:'Purple',
            replaceWith:'#5C3566',
            className:"col6-3"
        },
        {
            name:'Red',
            replaceWith:'#EF2929',
            className:"col7-1"
        },
        {
            name:'Red',
            replaceWith:'#CC0000',
            className:"col7-2"
        },
        {
            name:'Red',
            replaceWith:'#A40000',
            className:"col7-3"
        },
        {
            name:'Gray',
            replaceWith:'#FFFFFF',
            className:"col8-1"
        },
        {
            name:'Gray',
            replaceWith:'#D3D7CF',
            className:"col8-2"
        },
        {
            name:'Gray',
            replaceWith:'#BABDB6',
            className:"col8-3"
        },
        {
            name:'Gray',
            replaceWith:'#888A85',
            className:"col9-1"
        },
        {
            name:'Gray',
            replaceWith:'#555753',
            className:"col9-2"
        },
        {
            name:'Gray',
            replaceWith:'#000000',
            className:"col9-3"
        }
        ]
    },
    {
        name:'Heading 1',
        key:'1',
        openWith:'<h1(!( class="[![Class]!]")!)>',
        closeWith:'</h1>',
        placeHolder:'Your title here...'
    },
    {
        name:'Heading 2',
        key:'2',
        openWith:'<h2(!( class="[![Class]!]")!)>',
        closeWith:'</h2>',
        placeHolder:'Your title here...'
    },
    {
        name:'Heading 3',
        key:'3',
        openWith:'<h3(!( class="[![Class]!]")!)>',
        closeWith:'</h3>',
        placeHolder:'Your title here...'
    },
    {
        name:'Heading 4',
        key:'4',
        openWith:'<h4(!( class="[![Class]!]")!)>',
        closeWith:'</h4>',
        placeHolder:'Your title here...'
    },
    {
        name:'Heading 5',
        key:'5',
        openWith:'<h5(!( class="[![Class]!]")!)>',
        closeWith:'</h5>',
        placeHolder:'Your title here...'
    },
    {
        name:'Heading 6',
        key:'6',
        openWith:'<h6(!( class="[![Class]!]")!)>',
        closeWith:'</h6>',
        placeHolder:'Your title here...'
    },
    {
        separator:'---------------'
    },
    {
        name:'Bold',
        key:'B',
        openWith:'(!(<strong>|!|<b>)!)',
        closeWith:'(!(</strong>|!|</b>)!)'
    },
    {
        name:'Italic',
        key:'I',
        openWith:'(!(<em>|!|<i>)!)',
        closeWith:'(!(</em>|!|</i>)!)'
    },
    {
        name:'Stroke through',
        key:'S',
        openWith:'<del>',
        closeWith:'</del>'
    },
    {
        separator:'---------------'
    },
    {
        name:'Bulleted List',
        openWith:'    <li>',
        closeWith:'</li>',
        multiline:true,
        openBlockWith:'<ul>\n',
        closeBlockWith:'\n</ul>'
    },
    {
        name:'Numeric List',
        openWith:'    <li>',
        closeWith:'</li>',
        multiline:true,
        openBlockWith:'<ol>\n',
        closeBlockWith:'\n</ol>'
    },
    {
        separator:'---------------'
    },
    {
        name:'Picture',
        key:'P',
        replaceWith: function() {
            $('#galeriaFotos').modal({
                show: true
            });
            $('#galeriaFotos').removeClass('fileupload');
            $('#galeriaFotos').addClass('richtext');
        },
        beforeInsert:function(markItUp) {
        //miu. monitor("beforeInsert", markItUp)

        }
    },
    {
        name:'Link',
        key:'L',
        openWith:'<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>',
        closeWith:'</a>',
        placeHolder:'Your text to link...'
    },
    {
        separator:'---------------'
    },
    {
        name:'Clean',
        className:'clean',
        replaceWith:function(markitup) {
            return markitup.selection.replace(/<(.*?)>/g, "")
        }
    },
    {
        name:'Preview',
        className:'preview',
        call:'preview'
    }
    ]
}
