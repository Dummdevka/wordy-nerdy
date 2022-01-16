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
        window.location.href = base_url;
    })
    .fail( function( data, textStatus, errorThrown ) {
        console.log( errorThrown );
        show_mess( $( ".auth__span-message" ), errorThrown );
    })
})

//Delete user
$( "#delete_user" ).click(function() {
    if ( $("#delete_user").val().length > 0 ) {
        ajax_call("DELETE", base_url + "/delete/" + $("#delete_user").val(), function ( res ) {
            console.log( res );
            window.location.href = base_url;
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
$( ".search__btn-web" ).click(function() {
    if( $( ".search__input-word" ).val().length > 0 ){
        //Changing button while waiting
        $(this).html('Waiting...');
        $(this).prev().prop('disabled', true);

        $.get( base_url + "/web-search/" + $(".search__input-word").val()) 
        .done(function ( res ) {
            console.log ('Success');
            $( ".search__btn-web" ).html('Web');
            $( ".search__btn-web" ).prev().prop('disabled', false);
            $( ".result" ).html('');

            // res is an array of sentences ( each has [url, sentence ])
            let content = $.parseJSON( res );
            //Listing the items
            content.forEach(ex => {
                //Example is shared class for all sentences
                let block = $('<div class="result__div-lit"></div>')
                let fav = $( '<button class="result__btn-favorite">Add to favorite</button>')
                let p_sentence = $( '<p id="example" class="result__p-web">' + ex.sentence + '</p>');
                let a = $('<a href="' + ex.url + '">' + ex.url + '</a>');
                //The sentence itself
                block.append(p_sentence);
                //Link to the website
                block.append(a);
                //"Favorite" button
                block.append(fav);

                $( ".result" ).append( block );
                    });
        })
        .fail( function( data, textStatus, errorThrown ) {
            //Error message
            show_mess( $('.search__span-message'), errorThrown );
        })
        .always( function() {
            //Initial css
            $( ".search__btn-web" ).html('Web');
            $( ".search__btn-web" ).prev().prop('disabled', false);
        })
    } else{
        //Get rid of all the examples
        $(".result").html('');
        show_mess( $('.search__span-message'), 'Enter a word!');
    }
});

//Literature
$( ".search__btn-lit" ).click(function() {
    if( $( ".search__input-word" ).val().length > 0 ){
        //Changing button while waiting
        $(this).html('Waiting...');
        $(this).next().prop('disabled', true); 

        $.get( base_url +"/lit-search/" + $(".search__input-word").val())
        .done( function( res ) {
            //Changing button while waiting
            $( ".search__btn-lit" ).html('Literature');
            $( ".search__btn-lit" ).next().prop('disabled', false); 
            $( ".result" ).html('');
            let content = JSON.parse( res );
            if (content instanceof Array ) {
                content.forEach(ex => {
                    let block = $('<div class="result__div-lit"></div>')
                    let fav = $( '<button class="result__btn-favorite">Add to favorite</button>')
                    let p_sentence = $( '<p id="example" class="result__p-lit">' + ex.sentence + '<q>' + ex.title + '</q></p>');
                    block.append(p_sentence);
                    block.append(fav);
                    $( ".result" ).append( block );
                })
            }
            else {
                $( ".result" ).append( JSON.stringify(content) );
            };
        })
        .fail( function( data, textStatus, errorThrown ) {
            show_mess( $('.search__span-message'), errorThrown);
        })
        .always( function() {
            //Initial css
            $( ".search__btn-lit" ).html('Literature');
            $( ".search__btn-lit" ).next().prop('disabled', false); 
        })
    } else{
        show_mess( $('.search__span-message'), 'Enter a word!');
    }
});

// ---------------------------------------------
// Profile dashboard
// ---------------------------------------------

$( document ).ready( function() {
    //Username
    $( document ).on("click", "#save-username", function() {
        if( $(this).prev().val().length > 7 ){
            let username = $(this).prev().val();
            $.post( base_url + "/reset_username", { new_username:  username})
            .done( function( res ) {
                //Remove input
                //Construct username filed back
                userinfo_field( 'username', username );
                //Show success message
                show_mess( $('.dashboard__span-message'), 'Username changed!', false);
            })
            .fail( function( data, textStatus, errorThrown ) {
                show_mess( $('.dashboard__span-message'), errorThrown);
            })
        } else {
            show_mess( $( '.dashboard__span-message' ), 'Please come up with something a bit longer (8 characters :-))' );
        } 
    });

    //Password
    $( document ).on("click", "#save-password", function() {
        let old_pass = $('#new-password').val();
        let new_pass = $('#new-password').val();
        if( old_pass.length > 7 ) {
            $.post( base_url + "/reset_password", { old_password: old_pass,
                 new_password: new_pass})
                .done( function( res ) {
                    userinfo_field( 'password' );
                    show_mess( $( '.dashboard__span-message' ), 'Password changed!', false );
                })
                .fail( function( res, textStatus, errorThrown ) {
                    show_mess( $( '.dashboard__span-message' ), errorThrown );
                })
        } else {
            show_mess( $( '.dashboard__span-message' ), 'Please come up with something a bit longer (8 characters :-))' );
        }
    })

    //Email
    $( document ).on( "click", "#save-email", function() {
        let password = $('#email-password').val();
        let new_email = $('#new-email').val();
        if( new_email.length > 7 || password.length > 1) {
            $.post( base_url + "/reset_email", { password: password,
                 new_email: new_email})
                .done( function( res ) {
                    userinfo_field( 'email', 'Confirm');
                    show_mess( $( '.dashboard__span-message' ), 'Confirm your email!', false);
                })
                .fail( function( res, textStatus, errorThrown ) {
                    show_mess( $( '.dashboard__span-message' ), errorThrown );
                })
        } else {
            show_mess( $( '.dashboard__span-message' ), 'An email is usually a bit longer :)' );
        }
    })

    //Resend letter
    $( document ).on( "click", '.button-resend', function() {
        $.get( base_url + '/resend')
        .done( function( res ) {
            // //Append icon
            // let i = $('<i class="far fa-thumbs-up"></i>');
            // //Hide in 4 seconds
            // setTimeout(function() {
            //     $('.fa-thumbs-up').fadeOut('fast');
            // }, 4000);
            // $('.button-resend').append(i);
            thumb_up( $('.button-resend') );
        })
        .fail( function( data, statusText, errorThrown ) {
            show_mess( $( ".confirm__span-message" ), errorThrown );
        })
    })

// ---------------------------------------------
// Favorites
// ---------------------------------------------

    $( document ).on( "click", '.result__btn-favorite', function() {
        let sentence = $( this ).parent().children(':first').html();
        let button = $( this );
        $.post( base_url + '/add_favorite', { sentence: sentence })
        .done( function( res ) {
            thumb_up( button, 2000 );
            // show_mess( $( ".search__span-message" ), 'Sentence has been added', false );
        })
        .fail( function( data, textStatus, errorThrown ) {
            show_mess( $( ".search__span-message" ), errorThrown );
        })
    })


    //Delete favorite
    $( document ).on("click", "#delete-favorite", function() {
        let block = $(this).parent();
        if ( $("#delete-favorite").val().length > 0 )
        {
            let ajax = ajax_call( "DELETE", base_url + "/delete_favorite/" + $("#delete-favorite").val());
            ajax.done( function() {
                block.html('');
            });
            ajax.fail( function( data, textStatus, errorThrown ) {
                show_mess( $( ".favorites__span-message", errorThrown ));
            })
        }
    })
})
