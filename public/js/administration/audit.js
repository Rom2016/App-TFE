$('body').on('click', '.editable', function(){
    el = $(this);
    classes = el.attr('class');
    input = $('<input name="lol"/>');
    input.val(el.text());
    el.replaceWith( input );

    var save = function(){
        span = $('<span/>').text(input.val()).addClass(classes);
        input.replaceWith( span );
    };
    /**
     We're defining the callback with `one`, because we know that
     the element will be gone just after that, and we don't want
     any callbacks leftovers take memory.
     Next time `p` turns into `input` this single callback
     will be applied again.
     */
    input.one('blur', save).focus();
})

function addRow(){
    $('#audit-table').after('<div class="row">\n' +
        '                            <div class="sub-section comment col-md-2">\n' +
        '                                <span class="editable suggestion new-comment">Ajouter une sous-catégorie</span>\n' +
        '                            </div>\n' +
        '                            <div class="comment col-md-6">\n' +
        '                                <span class="editable suggestion new-comment">Ajouter un test</span>\n' +
        '                            </div>\n' +
        '                            <div class="">\n' +
        '                                <select class="form-control" name="test_phase[1][prio]" id="sel1">\n' +
        '                                    <option>1</option>\n' +
        '                                    <option>2</option>\n' +
        '                                    <option>3</option>\n' +
        '                                </select>\n' +
        '                            </div>\n' +
        '                        </div>')
}
