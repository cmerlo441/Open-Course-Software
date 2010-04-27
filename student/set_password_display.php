<?php

$no_header = 1;
require_once( '../_header.inc' );

if( isset( $_POST[ 'p1' ] ) and isset( $_POST[ 'p2' ] ) and
    isset( $_POST[ 'student_id' ] ) ) {
    $student_id = $db->real_escape_string( $_POST[ 'student_id' ] );
    if( $_POST[ 'p1' ] == $_POST[ 'p2' ] ) {
        $update_query = 'update students '
            . "set password = md5( \"{$_POST[ 'p1' ]}\" ) "
            . "where id = $student_id";
        $update_result = $db->query( $update_query );
        
        $v_query = 'delete from student_x_verification '
            . "where student = $student_id";
        $v_result = $db->query( $v_query );
        
        print "<p>Thank you.  You may now close this window.  You will "
            . "receive another e-mail from Prof. {$prof[ 'last' ]} when "
            . "your account is finalized.  Once you receive that e-mail, "
            . "you will be able to log in.</p>\n";
    } else {
        print "<p>Those passwords do not match.  Please use your browser's "
            . "back button and try again.</p>\n";
    }
} else {
    $code = $db->real_escape_string( $_POST[ 'code' ] );
    $v_query = 'select s.id, s.first, s.middle, s.last '
        . 'from students as s, student_x_verification as x '
        . 'where x.student = s.id '
        . "and x.code = \"$code\"";
    $v_result = $db->query( $v_query );
    if( $v_result->num_rows == 0 ) {
        print 'That verification code is not recognized.';
    } else {
        $student = $v_result->fetch_assoc( );
        $student_id = $student[ 'id' ];
        print wordwrap( "<p>{$student[ 'first' ]}, you have one more task to finish "
            . "in order to complete creation of your student account.  You need to "
            . 'choose an account password.  The password you choose <b>must</b> '
            . 'conform to the following rules:</p>' . "\n" );
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
        var p2 = $('input#p2').val();
        var student_id = "<?php echo $student_id; ?>";
        $.post( 'set_password_display.php',
            { p1: p1, p2: p2, student_id: student_id },
            function(data){
                $('div#set_password').html(data);
            }
        )
    })
})
</script>

<?php
    }
}
?>
