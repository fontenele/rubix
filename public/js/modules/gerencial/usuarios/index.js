$(document).ready(function() {
    
    //window.location = $('#dg').attr('href');
    
    $('.lnk-excluir').on('click',function() {
        var cod = $.trim($(this).closest('tr').find('td:first').text());
        $.get('usuarios/delete/' + cod, //@s
            function(data, status) {
                if(status == 'success') {
                    $('#mod-excluir-body').html(data);
                    $('#mod-excluir form').attr('action', $('#mod-excluir form').data('action-original') + cod);
                    $('#mod-excluir').modal('show');
                }
            }
        );
    });
});