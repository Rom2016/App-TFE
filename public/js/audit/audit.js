$('body').on('click', '.status', function() {
    el = $(this);
    split = $(this).closest('.row').attr('id').split('test');
    id = {'id':split[1]}

    if($(this).hasClass('selection-error') || $(this).hasClass('selection-success')) {
        $('#status'+id.id).removeClass();
        if(el.hasClass('selection-error')){
            status = 'error';
            $('#status'+id.id).addClass('selection-error');
            $('#change-status'+id.id).text('passe avec erreurs');
        }
        if(el.hasClass('selection-success')){
            status = 'success';
            $('#status'+id.id).addClass('selection-success');
            $('#change-status'+id.id).text('passe');
        }
        if (!$('#group-childs' + id.id).length) {
            data = {'id':split[1], 'status':status}
            $.ajax({
                url: '../audit/statut',
                type: 'POST',
                data: data
            }).done(function (response) {
                if (response) {
                    $('#test' + id.id).after(response);
                }
            }).fail(function () {
                swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
            });
        }


    }else if($(this).hasClass('selection-fail')) {
        if ($('#group-childs'+id.id).length) {
            $('#group-childs'+id.id).remove();
        }
        $('#status'+id.id).removeClass();
        $('#status'+id.id).addClass('selection-fail');
        $('#change-status'+id.id).text('échec');
    }
})

$('body').on('click', '.editable', function(){
    el = $(this);
    input = $('<input type="text" id="tmp-input" class="col-md-12" value=""/>');
    el.replaceWith( input );
    input.focus();
    input.focusout(function() {
        if ($(this).val() && $(this).val().trim().length != 0) {

        }else{
            if(el.hasClass('new-comment')){
                span = $('<span class="editable new-comment"/>').text('Ajouter un commentaire');
                $(this).replaceWith(span);
            }else{
                span = $('<span class="editable new-comment"/>').text($(this).val());
                $(this).replaceWith(span);

            }

        }
    })
})