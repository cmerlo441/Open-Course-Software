<?php

$no_header = 1;
require_once( "../_header.inc" );

if( $_SESSION[ 'admin' ] == 1 ) {
    $course     = $db->real_escape_string( $_REQUEST[ 'course' ] );
    $grade_type = $db->real_escape_string( $_REQUEST[ 'grade_type' ] );
    $checked    = $db->real_escape_string( $_REQUEST[ 'checked' ] );  // 0 or 1
    
    $existing_query = 'select id from drop_lowest '
        . "where course = $course "
        . "and grade_type = $grade_type";
    $existing_result = $db->query( $existing_query );
    if( $existing_result->num_rows == 1 and $checked == 0 ) {
        $delete_query = 'delete from drop_lowest '
            . "where course = $course "
            . "and grade_type = $grade_type";
        $delete_result = $db->query( $delete_query );
        print $db->affected_rows;
    } else if( $existing_result->num_rows == 0 and $checked == 1 ) {
        $insert_query = 'insert into drop_lowest( id, course, grade_type ) '
            . "values( null, $course, $grade_type )";
        $insert_result = $db->query( $insert_query );
        print $db->affected_rows;
    } else {
        print 0;  // If the checkbox and the DB don't match, bad things happened.
    }
}

?>