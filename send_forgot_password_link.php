<?php

$no_header = 1;
require_once( './_header.inc' );

for( $i = 0; $i < 5; $i++ ) {
    $code .= mt_rand( 1, 999 );
}

$banner = strtolower( $db->real_escape_string( $_POST[ 'banner' ] ) );
$name_query = 'select first from students where banner = "' . $banner . '"';
$name_result = $db->query( $name_query );
if( $name_result->num_rows == 0 ) {
    print "Error: Unrecognized N number $banner.";
} else {
    $name_row = $name_result->fetch_assoc( );
    $name = $name_row[ 'first' ];
    
    $insert_query = 'insert into password_reset_request (id, banner_id, code ) '
        . "values( null, \"$banner\", \"$code\" )";
    $insert_result = $db->query( $insert_query );

    if( $db->affected_rows == 1 ) {
    
      $message = <<<MESSAGE
Hello, $name.  You recently requested a password reset.  To complete
the process, please visit this link:

http://www.matcmp.ncc.edu/$docroot/student/reset_password.php?code=$code
    
If you did not request this change, there is no need to worry; nothing
has happened.  Just ignore this message.
MESSAGE;
    
      $headers = "From: {$prof[ 'name' ]} <{$prof[ 'email' ]}>\n";
    
      mail( "$banner@students.ncc.edu", 'Password Reset Requested',
	    $message, $headers );
    
      print 'Check your NCC student email.  A link has been sent.';
    } else {
      print 'There was a problem completing your request.  Please let your '
	. 'instructor know as soon as possible.';
    }
}
?>
