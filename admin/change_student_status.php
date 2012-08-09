<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $student = $db->real_escape_string( $_REQUEST[ 'student' ] );
    $section = $db->real_escape_string( $_REQUEST[ 'section' ] );
    $status  = $db->real_escape_string( $_REQUEST[ 'status' ] );
    
    $db->query( 'update student_x_section '
        . "set status = $status "
        . "where student = $student "
        . "and section = $section"
    );
    print $db->affected_rows;
}
