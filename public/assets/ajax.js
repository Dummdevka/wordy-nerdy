let headers = {
    'Content-Type' : 'application/json',
};
const base_url = 'http://localhost/wordy';
function error_mess( block, message) {
    if (block.children(":first").hasClass( "error" )){
        block.children(":first").html( message );
    } else {
    let err = $('<p class="error">'+message+'</p>');
    block.prepend( err );
    }
}
function ajax_call( method, url, callback, req_headers ) {
    $.ajax({
        url: url,
        type: method,
        success: function( res ) {
            callback( res );
        },
        headers: {req_headers}
      });
}
$("#dump_books").click(function() {
    ajax_call("POST", base_url + '/dump_lit', function ( res ) {
        console.log("Books are loaded!");
    })
})
$("#dump_web").click(function() {
    ajax_call("POST", base_url + '/dump_web', function ( res ) {
        console.log("Web examples are loaded!");
    })
})

$( "#logout" ).click(function() {
    ajax_call( "GET", base_url + '/logout', function ( res ) {
        window.location.href = base_url + '/search';
    })
});
$( "#login" ).click(function() {
    window.location.href = base_url + '/auth';
});
$( "#auth_button" ).click(function() {
    let password = $( "#password" ).val();
    let username = $( "#username" ).val();
    
    $.post( base_url + '/login', {password: password, username: username}, function( res ) {
        console.log(res);
        window.location.href = base_url + '/search';
    })
})

$( "#delete_user" ).click(function() {
    if ( $("#delete_user").val().length > 0 ) {
        ajax_call("DELETE", base_url + "/delete/" + $("#delete_user").val(), function ( res ) {
            console.log( res );
            window.location.href = base_url + '/search';
        })
    } else {
        //Check that anything left after the user is cleaned
        console.log('TODO');
    }
});

$( "#web_search" ).click(function() {
    //Changing button while waiting
    $(this).html('Waiting...');
    $(this).prev().prop('disabled', true);  

    if( $( "#word_input" ).val().length > 0 ){
        $.get( base_url + "/web-search/" + $("#word_input").val()) 
        .done(function ( res ) {
            console.log ('Success');
            $( "#web_search" ).html('Web');
            $( "#web_search" ).prev().prop('disabled', false);
            $( ".result_list" ).html('');

            let content = $.parseJSON( res );
            content.forEach(ex => {
                let block = $('<div class="web example"></div>')
                let fav = $( '<button class="add_favorite">Add to favorite</button>')
                let p_sentence = $( '<p class="web_ex_sentence">' + ex.sentence + '</p>');
                let a = $('<a href="' + ex.url + '">' + ex.url + '</a>');
                block.append(p_sentence);
                block.append(a);
                block.append(fav);
                $( ".result_list" ).append( block );
                    });
        })
        .fail( function() {
            $( "#web_search" ).html('Web');
            $( "#web_search" ).prev().prop('disabled', false);
            error_mess( $('.result_panel'), 'Nothing could be found:(' );
        })
    } else{
        error_mess( $('.result_panel'), 'Enter a word!');
    }
    
});

$( "#lit_search" ).click(function() {
    //Changing button while waiting
    $(this).html('Waiting...');
    $(this).next().prop('disabled', true); 

    if( $( "#word_input" ).val().length > 0 ){
        $.get( base_url +"/lit-search/" + $("#word_input").val())
        .done( function( res ) {
            //Changing button while waiting
            $( "#lit_search" ).html('Literature');
            $( "#lit_search" ).next().prop('disabled', false); 
            $( ".result_list" ).html('');
            let content = JSON.parse( res );
            content.forEach(ex => {
                let block = $('<div class="lit example"></div>')
                let fav = $( '<button class="add_favorite">Add to favorite</button>')
                let p_sentence = $( '<p class="lit_ex_sentence">' + ex.sentence + '<q>' + ex.title + '</q></p>');
                block.append(p_sentence);
                block.append(fav);
                $( ".result_list" ).append( block );
            });
        })
        .fail( function( res ) {
            //Changing button while waiting
            $( "#lit_search" ).html('Literature');
            $( "#lit_search" ).next().prop('disabled', false); 
            error_mess( $('.result_panel'), 'Nothing could be found:(');
        })
    } else{
        error_mess( $('.result_panel'), 'Enter a word!');
    }
    
});
$( document ).ready( function() {
    //Change user info
    $( document ).on("click", "#save_username", function() {
        if( $(this).prev().val().length > 7 ){
            $.post( base_url + "/reset_username", { new_username: $(this).prev().val() })
            .done( function( res ) {
                window.location.reload();
            })
            .fail( function( res ) {
                error_mess( $( '.profile_username' ), res.statusText);
            });
            
        } else {
            error_mess( $( '.profile_username' ), 'Please come up with something a bit longer (8 characters :-))' );
        } 
});

    $( document ).on("click", "#save_password", function() {
        if( $(this).prev().val().length > 7 ) {
            $.post( base_url + "/reset_password", { old_password: $( ".old_password").val(),
                 new_password: $( ".new_password" ).val()})
                .done( function( res ) {
                    console.log( res );
                    window.location.reload();
                })
                .fail( function( res ) {
                    error_mess( $( '.profile_password' ), res.statusText );
                })
        } else {
            error_mess( $( '.profile_password' ), 'Please come up with something a bit longer (8 characters :-))' );
        }
    })

    $( document ).on( "click", "#save_email", function() {
        if( $(this).prev().val().length > 7 ) {
            $.post( base_url + "/reset_email", { password: $( ".password").val(),
                 new_email: $( ".new_email" ).val()})
                .done( function( res ) {
                    console.log( res );
                    error_mess( $( '.profile_email' ), res );
                    $( '.profile_email' ).html() = '';
                })
                .fail( function( res ) {
                    error_mess( $( '.profile_email' ), res.statusText );
                })
        } else {
            error_mess( $( '.profile_email' ), 'An email is usually a bit longer :)' );
        }
    })

    $( document ).on( "click", '.resend_button', function() {
        $.get( base_url + '/resend')
        .done( function( res ) {
            console.log(res)
        })
        .fail( function() {
            console.log( 'something went wrong' );
        })
    })
    $( document ).on( "click", '.add_favorite', function() {
        console.log( 'here' );
        let sentence = $( this ).parent().children(":first").html();
        console.log( sentence);
        $.post( base_url + '/add_favorite', { sentence: sentence })
        .done( function( res ) {
            console.log(res)
        })
        .fail( function() {
            console.log( 'something went wrong' );
        })
    })

    $( document ).on("click", ".delete_favorite", function() {
        if ( $(".delete_favorite").val().length > 0 )
        {
            ajax_call( "DELETE", base_url + "/delete_favorite/" + $(".delete_favorite").val(), function() {
                window.location.reload();
            });
        }
    })
})
