$('.slider').slideReveal({
    position: "right",
    push: false,
    overlay: true,
    width: 500
});

$('#display-perm').click(function(){
    $('#slider-perm').slideReveal('show');
})