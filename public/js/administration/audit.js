$('body').on('click', '.editable', function(){
    el = $(this);
    input = $('<input type="text" id="tmp-input" value=""/>');
    el.replaceWith( input );
    input.focus();
    if(el.hasClass('new-comment')){
        input.focusout(function() {
            if (el.hasClass('new-section')) {
                if ($(this).val() && $(this).val().trim().length != 0) {
                    $.ajax({
                        url: '../administration/contenu-audits/ajouter-section',
                        type: 'POST',
                        data: 'section=' + $(this).val()
                    }).done(function (response) {
                        $('#audit-content').append(response);
                    }).fail(function () {
                        swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
                    });
                    $(this).remove();
                } else {
                    span = $('<span class="editable new-comment new-section"/>').text('Nouvelle section');
                    $(this).replaceWith(span);
                }
            } else if (el.hasClass('new-check')) {
                if ($(this).val() && $(this).val().trim().length != 0) {
                    group = $(this).closest('.test').attr('id');
                    sub = group.split('group');
                    rq = {'type':'Question','data':$(this).val(),'sub':sub[1]};
                    $.ajax({
                        url: '../administration/contenu-audits/ajouter-test',
                        type: 'POST',
                        data: rq
                    }).done(function (response) {
                        $('#test-group'+sub[1]).append(response);
                        $('.slider').slideReveal({
                            position: "right",
                            push: false,
                            overlay: true,
                            width: 500
                        });
                    }).fail(function () {
                        swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
                    });
                    $(this).remove();
                } else {
                    span = $('<span class="editable new-comment new-check"/>').text('Nouveau test checkbox');
                    $(this).replaceWith(span);
                }
            } else if (el.hasClass('new-select')) {
                if ($(this).val() && $(this).val().trim().length != 0) {
                    group = $(this).closest('.test').attr('id');
                    sub = group.split('group');
                    rq = {'type':'Selection','data':$(this).val(),'sub':sub[1]};
                    $.ajax({
                        url: '../administration/contenu-audits/ajouter-test',
                        type: 'POST',
                        data: rq
                    }).done(function (response) {
                        $('#test-group'+sub[1]).append(response);
                        $('.slider').slideReveal({
                            position: "right",
                            push: false,
                            overlay: true,
                            width: 500
                        });
                    }).fail(function () {
                        swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
                    });
                    $(this).remove();
                } else {
                    span = $('<span class="editable new-comment new-select"/>').text('Nouveau test sélection');
                    $(this).replaceWith(span);
                }
            }
        });
    }else{
            if (el.hasClass('section')) {
                classes = el.attr('class');
                input.val(el.text());
                input.focusout(function() {
                    if($(this).val() != el.text() && $(this).val().trim().length !=0){
                        section = el.attr('id').split('section');
                        rq = {'data':$(this).val(),'rq':'section','section':section[1]};
                        $.ajax({
                            url: '../administration/contenu-audits/modifier',
                            type: 'POST',
                            data: rq
                        }).done(function (response){
                            input.replaceWith(response.html);
                            $('#section-group'+section[1]).attr('id','section-group'+response.id);
                            $('#new-sub'+section[1]).attr('id','new-sub'+response.id);
                            $('#audit-table'+section[1]).attr('id','audit-table'+response.id);
                        }).fail(function () {
                            swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
                        });
                    }else{
                        span = $('<span/>').text(input.val()).addClass(classes);
                        input.replaceWith(span);
                    }
                });
        }else if(el.hasClass('sub-section')){
                classes = el.attr('class');
                input.val(el.text());
                input.focusout(function() {
                    if($(this).val() != el.text() && $(this).val().trim().length !=0){
                        subsection = el.attr('id').split('section');
                        rq = {'data':$(this).val(),'rq':'subsection','section':subsection[1]};
                        $.ajax({
                            url: '../administration/contenu-audits/modifier',
                            type: 'POST',
                            data: rq
                        }).done(function (response) {
                            input.replaceWith(response);
                        }).fail(function () {
                            swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
                        });
                    }else{
                        span = $('<span/>').text(input.val()).addClass(classes);
                        input.replaceWith(span);
                    }
                });
            }else if(el.hasClass('test')){
                classes = el.attr('class');
                input.val(el.text());
                input.focusout(function() {
                    if($(this).val() != el.text() && $(this).val().trim().length !=0){
                        test = el.attr('id').split('test');
                        rq = {'data':$(this).val(),'rq':'test','test':test[1]};
                        $.ajax({
                            url: '../administration/contenu-audits/modifier',
                            type: 'POST',
                            data: rq
                        }).done(function (response) {
                            input.replaceWith(response);
                        }).fail(function () {
                            swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
                        });
                    }else{
                        span = $('<span/>').text(input.val()).addClass(classes);
                        input.replaceWith(span);
                    }
                });
            }

    }
})

