// ---------------------------------------------
// Dump contents to the database ( admins only ) 
// ---------------------------------------------
$("#dump_books").click(function() {
    $.get( base_url + '/dump_lit' ) 
    .done( function() {
        error_mess( $("#dump_lit").parent(), 'Books are loaded!', false );
    })
    .fail( function( data, textStatus, errorThrown ) {
        let message = 'Internal error';
        if ( data.status === 404 ) {
            message = textStatus;
        } 
        error_mess( $("#dump_books").parent(), errorThrown );
    })
})

$("#dump_web").click(function() {
    $(this).val( 'Waiting...' );
    $.get( base_url + '/dump_web' ) 
    .done( function() {
        error_mess( $("#dump_web").parent(), 'Web examples are loaded!', false );
    })
    .fail( function( data, textStatus, errorThrown ) {
        if ( data.status === 404 ) {
            message = textStatus;
        } 
        error_mess( $("#dump_web").parent(), errorThrown );
    })
    .always( function() {
        $("#dump_web").val('Dump web!');
    })
})

// ---------------------------------------------
// Registration
// ---------------------------------------------
//Log in
$( "#auth_button" ).click(function() {
    let password = $( "#password" ).val();
    let username = $( "#username" ).val();
    
    $.post( base_url + '/login',
    {password: password, username: username})
    .done( function( res ) {
        //Redirect
        window.location.href = base_url + '/search';
    })
    .fail( function( data, textStatus, errorThrown ) {
        console.log( errorThrown );
        show_mess( $( ".error_message" ), errorThrown );
    })
})

//Delete user
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

// ---------------------------------------------
// Search
// ---------------------------------------------

//Web
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
            //Listing the items
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
            error_mess( $('.result_panel'), 'Nothing could be found:(' );
        })
        .always( function() {
            //Initial css
            $( "#web_search" ).html('Web');
            $( "#web_search" ).prev().prop('disabled', false);
        })
    } else{
        error_mess( $('.result_panel'), 'Enter a word!');
    }
    
});

//Literature
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
            error_mess( $('.result_panel'), 'Nothing could be found:(');
        })
        .always( function() {
            //Initial css
            $( "#lit_search" ).html('Literature');
            $( "#lit_search" ).next().prop('disabled', false); 
        })
    } else{
        error_mess( $('.result_panel'), 'Enter a word!');
    }
});

// ---------------------------------------------
// Profile dashboard
// ---------------------------------------------

$( document ).ready( function() {
    //Username
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

    //Password
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

    //Email
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

    //Resend letter
    $( document ).on( "click", '.resend_button', function() {
        $.get( base_url + '/resend')
        .done( function( res ) {
            show_mess( $( ".error_message" ), 'Confirmation letter resent!', false );
        })
        .fail( function( data, statusText, errorThrown ) {
            show_mess( $( ".error_message" ), errorThrown );

        })
    })
    $( document ).on( "click", '.add_favorite', function() {
        let sentence = $( this ).parent().children(":first").html();
        $.post( base_url + '/add_favorite', { sentence: sentence })
        .done( function( res ) {
            //add 'ok'
        })
        .fail( function() {
            console.log( 'something went wrong' );
        })
    })

// ---------------------------------------------
// Favorites
// ---------------------------------------------
    //Delete favorite
    $( document ).on("click", ".delete_favorite", function() {
        if ( $(".delete_favorite").val().length > 0 )
        {
            ajax_call( "DELETE", base_url + "/delete_favorite/" + $(".delete_favorite").val(), function() {
                window.location.reload();
            });
        }
    })
})
