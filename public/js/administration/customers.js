$("input:file").change(function (){
    var id = $(this).attr('id').split('contract');
    var file_data = $(this).prop('files')[0];
    var contract = new FormData();
    contract.append('file', file_data);
    contract.append('id', id[1]);
    input = $(this);
alert(id[1])
    $.ajax({
        url: '../administration/clients/enregistrer-contrat', // point to server-side PHP script
        dataType: 'text',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: contract,
        type: 'post',
        success: function(response){
            $('.input').remove();
            input.before(response);
        }
    });
});