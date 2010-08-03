<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {

    foreach( explode( ',', 'student,section,section_name,subject,replyto,message' ) as $field ) {
        $send[ $field ] = stripslashes( trim( wordwrap( $_POST[ $field ] ) ) );
        $insert[ $field ] = $db->real_escape_string( $send[ $field ] );
    }
    
    $student_name = $_SESSION[ 'first' ] . ' ';
    if( $_SESSION[ 'middle' ] != '' ) {
        $student_name .= $_SESSION[ 'middle' ] . ' ';
    }
    $student_name .= $_SESSION[ 'last' ];

    $headers = "From: $student_name <{$_SESSION[ 'email' ]}>\n"
        . "X-Mailer: OCSW Version {$ocsw[ 'version' ]}\n";
    
    if( $send[ 'replyto' ] == 'ncc' ) {
        $headers .= "Reply-To: "
            . "$student_name <" . strtolower( $_SESSION[ 'banner' ] )
            . "@students.ncc.edu>\n";
    }
    
    // Send the e-mail
    mail( "{$prof[ 'name' ]} <{$prof[ 'email' ]}>",
          "{$send[ 'section_name' ]}: {$send[ 'subject' ]}",
          $send[ 'message' ], $headers );
    
    $mobile_query = 'select ocsw.v, prof.mobile_email '
        .'from ocsw, prof '
        . 'where ocsw.k = "mobile_email"';
    $mobile_result = $db->query( $mobile_query );
    $mobile_row = $mobile_result->fetch_assoc( );
    $mobile = $mobile_row[ 'v' ];
    if( $mobile == 1 ) {
        mail( $mobile_row[ 'mobile_email' ], "{$send[ 'section_name' ]}: {$send[ 'subject' ]}",
        $send[ 'message' ], $headers );
    }
          
    // Find the student_x_section id
    $x_query = "select id from student_x_section "
        . "where student = {$_SESSION[ 'student' ]} and section = {$send[ 'section' ]}";
    $x_result = $db->query( $x_query );
    $x_row = $x_result->fetch_assoc( );
    $x_id = $x_row[ 'id' ];

    // Insert the e-mail into the DB
    $insert_query = 'insert into mail_from_students '
        . '( id, student_x_section, subject, message, sent_time ) '
        . "values( null, $x_id, \"{$insert[ 'subject' ]}\", \"{$insert[ 'message' ]}\", "
        . '"' . date( 'Y-m-d H:i:s' ) . '" )';
    $insert_result = $db->query( $insert_query );
    
    print "<h2 style=\"color: #1e273e;\">Mail Sent</h2>\n";
    print "<p>Your e-mail message \"{$send[ 'subject' ]}\" has been sent.</p>";
}
?>
