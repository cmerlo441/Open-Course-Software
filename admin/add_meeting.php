<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $section  = $db->real_escape_string( $_POST[ 'section' ] );
    $day      = $db->real_escape_string( $_POST[ 'day' ] );
    $start    = $db->real_escape_string( $_POST[ 'start' ] ); 
    $end      = $db->real_escape_string( $_POST[ 'end' ] );
    $building = $db->real_escape_string( $_POST[ 'building' ] );
    $room     = $db->real_escape_string( $_POST[ 'room' ] );
    
    $insert_query = 'insert into section_meetings (id, section, day, start, end, building, room) '
        . "values( null, \"$section\", \"$day\", "
        . "\"" . date( 'H:i:s', strtotime( $start ) ) . "\", \"" . date( 'H:i:s', strtotime( $end ) ) . "\", "
        . "\"$building\", \"$room\" )";
    $insert_result = $db->query( $insert_query );
    print $db->insert_id;
}

?>