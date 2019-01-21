$('.slider').slideReveal({
    position: "right",
    push: false,
    overlay: true,
    width: 500
});

$('#display-perm').click(function(){
    $('#slider-perm').slideReveal('show');
})

$('#search-user').keyup(function(){
    if($(this).val()){
        data = {'search-user':$(this).val()};
        el = $(this);
        $.ajax({
            url: '../voir-audit/audit/utilisateur',
            type: 'POST',
            data: data
        }).done(function(response){
            $('#search-result').remove();
            el.after(response);
        })
    }else{
        $('#search-result').remove();
    }
})

$('body').on('click', '.select-user', function(){
    val = $(this).text();
    $('#search-result').remove();
    $('#search-user').val(val);
})

$('.audit-version').click(function () {
    split = $(this).attr('id').split('audit');
    data = {'id':split[1]};
    $.ajax({
        url: '../voir-audit/audit/enfant',
        type: 'POST',
        data: data
    }).done(function(response){
        $('#tab-audit').html(response);
    })
})

$('body').on('click', '.conclude-audit', function() {
    split = $(this).attr('id').split('conclude');
    data = {'id':split[1]};
    $.ajax({
        url: '../voir-audit/audit/conclure-audit',
        type: 'POST',
        data: data
    }).done(function(response){
        $('#tab-audit').html(response);
    })
})