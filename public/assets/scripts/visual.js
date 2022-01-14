
    $( '#ch_username' ).click(function() {
        ch_userinfo($(this), 'username');
    });
    $( '#ch_email' ).click(function() {
        ch_userinfo($(this), 'email');
    });
    $( '#ch_pass' ).click(function() {
        ch_userinfo($(this), 'password');
    });
    function userinfo_field( field, data = '' ) {
        $('#new-' + field).parent().html('');
        let str = $('<p>' + field + ':' + data + '</p>');
        let change = $('<button type="button" id="ch_' + field + '"class="dashboard__btn btn-change">Change it</button>');
        $('#dashboard-' + field ).append(str, change);
    }

    //Function to convert text strings to inputs
    function ch_userinfo(e, placeholder) {
        let block = e.parent();
        
        let new_data = $('<input type="text" name="new_' + placeholder + '" class="dashboard__input-' + placeholder
            + '"id="new-' + placeholder 
            + '"placeholder="New ' + placeholder + '">');
        let save_data = $('<button type="button" id="save_' + placeholder 
            + '"class="dashboard__btn-save">Save</button>');
        
        block.html('');
        if( placeholder == 'password'){
            let old_data = $('<input type="text" name="old_'+ placeholder 
            + '"class="dashboard__input input-' + placeholder
            + '" id="old_'+ placeholder 
            + '" placeholder="Old '+ placeholder+'">');
            block.append(old_data);
        }
        if( placeholder == 'email'){
            let old_data = $('<input type="text" name="password" class="dashboard__input-password" id="email_password" placeholder="Password">');
            block.append(old_data);
        }
        block.append(new_data);
        block.append(save_data);
    };

