$(window).on('beforeunload', function(){
    if( $( ".result" ).html() ){
        let a = $( ".result" ).html();
        sessionStorage.setItem('results', JSON.stringify(a));
    }
});
function gotoHASH() {
    if (location.hash) {
        if ( $.browser.webkit == false ) {
            window.location.hash = location.hash;
        } else {
            window.location.href = location.hash;
        }
    }
};

$(window).on('load', function(){
    if ( (sessionStorage.getItem('results')) != null ){
        let res = $.parseJSON(sessionStorage.getItem('results'));
        $( ".result" ).append( res );
        sessionStorage.removeItem('results');
    }
});

  
