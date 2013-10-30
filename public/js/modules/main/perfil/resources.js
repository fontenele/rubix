$(document).ready(function() {

    $('#bt-select-all').on('click', function() {
        var totalCheckbox = $('#tb-resources input:checkbox').length;

        if ($('#tb-resources input:checkbox:checked').length === totalCheckbox) {
            $('#tb-resources input[type=checkbox]').removeProp('checked');
        } else {
            $('#tb-resources input[type=checkbox]').prop('checked', 'checked');
        }
    });
    
});