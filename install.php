<?php

$cwd = getcwd( );
if( preg_match( '|^(/home/faculty/)(.+)/public_html|', $cwd, $matches ) ) {
    $home_directory = $matches[ 1 ] . $matches[ 2 ];
    $username = $matches[ 2 ];
}
require_once( "$home_directory/.htpasswd" );

if( isset( $_REQUEST[ 'first' ] ) && isset( $_REQUEST[ 'last' ] ) &&
    isset( $_REQUEST[ 'username' ] ) && isset( $_REQUEST[ 'password' ] ) ) {
        
    // Create tables as necessary
?>

<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $.post('create_databases.php');
})
</script>

<?php

    // Make sure we're not overwriting something that's there already
    
    $rows_query = 'select count( id ) as c from prof';
    $rows_result = $db->query( $rows_query );
    $rows_row = $rows_result->fetch_object( );
    if( $rows_row->c == 0 ) {
    
        $first = $db->real_escape_string( $_REQUEST[ 'first' ] );
        $last = $db->real_escape_string( $_REQUEST[ 'last' ] );
        $username = $db->real_escape_string( $_REQUEST[ 'username' ] );
        $password = $db->real_escape_string( $_REQUEST[ 'password' ] );
        $db->query( 'lock tables prof' );
        $db->query( 'truncate table prof' );
        $db->query( 'insert into prof( id, first, last, username, password ) '
        	. "values( null, \"$first\", \"$last\", \"$username\", "
        	. "\"$password\" )" );
    }
}

$prof_query = 'select * from prof';
$prof_result = $db->query( $prof_query );

if( $prof_result->num_rows == 0 ) {

    print "<html><head><title>OCSW Installation</title>\n";
    print "<script type=\"text/javascript\" "
      . "src=\"js/jquery-1.3.2.min.js\"></script>\n";
    print "</head>\n\n";

    print "<body>\n";

    print "<div id=\"banner\" style=\"text-align: center; padding: 1em;\">\n";
    print "<img src=\"images/ocsw-banner.png\" width=\"865\" height=\"180\" "
      . "alt=\"OCSW Banner\" />\n";
    print "</div>  <!-- div#banner -->\n";

    print "<div id=\"install\" style=\"text-align: center\">\n";
    print "<p>Thank you for choosing OCSW.  To get started, please enter "
	. "the following important information:</p>\n";

    print "<div id=\"real_name\" "
      . "style=\"padding: 1em; margin: 1em; border: 1px solid black; "
      . "width: 45%; float: left; background-color: #8888ff\">\n";
    print "<h1>Your Real Name</h1>\n";

    print "<p id=\"p_first\">Your first name: ";
    print "<input type=\"text\" id=\"first\" /></p>\n";

    print "<p id=\"p_last\">Your last name: ";
    print "<input type=\"text\" id=\"last\" /></p>\n";

    print "</div>\n";

    print "<div id=\"creds\" "
      . "style=\"padding: 1em; margin: 1em; border: 1px solid black; "
      . "width: 45%; float: left; background-color: #ff8888\">\n";
    print "<h1>Your OCSW Credentials</h1>\n";

    print "<p id=\"p_username\">Your username: ";
    print "<input type=\"text\" id=\"username\" /></p>\n";

    print "<p id=\"p_pw1\">Your password: ";
    print "<input type=\"password\" id=\"pw1\" /></p>\n";

    print "<p id=\"p_pw2\">Your password again: ";
    print "<input type=\"password\" id=\"pw2\" /></p>\n";

    print "</div>\n";

    print "<br clear=\"both\">\n";

    print "<p id=\"p_submit\"><input type=\"submit\" id=\"install\" "
      . "value=\"Install OCSW\"></p>\n";

    print "</div>\n";
    print "</body>\n</html>\n";

?>

<script type="text/javascript" src="js/jquery.crypt.js"></script>
<script type="text/javascript">
$(document).ready(function(){

    $('input#install').attr('disabled','true');

    $('input:text').val('');
    $('input:password').val('');

    $('input:text').keyup(function(e){
        check_inputs( );
    })

    $('input:password').keyup(function(e){
        check_inputs( );
    })

    function check_inputs() {

        var first =      $('input#first').val();
        var last =       $('input#last').val();
        var username =   $('input#username').val();
        var p1 =         $('input#pw1').val();
        var p2 =         $('input#pw2').val();
        var equal =      ( p1 == p2 );
        var nonempty =   ( p1 != "" && username != "" && first != "" &&
			   last != "" );
        
        if( equal && nonempty ) {
            $('input#install').attr('disabled','');
        } else {
            $('input#install').attr('disabled','disabled');
        }
        
    }

    $('input#install').click(function(){
        var first;
	var last;
        var username;
	var p1;

	first = $('input#first').val();
	last = $('input#last').val();
	username = $('input#username').val();
	p1 = $('input#pw1').val();
	p1_md5 = $().crypt({method:"md5",source:p1});

	if( first != '' && last != '' && username != '' && p1 != '' )
	  $(location).attr('href', 'install.php?first=' + first +
			   '&last=' + last + '&username=' + username +
			   '&password=' + p1_md5);
    })

})
</script>

<?php

} else {
    header( 'Location: index.php' );
    die( );
}

?>