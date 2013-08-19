$.fn.updateUI = function() {
    alert('vamoss!!!');
}

$(document).ready(function() {



    $('#btn-galeria-fotos-add-imagem').on('click', function() {
        console.log('boraa');
    //$('.part-galeria').hide();
    //$('.part-cadastro').show();
    });


    $('#btn-galeria-fotos-voltar-listagem').on('click', function() {
        $('.part-galeria').show();
        $('.part-cadastro').hide();
    });

    $('#submit_imagens').on('click', function() {
        console.log('foi');
        return false;
    });
});