

$( ".query_word" ).click(function() {
    
});

$( "#web_search" ).click(function() {
    if( $( "#word_input" ).val().length > 0 ){
        $.ajax({
            url: "http://localhost/wordy/web-search/"+$( "#word_input" ).val(),
            type: "GET",
            success: function (res) {
                console.log(JSON.parse(res));
                $( ".result_list" ).html(JSON.parse(res));
            }
          });
    } else{
        alert($( "#word_input" ).val());
    }
    
});

$( "#lit_search" ).click(function() {
    if( $( "#word_input" ).val().length > 0 ){
        $.ajax({
            url: "http://localhost/wordy/lit-search/"+$( "#word_input" ).val(),
            type: "GET",
            success: function (res) {
                console.log(JSON.parse(res));
                $( ".result_list" ).html(res);
            }
          });
    } else{
        alert("Enter a word!");
    }
    
});