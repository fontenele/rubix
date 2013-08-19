$(document).ready(function() {
    $('form .control-group .controls *').tooltip({
        html: true,
        placement: 'right'
    });

    $('.tab-content *, .table *, form .form-actions *').tooltip({
        html: true,
        placement: 'top'
    });

    $('.btn-reset').on('click', function(){
        $(this).closest('form').find('input,option,textarea').each(function(i, item) {
            if(item.tagName == 'OPTION') {
                $(item).removeAttr('selected');
            }else{
                $(item).attr('value', '');
            }
        });
        $(this).closest('form').submit();
    });

    $(".richtext").markItUp(mySettings);
    $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
    $('.datetime').datetimepicker({
        hourGrid: 6,
        minuteGrid: 15,
        currentText: 'Agora',
        closeText: 'Selecionar',
        timeText: 'Tempo',
        hourText: 'Hora',
        minuteText: 'Minuto',
        timeText: 'Tempo',
    });

    $('.fileupload-new').on('click', function(e) {
        $('#galeriaFotos').modal({
            show: true
        });

        var ref = $(this).data('ref');

        if(!ref) {
            ref = $(this).find('.btn-file .fileupload-new').data('ref');
        }
        if(!ref) {
            ref = $(this).parent().find('.btn-file .fileupload-new').data('ref');
        }

        $('#galeria-fotos-ref-callback').val(ref);

        $('#galeriaFotos').removeClass('richtext');
        $('#galeriaFotos').addClass('fileupload');
        return false;
    });

    $('#btn-galeria-fotos-selecionar-imagem').on('click', function() {
        if($('#galeriaFotos').hasClass('fileupload')) {
            $('#galeriaFotos').modal('hide');
            $('#img_' + $('#galeria-fotos-ref-callback').val()).attr('src', $('#galeria-fotos-imagem img').attr('src'));
            $('#' + $('#galeria-fotos-ref-callback').val()).val($('#galeria-fotos-id').val());
            $('#galeriaFotos').removeClass('fileupload');
            $('#galeria-fotos-ref-callback').val('');
        }
    });

    //$(".tree-list").jstree();
});