// $( document ).on('reload', function() {
//     if( $( ".result_list" ).html().length()>0){
//     }
// })

$(window).on('beforeunload', function(){
    if( $( ".result_list" ).html() ){
        let a = $( ".result_list" ).html();
        sessionStorage.setItem('results', JSON.stringify(a));
    }
    // if( $( "#word_input" ).val().length()>0 ){
    //     let a = $( "#word_input" ).val();
    //     sessionStorage.setItem('query', JSON.stringify(a));
    // }
});

$(window).on('load', function(){
    if ( (sessionStorage.getItem('results')) != null ){
        let res = $.parseJSON(sessionStorage.getItem('results'));
        $( ".result_list" ).append( res );
        sessionStorage.removeItem('results');
    }
    // if ( (sessionStorage.getItem('query')) != null ){
    //     let res = $.parseJSON(sessionStorage.getItem('query'));
    //     $( "#word_input" ).val( res )
    //     sessionStorage.removeItem('results');
    // }
});