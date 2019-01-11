$('#fName').focusout(function(){
    username = $('#userEmail').val();
    $.ajax({
        url: '../administration/vérifie-utilisateur',
        type: 'POST',
        data: 'email='+username,
    }).done(function(response){
        if(response == 'false'){
            $('#submit_new_user').prop('disabled', true);
            $('#alertUser').remove()
            $('#userEmail').after('<p id="alertUser" class="alert alert-warning">Cet utilisateur existe déjà!</p>')
        }else{
            $('#alertUser').remove()
            $('#submit_new_user').prop('disabled', false);
        }
    }).fail(function(){
        swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
    });
})

$('#sName').focusout(function(){
    username = $('#userEmail').val();
    $.ajax({
        url: '../administration/vérifie-utilisateur',
        type: 'POST',
        data: 'email='+username,
    }).done(function(response){
        if(response == 'false'){
            $('#submit_new_user').prop('disabled', true);
            $('#alertUser').remove()
            $('#userEmail').after('<p id="alertUser" class="alert alert-warning">Cet utilisateur existe déjà!</p>')

        }else{
            $('#alertUser').remove()
            $('#submit_new_user').prop('disabled', false);
        }
    }).fail(function(){
        swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
    });
})

$('#userEmail').focusout(function(){
    username = $('#userEmail').val();
    $.ajax({
        url: '../administration/vérifie-utilisateur',
        type: 'POST',
        data: 'email='+username,
    }).done(function(response){
        if(response == 'false'){
            $('#submit_new_user').prop('disabled', true);
            $('#alertUser').remove()
            $('#userEmail').after('<p id="alertUser" class="alert alert-warning">Cet utilisateur existe déjà!</p>')

        }else{
            $('#alertUser').remove()
            $('#submit_new_user').prop('disabled', false);

        }
    }).fail(function(){
        swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
    });
})

function deleteUser(id,name){
    const swalWithBootstrapButtons = swal.mixin({
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        buttonsStyling: false,
    })
    swalWithBootstrapButtons({
        title: 'Etes-vous sûre de vouloir supprimer cet utilisateur?',
        text: name,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-check"></i>',
        cancelButtonText: '<i class="fa fa-close"></i>',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    url: '../administration/utilisateurs/supprimer-utilisateur',
                    type: 'POST',
                    data: 'id='+id,
                    dataType: 'json'
                }).done(function(response){
                        swal({
                            position: 'top-end',
                            type: 'success',
                            showConfirmButton: false,
                            timer: 1500,
                            title: 'Utilisateur supprimé avec succès!'
                        })
                        $('#user'+id).remove();
                    }).fail(function(){
                    swal({
                        position: 'top-end',
                        type: 'success',
                        showConfirmButton: false,
                        timer: 1500,
                        title: 'Utilisateur supprimé avec succès!'
                    })
                    $('#user'+id).remove()                    });
            });
        },
    })
}

$('#btnAddUser').click(function(){
    $('#new-user').slideReveal('show');

})

$('#display-log').click(function(){
    $('#slider-log').slideReveal('show');
})

$('#slider-log').slideReveal({
    position: "right",
    push: false,
    overlay: true,
    width: 650
});

$('.selectpicker').selectpicker();


$('.nav-item.active').removeClass('active');
$('#nav-adm').addClass('active');

$('.slider').slideReveal({
    position: "right",
    push: false,
    overlay: true,
    width: 500
});

$('#check-password').keyup(function () {
    initial = $('#initial-password').val()
    check = $(this).val();
    if(check != initial) {
        $('#alert-pwd').remove();
        $(this).after('<div id="alert-pwd" class="alert alert-danger"><span>Les mots de passe ne correspondent pas</span></div>');
        $('#submit_new_user').prop('disabled',true);
    }else{
        $('#submit_new_user').prop('disabled',false);
        $('#alert-pwd').remove();
    }
})