<?php

require_once( '../.htpasswd' );

$prof_query = 'select * from prof';
$prof_result = $db->query( $prof_query );

if( isset( $_POST[ 'username' ] ) and isset( $_POST[ 'password' ] ) ) {
    $username = $db->real_escape_string( $_POST[ 'username' ] );
    $password = $db->real_escape_string( $_POST[ 'password' ] );
    $user_query = 'insert into prof( id, username, password ) '
      . "values( null, \"$username\", md5( \"$password\" ) )";
    $user_result = $db->query( $user_query );

    if( $db->affected_rows == 1 ) {
        header( 'Location: index.php' );
        die( );
    }
}

if( $prof_result->num_rows == 0 ) {

    print "<html><head><title>OCSW Installation</title>\n";
    print "<link rel=\"icon\" type=\"image/png\" "
      . "href=\"images/ocsw.favicon.png\" />\n";
    print "<script type=\"text/javascript\" "
      . "src=\"jquery-1.4.2.min.js\"></script>\n";
    print "</head>\n\n";

    print "<body>\n";

    print "<div id=\"banner\" style=\"text-align: center; padding: 1em;\">\n";
    print "<img src=\"images/ocsw-banner.png\" width=\"865\" height=\"180\" "
      . "alt=\"OCSW Banner\" />\n";
    print "</div>  <!-- div#banner -->\n";

    print "<div id=\"install\" style=\"text-align: center\">\n";
    print "<p>Thank you for choosing OCSW.  To get started, please choose "
	. "a username and a password:</p>\n";
    print "<p id=\"p_username\">Your username: ";
    print "<input type=\"text\" id=\"username\" /></p>\n";

    print "<p id=\"p_pw1\">Your password: ";
    print "<input type=\"password\" id=\"pw1\" /></p>\n";

    print "<p id=\"p_pw2\">Your password again: ";
    print "<input type=\"password\" id=\"pw2\" /></p>\n";

    print "<p id=\"p_submit\"><input type=\"submit\" id=\"install\" "
      . "value=\"Install OCSW\"></p>\n";

    print "</div>\n";
    print "</body>\n</html>\n";

?>

<script type="text/javascript">
$(document).ready(function(){

    $('input#install').attr('disabled','true');

    $('input:pw2').keyup(function(e){
        var username =   $('input#username').val();
        var p1 =         $('input#pw1').val();
        var p2 =         $('input#pw2').val();
        var equal =      ( p1 == p2 );
        var nonempty =   ( p1 != "" && username != "" );
        
        if( equal && nonempty ) {
            $('input#install').attr('disabled','');
        } else {
            $('input#install').attr('disabled','disabled');
        }
        
    })
    
    $('input#install').click(function(){
        var username;
	var p1;
	var p2;

	username = $('input#username').val();
	p1 = $('input#pw1').val();
	p2 = $('input#pw2').val();

	$.post( 'install.php',
	    { username: username, password: p1 }
	);
   })

})
</script>

<?php

} else {
    header( 'Location: index.php' );
    die( );
}

?>