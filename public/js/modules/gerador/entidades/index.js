$(document).ready(function() {
    $('.table .btn').tooltip({html: true, placement: 'top'});
    $('.bt-nova-entidade').on('click', function() {
        window.location = '/gerador/entidades/add/schema/' + $(this).data('schema') + '/table/' + $(this).data('table');
        //console.log($(this).data('schema'), $(this).data('table'));
        return false;
    });
});