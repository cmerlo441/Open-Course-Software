<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $from = stripslashes( trim( $_POST[ 'from' ] ) );
    $to = stripslashes( trim( $_POST[ 'to' ] ) );
    $subject = stripslashes( trim( $_POST[ 'subject' ] ) );
    $message = stripslashes( trim( wordwrap( $_POST[ 'message' ] ) ) );
    
    $cc_query = 'select v from ocsw where k = "cc"';
    $cc_result = $db->query( $cc_query );
    $cc_row = $cc_result->fetch_assoc( );
    $cc = $cc_row[ 'v' ];
    
    $qotd_query = 'select v from ocsw where k = "qotd-email"';
    $qotd_result = $db->query( $qotd_query );
    $qotd_row = $qotd_result->fetch_assoc( );
    $qotd = $qotd_row[ 'v' ];

    $headers = "From: $from\n";
    if( $cc == 1 ) {
        $headers .= "Bcc: $from\n";
    }
    $headers .= "Reply-To: $from\n"
	. "X-Mailer: OCSW Version {$ocsw[ 'version' ]}\n";
    
    if( $qotd == 1 ) {
        $qotd_query = 'select * from quotes order by rand() limit 1';
        $qotd_result = $db->query( $qotd_query );
        $qotd_row = $qotd_result->fetch_assoc( );
        $message .= "\n\n--\n{$qotd_row[ 'quote' ]}";
        if( $qotd_row[ 'attribution' ] != '' ) {
            $message .= "\n - {$qotd_row[ 'attribution' ]}";
        }
        $message .= "\n";
    }
    
    // Send the mail
    
    $sent = mail( $to, $subject, $message, $headers );

    if( $sent === true ) {

	// Look up student_x_section
    
	$x_query = 'select id from student_x_section '
	    . 'where student = '
	    . $db->real_escape_string( $_POST[ 'student_id' ] )
	    . ' and section = '
	    . $db->real_escape_string( $_POST[ 'section' ] );
	$x_result = $db->query( $x_query );
	$x_row = $x_result->fetch_assoc( );
	$x = $x_row[ 'id' ];
    
	// Add the mail to the database
    
	$insert_query = 'insert into mail_to_students '
	    . '( id, student_x_section, subject, message, datetime ) '
	    . 'values '
	    . "( null, $x, \"" . $db->real_escape_string( $subject )
	    . "\", "
	    . '"' . $db->real_escape_string( $message ) . '", '
	    . '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$insert_result = $db->query( $insert_query );

	$rows = $db->affected_rows;
	print $rows;
    } else {
	print 0;
    }
}
   
?>
