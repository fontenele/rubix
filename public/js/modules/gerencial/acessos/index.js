$(document).ready(function() {
    $('#int_perfil').on('change', function() {
        if($(this).val()) {
            window.location = "/gerencial/acessos/" + $(this).val();
        }else{
            window.location = "/gerencial/acessos";
        }

        return false;
    });

    $('.selecionarTodasActions').on('click', function() {
        var classe = $(this).parent().parent().prev().children(0).attr('class');
        
        $('input.' + classe).prop("checked", true);
        return false;
    });
    
    $('.selecionarNenhumaAction').on('click', function() {
        var classe = $(this).parent().prev().parent().prev().children(0).attr('class');
        
        $('input.' + classe).attr("checked", false);
        return false;
    });
    
    $('.selecionarTodas').on('click', function() {
        $('#tb-acessos td input[type=checkbox]').prop("checked", true);
        
        return false;
    });
    
    $('.selecionarNenhuma').on('click', function() {
        $('#tb-acessos td input[type=checkbox]').prop("checked", false);
        
        return false;
    });

});