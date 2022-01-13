//This file is used to define functions used globally
//Define base URL
const base_url = 'http://localhost/wordy';

//Function to display messages ( default - errors )
function show_mess( block, message, error = true ) {
    //Rewrite existing error message
    if (block.children(":first").hasClass( "error_message" )){
        block.children(":first").html( message );
    } else {
        let p = $('<p>'+message+'</p>');
        if ( error ) {
            p.addClass('error_message');
        } else {
            p.addClass('any_message');
        }
        block.prepend( p );
    }
}
//Function for ajax calls (delete,put)
function ajax_call( method, url, data = '' ) {
    let ajax = $.ajax({
        url: url,
        type: method,
    });
    return ajax;
}