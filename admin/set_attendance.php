<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    mb_parse_str( $_POST[ 'id' ], $values );
    $date = $db->real_escape_string( $values[ 'date' ] );
    $student = $db->real_escape_string( $values[ 'student' ] );
    $section = $db->real_escape_string( $values[ 'section' ] );
    $update_value = $db->real_escape_string( $_POST[ 'attendance' ] );
    
    // Are we changing to P, A, or E?
    if( $update_value >= 1 ) {

        // See if there's already an entry for this date
        $check_query = 'select * from attendance '
            . "where student = $student "
            . "and section = $section "
            . "and date = \"$date\"";
        $check_result = $db->query( $check_query );
        if( $check_result->num_rows == 0 ) {
            
            // If not, insert a new entry
            $insert_query = 'insert into attendance '
                . '( id, student, section, date, presence ) '
                . "values( null, $student, $section, \"$date\", $update_value )";
            $insert_result = $db->query( $insert_query );
        } else {
            
            // Update what's there
            $check_row = $check_result->fetch_assoc( );
            $id = $check_row[ 'id' ];
            $update_query = 'update attendance '
                . "set presence = $update_value where id = $id";
            $update_result = $db->query( $update_query );
        }
    } else {
        
        // Otherwise, we're changing back to nothing
        // See if there's already an entry for this date
        $check_query = 'select * from attendance '
            . "where student = $student "
            . "and section = $section "
            . "and date = \"$date\"";
        $check_result = $db->query( $check_query );
        if( $check_result->num_rows == 0 ) {
            
            // If not, insert a new entry
            $insert_query = 'insert into attendance '
                . '( id, student, section, date, presence ) '
                . "values( null, $student, $section, \"$date\", 0 )";
            $insert_result = $db->query( $insert_query );
        } else {
            
            // Update what's there
            $check_row = $check_result->fetch_assoc( );
            $id = $check_row[ 'id' ];
            $update_query = 'update attendance '
                . 'set presence = 0 '
                . "where id = $id";
            $update_result = $db->query( $update_query );
        }
    }
}
   
?>
