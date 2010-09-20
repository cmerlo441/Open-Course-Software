<?php

$no_header = 1;
require_once( "../_header.inc" );

if( $_SESSION[ 'admin' ] == 1 ) {

    $update_string = $db->real_escape_string( $_POST[ 'update_string' ] );

    // Does this professor have a Twitter account?
    $twitter = null;
    $twitter_enabled_query = 'select twitter_username as u, '
	. 'twitter_password as p '
	. 'from prof';
    $twitter_enabled_result = $db->query( $twitter_enabled_query );
    if( $twitter_enabled_result->num_rows > 0 ) {
	include_once( '../twitter/class.twitter.php' );
	$twitter_creds_row = $twitter_enabled_result->fetch_assoc( );
	$u = $twitter_creds_row[ 'u' ];
	$p = $twitter_creds_row[ 'p' ];
	$twitter = new twitter( );
	$twitter->username = $u;
	$twitter->password = $p;
    }

    if( $twitter != null ) {
	print_r( $twitter );
	$result = $twitter->update( $update_string );
	print "<pre>result: $result</pre>\n";
	$status = ( $result === FALSE ? 0 : 1 );
?>

<script type="text/javascript">

$(document).ready(function(){
    var status = "<?php echo $status; ?>";
    var title;
    var text;
    if( status == 1 ) {
	title = "Tweet Sent";
	text = "Your message has been posted to Twitter.";
    } else {
	title = "Twitter Error";
	text = "Your message was not posted to Twitter.";
    }

    $.pnotify({
        pnotify_title: title,
        pnotify_text: text,
        pnotify_shadow: true,
        pnotify_type: ( status = 1 ? 'normal' : 'error' )
    })
})

</script>

<?php
    }
 }