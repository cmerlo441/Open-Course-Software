<?php

$no_header = 1;
require_once( "../_header.inc" );

if( $_SESSION[ 'admin' ] == 1 ) {
    $course     = $db->real_escape_string( $_REQUEST[ 'course' ] );
    $grade_type = $db->real_escape_string( $_REQUEST[ 'grade_type' ] );
    $checked    = $db->real_escape_string( $_REQUEST[ 'checked' ] );  // 0 or 1
    
    $existing_query = 'select id from grade_weights '
        . "where course = $course "
        . "and grade_type = $grade_type";
    $existing_result = $db->query( $existing_query );
    if( $existing_result->num_rows == 1 ) {
        $existing = $existing_result->fetch_object( );
        $update_query = 'update grade_weights '
            . "set collected = $checked "
            . "where id = $existing->id";
        $update_result = $db->query( $update_query );
        print $db->affected_rows;
    } else {
        print 0;  // Why would this weight not exist in the DB?
    }
}

?>