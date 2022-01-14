$( document ).ready( function() {
    $.get( base_url + '/get_favorites')
    .done ( function( res ) {
        let list = $.parseJSON( res );
        if ( typeof list === 'string' ) {
            $('.favorites__list').html(list);
        }
        list.forEach(str => {
            let block = $( '<div class="favorites__item"></div>');
            block.append($('<p class="favorites__str">' + str.sentence + '</p>'));
            block.append($('<button class="favorites__btn btn-delete" id="delete-favorite" value="' + str.id +'">Delete</button>'));
            $('.favorites__list').append(block);
        });
    })
    .fail ( function( res, textStatus, errorThrown ) {
        show_mess( $('.favorites__span-message'), errorThrown);
    })
})