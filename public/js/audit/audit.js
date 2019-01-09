$('body').on('click', '.status', function() {
    el = $(this);
    id = $(this).closest('.row').attr('id').split('test');
    id = {'id':id[1]};
    if($(this).hasClass('selection-error') || $(this).hasClass('selection-success')) {
        if (!$('#group-childs' + id.id).length) {
            $.ajax({
                url: '../audit/chercher-enfants',
                type: 'POST',
                data: id
            }).done(function (response) {
                if (response) {
                    $('#test' + id.id).after(response);
                }
            }).fail(function () {
                swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
            });
        }
        $('#status'+id.id).removeClass();
        if(el.hasClass('selection-error')){
            $('#status'+id.id).addClass('selection-error');
            $('#change-status'+id.id).text('passe avec erreurs');
        }
        if(el.hasClass('selection-success')){
            $('#status'+id.id).addClass('selection-success');
            $('#change-status'+id.id).text('passe');
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