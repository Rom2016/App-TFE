$(document).ready(function () {
    $('.btn.btn-default.submit').on('click', function(e){
        e.preventDefault();

        var $btn=$(e.currentTarget);
        var $link=$btn.attr('href');

        $.ajax({
            method: 'POST',
            url: $link
        }).done(function (data) {
            $('.btn.btn-default.submit').html(data.result);
        });
    });
});