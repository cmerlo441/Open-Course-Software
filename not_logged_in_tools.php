<?php

$no_header = 1;
require_once( '_header.inc' );

$fail_text = "<h2>Login Failure</h2>"
    . "<p>Your username and/or password were not found in the system.</p>";

?>

<p id="top">You are not logged in.<br />
<a href="javascript:void(0)" id="show_login_dialog">Log in now</a> or
<a href="javascript:void(0)" id="show_create_dialog">create an account</a>.<br />
<a href="javascript:void(0)" id="show_forgot_password_dialog">Did you forget your
password?</a></p>

</div> <!-- #login -->

<div class="dialog" id="login_dialog" title="Log In">
</div>  <!-- div#login_dialog -->

<div class="dialog" id="create_dialog" title="Create An Account">
</div>  <!-- div#create_dialog -->

<div class="dialog" id="forgot_password_dialog" title="Reset My Password">
</div>

<div class="dialog" id="forgot_confirm_dialog" title="Check Your E-Mail">
</div>

<div class="dialog" id="password_needed_dialog" title="Check Your E-Mail">
</div>  <!-- div#password_needed_dialog -->

<script type="text/javascript">;
$(document).ready(function(){
    $("div.failure").hide();

    $("a#show_login_dialog").click(function(){
        
        $.get('login_dialog.html',
            function(data){
                $('div#login_dialog').html(data);
                $('input#username').focus();
            }
        )

        $("div#login_dialog").dialog({
            autoOpen: true,
            hide: 'puff',
            modal: true,
            buttons: {
                'Log In': function(){
                    $.post( 'login.php', 
                    {
                        username: $("div#login_dialog input#username").val(),
                        password: $("div#login_dialog input#password").val()
                    },
                    function(data){
                        if( data == 'admin' ) {
                            $("div#login_dialog").dialog('destroy');
                            $.post( 'admin_tools.php',
                                function(data){
                                    $("div#not_logged_in").fadeOut(500);
                                    $("div#admin").html(data).fadeIn(500);
                                }
                            )
		        } else if( data == 'inactive' ) {
			    $("input[id=username]").select();
			    $("span#error_message").html("You are not active in any classes.");
			    $("div#login_error").fadeIn(500);
                        } else if( data == 'student' ) {
                            $("div#login_dialog").dialog('destroy');
                            $.post( 'student_tools.php',
                                function(data){
                                    $("div#not_logged_in").fadeOut( 500 );
                                    $("div#student").html(data).fadeIn( 500 );
                                }
                            )
                        } // else...
                        else if( data == 'none' ) {
                            $("input[id=username]").select();
			    $("span#error_message").html("This combination of username and password was not found in the system.  Please check your spelling and try again.");
                            $("div#login_error").fadeIn(500);
                        }
                    })
                },
                'Cancel': function(){
                    $(this).dialog('destroy');
                }
            }
        });
    })
    
    $("a#show_create_dialog").click(function(){
        
        $.post('create_account_dialog.php',
            function(data) {
                $('div#create_dialog').html(data);
                $('input#first').focus();
            }
        )
    
        $("div#create_dialog").dialog({
            autoOpen: true,
            hide: 'puff',
            modal: true,
            width: 400,
            buttons: {
                'Create Account': function(){
                    var first = $("input[id=first]").val();
                    var middle = $("input[id=middle]").val();
                    var last = $("input[id=last]").val();
                    var email = $("input[id=email]").val();
                    var banner = $("input[id=banner]").val();
                    
                    var error = false;
                    
                    if( banner == '' ) {
                        $("input#banner").select();
                        $("td#banner").css({
                            "color": "#cd0a0a",
                            'font-weight': 'bold'
                        });
                        error = true;
                    }
                    if( email == '' ) {
                        $("input#email").select();
                        $("td#email").css({
                            "color": "#cd0a0a",
                            'font-weight': 'bold'
                        });
                        error = true;
                    }
                    if( last == '' ) {
                        $("input#last").select();
                        $("td#last").css({
                            'color': '#cd0a0a',
                            'font-weight': 'bold'
                        });
                        error = true;
                    }
                    if( first == '' ) {
                        $("input#first").select();
                        $("td#first").css({
                            'color': '#cd0a0a',
                            'font-weight': 'bold'
                        });
                        error = true;
                    }
                    if( error == true ) {
                        $("div#create_error").fadeIn(500);
                    }
    
                    var sections = '';
    
                    if( $(":checked").size() == 0 ) {
                        error = true;
                        $("div#courses_error").fadeIn(500);
                        $("p#courses").css({
                            'color': '#cd0a0a',
                            'font-weight': 'bold'
                        })
                    } else {
                        $(':checked').each(function(){
                            var section = $(this).attr('id');
                            sections += section + ',';
                        })
                    }
    
                    if( error == false ) {
                        $.post( 'create_account.php',
                            {
                                first: first,
                                middle: middle,
                                last: last,
                                email: email,
                                banner: banner,
                                sections: sections
                            }, function( data ) {
                                if( data == 'Good' ) {
                                    
                                    $(':text').val('');
                
                                    $('div#create_dialog').dialog('destroy');
                                    
                                    $.post( 'password_needed_dialog.php',
                                        { first: first, email: email },
                                        function(data){
                                            $('div#password_needed_dialog').html(data);
                                        }
                                    )
                                    
                                    $("div#password_needed_dialog").dialog({
                                        autoOpen: true,
                                        hide: 'puff',
                                        modal: true,
                                        buttons: {
                                            'OK': function(){
                                                $("div#password_needed_dialog").dialog('destroy');
                                            }
                                        }
                                    })
                                } else if( data == 'Malformed Banner' ) {
                                    
                                    $(':text').val('');
                
                                    $('div#create_dialog').dialog('destroy');
                                    
                                    $.post( 'bad_banner.php',
                                        { first: first, email: email },
                                        function(data){
                                            $('div#password_needed_dialog').html(data);
                                        }
                                    )
                                    
                                    $("div#password_needed_dialog").dialog({
                                        autoOpen: true,
                                        hide: 'puff',
                                        modal: true,
                                        buttons: {
                                            'OK': function(){
                                                $("div#password_needed_dialog").dialog('destroy');
                                            }
                                        }
                                    })
                                    
                                } else if( data == 'Used Banner' ) {
                                    
                                    $(':text').val('');
                
                                    $('div#create_dialog').dialog('destroy');
                                    
                                    $.post( 'used_banner.php',
                                        { first: first, email: email },
                                        function(data){
                                            $('div#password_needed_dialog').html(data);
                                        }
                                    )
                                    
                                    $("div#password_needed_dialog").dialog({
                                        autoOpen: true,
                                        hide: 'puff',
                                        modal: true,
                                        buttons: {
                                            'OK': function(){
                                                $("div#password_needed_dialog").dialog('destroy');
                                            }
                                        }
                                    })
                                    
                                } else {
                                    
                                    $(':text').val('');
                
                                    $('div#create_dialog').dialog('destroy');
                                    
                                    $.post( 'other_new_account_problem.php',
                                        { first: first, email: email },
                                        function(data){
                                            $('div#password_needed_dialog').html(data);
                                        }
                                    )
                                    
                                    $("div#password_needed_dialog").dialog({
                                        autoOpen: true,
                                        hide: 'puff',
                                        modal: true,
                                        buttons: {
                                            'OK': function(){
                                                $("div#password_needed_dialog").dialog('destroy');
                                            }
                                        }
                                    })
                                }
                            }

                        );

                    }
                    
                },
                'Cancel': function(){
                    $('div#create_dialog').dialog('destroy');
                }
            }
        });
    })
    
    $('a#show_forgot_password_dialog').click(function(){
        $.post( 'forgot_password_dialog.php',
            function(data){
                $('div#forgot_password_dialog').html(data);
                $('input#banner_id').focus();
            }
        )
        
        $('div#forgot_password_dialog').dialog({
            autoOpen: true,
            hide: 'puff',
            modal: true,
            buttons: {
                'OK': function(){
                    var banner = $('input#banner_id').val();
                    $(this).dialog('destroy');
                    $.post( 'send_forgot_password_link.php',
                        { banner: banner },
                        function(data){
                            $('div#forgot_confirm_dialog').html(data).dialog({
                                autoOpen: true,
                                hide: 'puff',
                                modal: true,
                                buttons: {
                                    'OK': function(){
                                        $(this).dialog('destroy');
                                    }
                                }
                            });
                        }
                    );
                },
                'Cancel': function(){
                    $(this).dialog('destroy');
                }
            }
        })
    })
})</script>
