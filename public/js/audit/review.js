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
/*
$('#search-user').autocomplete({
    source : function(requete, reponse){ // les deux arguments représentent les données nécessaires au plugin
        $.ajax({
            url : 'http://ws.geonames.org/searchJSON', // on appelle le script JSON
            dataType : 'json', // on spécifie bien que le type de données est en JSON
            data : {
                name_startsWith : $('#recherche').val(), // on donne la chaîne de caractère tapée dans le champ de recherche
                maxRows : 15
            },

            success : function(donnee){
                reponse($.map(donnee.geonames, function(objet){
                    return objet.name + ', ' + objet.countryName; // on retourne cette forme de suggestion
                }));
            }
        });
    }
});
*/
$('.selectpicker').selectpicker();

