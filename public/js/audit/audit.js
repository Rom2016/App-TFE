$('body').on('click', '.status', function() {

    if($(this).hasClass('child')){
        split = $(this).closest('.row').attr('id').split('child');
    }else{
        split = $(this).closest('.row').attr('id').split('test');
    }
    el = $(this);
    idresult = split[1];
    if($('#change-status'+idresult).text() == $(this).text()){
        return false;
    }
    audit = $(this).closest('.content').attr('id').split('audit');
    idaudit = audit[1];
    if($(this).hasClass('selection-error') || $(this).hasClass('selection-success')) {
        $('#status'+idresult).removeClass();
        if(el.hasClass('selection-error')){
            status = 'error';
            $('#status'+idresult).addClass('selection-error');
            $('#change-status'+idresult).text('passe avec erreurs');
        }
        if(el.hasClass('selection-success')){
            status = 'success';
            $('#status'+idresult).addClass('selection-success');
            $('#change-status'+idresult).text('passe');
        }
            data = {'id':idresult, 'status':status, 'audit':idaudit}
            $.ajax({
                url: '../audit/statut',
                type: 'POST',
                data: data
            }).done(function (response) {
                if ($('#group-childs'+idresult).length == 0) {
                    $('#test'+idresult).after(response);
                }
            }).fail(function () {
                swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
            });
    }else if($(this).hasClass('selection-fail')) {
        $('#status'+idresult).removeClass();
        $('#status'+idresult).addClass('selection-fail');
        $('#change-status'+idresult).text('échec');
        status = 'fail';
        data = {'id':idresult, 'status':status, 'audit':idaudit}
        $.ajax({
            url: '../audit/statut',
            type: 'POST',
            data: data
        }).done(function (response) {
            if ($('#group-childs'+idresult).length) {
                $('#group-childs'+idresult).remove();
            }
        }).fail(function () {
            swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
        });
    }
})

function updateStatus(id, status){
    data={'id':id,'status':status}
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

$('body').on('click', '.editable', function(){
    el = $(this);
    input = $('<input type="text" id="tmp-input" class="col-md-12"/>');
    if(!$(this).hasClass('new-comment')){
        input.val(el.text());
    }
    el.replaceWith( input );
    input.focus();
    input.focusout(function() {
        if ($(this).val() && $(this).val().trim().length != 0 && $(this).val() != el.text()) {
            if($(el).hasClass('child')){
                split = $(this).closest('.row').attr('id').split('child');
            }else{
                split = $(this).closest('.row').attr('id').split('test');
            }
            idresult = split[1];
            data = {'data':$(this).val(),'id':idresult}
            $.ajax({
                url: '../audit/commentaire',
                type: 'POST',
                data: data
            }).done(function (response) {
                input.replaceWith(response);
            }).fail(function () {
                swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
            });
        }else{
            if(el.hasClass('new-comment')){
                span = $('<span class="editable new-comment"/>').text('Ajouter un commentaire');
                $(this).replaceWith(span);
            }else{
                span = $('<span class="editable"/>').text($(this).val());
                $(this).replaceWith(span);
            }
        }
    })
})