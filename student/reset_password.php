<?php

$title_stub = 'Reset Your Password';
require_once( '../_header.inc' );

$code = $db->real_escape_string( $_GET[ 'code' ] );

print "<div class=\"success\" id=\"password_reset\"></div>\n";

$student_query = 'select s.banner, s.first '
    . 'from students as s, password_reset_request as r '
    . 'where r.banner_id = s.banner '
    . "and r.code = $code";
$student_result = $db->query( $student_query );
if( $student_result->num_rows == 0 ) {
    print 'Invalid code.';
} else {
    $student_row = $student_result->fetch_assoc( );
    print wordwrap( "<p>{$student_row[ 'first' ]}, please choose a new password.  "
        . 'The password you choose <b>must</b> conform to the following rules:</p>'
        . "\n" );
    print "<ul>\n";
    print "<li id=\"length\">It must be at least eight characters long</li>\n";
    print "<li id=\"cap\">It must contain at least one capital letter</li>\n";
    print "<li id=\"lc\">It must contain at least one lowercase letter</li>\n";
    print "<li id=\"digit\">It must contain at least one digit</li>\n";
    print "</ul>\n";
    print "<p>Please choose a password now, and type it in both of these boxes:</p>\n";
    print "<table>\n";
    print "<tr>\n";
    print "<td>Your password:</td>\n";
    print "<td><input type=\"password\" id=\"p1\" /></td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td>Your password again:</td>\n";
    print "<td><input type=\"password\" id=\"p2\" /></td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td colspan=\"2\" align=\"center\">"
        . "<input type=\"submit\" id=\"set\" value=\"Set Password\" /></td>\n";
    print "</tr>\n";
    print "</table>\n";

}
?>

<script type="text/javascript">
$(document).ready(function(){
    if ( $('input:submit').length ) {
        $('input:submit').attr('disabled', 'disabled');
    }
    
    if( $('input#p1').length ) {
        $('input#p1').focus();
    }
    
    $('input:password').keyup(function(e){
        var p1 =         $('input#p1').val();
        var p2 =         $('input#p2').val();
        var equal =      ( p1 == p2 );
        var nonempty =   ( p1 != "" );
        var length =     p1.length;
        var p1_upcase =  ( p1.match( /[A-Z]/ ) != null );
        var p2_upcase =  ( p2.match( /[A-Z]/ ) != null );
        var p1_lowcase = ( p1.match( /[a-z]/ ) != null );
        var p2_lowcase = ( p2.match( /[a-z]/ ) != null );
        var p1_digit =   ( p1.match( /[0-9]/ ) != null );
        var p2_digit =   ( p2.match( /[0-9]/ ) != null );
        
        if( length < 8 ) {
            $('li#length').css('color','red');
        } else {
            $('li#length').css('color','white');
        }
        
        if( ! p1_upcase ) {
            $('li#cap').css('color','red');
        } else {
            $('li#cap').css('color','white');
        }
        
        if( ! p1_lowcase ) {
            $('li#lc').css('color','red');
        } else {
            $('li#lc').css('color','white');
        }
        
        if( ! p1_digit ) {
            $('li#digit').css('color','red');
        } else {
            $('li#digit').css('color','white');
        }
        
        if( equal && nonempty && length >= 8 && p1_upcase && p2_upcase &&
            p1_lowcase && p2_lowcase && p1_digit && p2_digit ) {
            $('input:submit').attr('disabled','');
        } else {
            $('input:submit').attr('disabled','disabled');
        }
        
    })
    
    $('input#set').click(function(){
        var p1 = $('input#p1').val();
        var banner_id = "<?php echo $student_row[ 'banner' ]; ?>";
        $.post( 'perform_password_reset.php',
            { p1: p1, banner_id: banner_id },
            function(data){
                $('div#password_reset').html(data).slideDown();
            }
        )
    })
})
</script>
