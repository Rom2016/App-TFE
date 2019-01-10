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
                })
                    .done(function(response){
                        swal({
                            position: 'top-end',
                            type: 'success',
                            showConfirmButton: false,
                            timer: 1500,
                            title: 'Utilisateur supprimé avec succès!'
                        })
                        $('#user'+id).remove();
                    })
                    .fail(function(){
                        swal('Oops...', 'Un problème est survenu, réessayez plus tard!', 'error');
                    });
            });
        },
    })
}

$('#btnAddUser').click(function(){
    $('#new-user').slideReveal('show');

})
