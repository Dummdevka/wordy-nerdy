$( document ).ready( function() {
    $.get( base_url + '/get_favorites')
    .done ( function( res ) {
        let list = $.parseJSON( res );
        if ( typeof list === 'string' ) {
            $('.favorites_list').html(list);
        }
        list.forEach(str => {
            let block = $( '<div class="favorite_item"></div>');
            block.append($('<p class="favorite_str">' + str.sentence + '</p>'));
            block.append($('<button class="delete_favorite" value="' + str.id +'">Delete</button>'));
            $('.favorites_list').append(block);
        });
    })
    .fail ( function( res, textStatus, errorThrown ) {
        show_mess( $('.error_message'), errorThrown);
    })
})