$('body').on('click', '.add-button', function(){
    el = $(this);
    if($(this).hasClass('sub-section')){
        id =  el.closest('.section-group').attr('id');
        id = id.split('group');

        if(!$('#new-sub'+id[1]).val()){
            return false;
        }
        data = {'id':id[1],'subsection':$('#new-sub'+id[1]).val()}
        $.ajax({
            url: '../administration/contenu-audits/ajouter-sous-section',
            type: 'POST',
            data: data
        }).done(function(response){
            $('#audit-table'+data.id).append(response);
            $('#new-sub'+data.id).val('');
        }).fail(function(){
            swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
        });
    }else if($(this).hasClass('select')){
        id =  el.closest('.group-select').attr('id');
        id = id.split('select');

        if(!$('#new-select'+id[1]).val()){
            return false;
        }
        data = {'id':id[1],'select':$('#new-select'+id[1]).val()}
        $.ajax({
            url: '../administration/contenu-audits/ajouter',
            type: 'POST',
            data: data
        }).done(function(response){
            $('#group-selection'+data.id).append(response);
            $('#new-select'+data.id).val('');
        }).fail(function(){
            swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
        });
    }else if($(this).hasClass('child')){
        id =  el.closest('.group-child').attr('id');
        id = id.split('child');

        if(!$('#new-child'+id[1]).val()){
            return false;
        }
        data = {'id':id[1],'child':$('#new-child'+id[1]).val()}
        $.ajax({
            url: '../administration/contenu-audits/ajouter-enfant',
            type: 'POST',
            data: data
        }).done(function(response){
            $('#group-selection'+data.id).append(response);
            $('#new-select'+data.id).val('');
        }).fail(function(){
            swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
        });
    }

})



$('body').on('click', '.selection-choice', function(){
    id =  $(this).closest('.group-selection').attr('id');
    id = id.split('status');
    if($(this).hasClass('selection-fail')){
        data = {'id':id[1],'status':'fail'}
        $.ajax({
            url: '../administration/contenu-audits/modifier/status-selection',
            type: 'POST',
            data: data
        }).done(function(response){
            $('#group-status'+id[1]).html(response);
        }).fail(function(){
            swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
        });
  }else if($(this).hasClass('selection-error')){
        data = {'id':id[1],'status':'error'}
        $.ajax({
            url: '../administration/contenu-audits/modifier/status-selection',
            type: 'POST',
            data: data
        }).done(function(response){
            $('#group-status'+id[1]).html(response);
        }).fail(function(){
            swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
        });
    }else if($(this).hasClass('selection-success')){
        data = {'id':id[1],'status':'success'}
        $.ajax({
            url: '../administration/contenu-audits/modifier/status-selection',
            type: 'POST',
            data: data
        }).done(function(response){
            $('#group-status'+id[1]).html(response);
        }).fail(function(){
            swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
        });
    }
})


function addRow(id){

}

$('#add-section').click(function(){
    $('#audit-content').append(' <div class="section">\n' +
        '                                <span class="editable new-comment new-section">Nouvelle section</span>\n' +
        '                            </div>');
});


function changeType(id,type){
    if(type == 'check'){
        $('#type-check'+id).addClass('active');
        $('#type-select'+id).removeClass('active');
    }else{
        $('#type-select'+id).addClass('active');
        $('#type-check'+id).removeClass('active');
        $('#test-type'+id).after('<p>zddzdzd</p>');
    }
}

function slidePanel(id){
    $('#slider'+id).slideReveal('show');
}

function addTest(id,type){
    if(type == 'check'){
        $('#test-group'+id).append('<div class="">' +
            '<span class="editable new-comment new-check">Nouveau test checkbox</span>' +
            '</div>');
    }else if(type == 'select'){
        $('#test-group'+id).append('<div class="">' +
            '<span class="editable new-comment new-select">Nouveau test sélection</span>' +
            '</div>');
    }
}
