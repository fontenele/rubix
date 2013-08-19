$('#btn-galeria-fotos-selecionar-imagem').on('click', function() {
    if(!$('#galeriaFotos').hasClass('fileupload')) {
        $('#galeria-fotos-imagem-selecionada').val($('#galeria-fotos-imagem img').attr('src'));
        $('#galeriaFotos').modal('hide');
        $.markItUp({ replaceWith:'<img width="[![Largura (Opcional)]!]" height="[![Altura (Opcional)]!]" src="' + $('#galeria-fotos-imagem-selecionada').val() + '" title="[![Descrição (MouseOver) (Opcional)]!]" />' } );
        $('#galeriaFotos').removeClass('richtext');
    }
});

$('#btn-galeria-fotos-cancelar').on('click', function() {
    $('#galeriaFotos').modal('hide');
    $('#galeria-fotos-imagem-selecionada').val('');
});