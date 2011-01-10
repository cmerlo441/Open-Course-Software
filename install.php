<?php

$cwd = getcwd( );
if( preg_match( '|^(/home/faculty/)(.+)/public_html|', $cwd, $matches ) ) {
    $home_directory = $matches[ 1 ] . $matches[ 2 ];
    $username = $matches[ 2 ];
}
require_once( "$home_directory/.htpasswd" );
$docroot = "/~{$username}";
$fileroot = "$home_directory/public_html";
$admin = "$docroot/admin";
$student = "$docroot/student";
$url = "http://{$_SERVER[ 'SERVER_NAME' ]}{$docroot}/";

$prof_query = 'select * from prof';
$prof_result = $db->query( $prof_query );

if( $prof_result->num_rows == 0 ) {

    print "<html><head><title>OCSW Installation</title>\n";
    print "<script type=\"text/javascript\" "
      . "src=\"js/jquery-1.4.2.min.js\"></script>\n";
      
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

    $('input#first').focus( );

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
	var url;

    	first =    $('input#first').val();
    	last =     $('input#last').val();
    	username = $('input#username').val();
    	p1 =       $('input#pw1').val();
    	p1_md5 =   $().crypt({method:"md5",source:p1});
    
    	if (first != '' && last != '' && username != '' && p1 != '') {
            $.post('create_databases.php', {
                first: first,
                last: last,
                username: username,
                password: p1_md5
            }, function( data ) {
	        url = "<?php echo $url; ?>";
                window.location = url;
	    })
        }
    })

})
</script>

<?php

} else {
    header( 'Location: index.php' );
}

?>