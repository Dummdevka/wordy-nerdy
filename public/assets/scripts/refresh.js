// $( document ).on('reload', function() {
//     if( $( ".result_list" ).html().length()>0){
//     }
// })

$(window).on('beforeunload', function(){
    if( $( ".result" ).html() ){
        let a = $( ".result" ).html();
        sessionStorage.setItem('results', JSON.stringify(a));
    }
});

$(window).on('load', function(){
    if ( (sessionStorage.getItem('results')) != null ){
        let res = $.parseJSON(sessionStorage.getItem('results'));
        $( ".result" ).append( res );
        sessionStorage.removeItem('results');
    }
});