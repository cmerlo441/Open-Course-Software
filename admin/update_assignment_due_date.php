<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $assignment = $db->real_escape_string( $_POST[ 'assignment' ] );
    $due_date = $db->real_escape_string( $_POST[ 'due_date' ] );
    $due_time = $db->real_escape_string( $_POST[ 'due_time' ] );
    
    $update_query = 'update assignments '
        . "set due_date = \""
        . date( 'Y-m-d H:i:s', strtotime( "$due_date $due_time" ) )
        . "\" where id = $assignment";
    $update_result = $db->query( $update_query );
    
    $check_query = 'select due_date from assignments '
        . "where id = $assignment";
    $check_result = $db->query( $check_query );
    $check = $check_result->fetch_object( );
    print date( 'l, F j, Y H:i:s', strtotime( $check->due_date ) );
}

?>