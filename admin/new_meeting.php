<?php 

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    if( isset( $_POST[ 'day' ] ) and isset( $_POST[ 'start' ] ) and
        isset( $_POST[ 'end' ] ) and isset( $_POST[ 'building' ] ) and
        isset( $_POST[ 'room' ] ) ) {
        $day      = $db->real_escape_string( $_POST[ 'day' ] );
        $start    = $db->real_escape_string( $_POST[ 'start' ] );
        $end      = $db->real_escape_string( $_POST[ 'end' ] );
        $building = ucwords( $db->real_escape_string( $_POST[ 'building' ] ) );
        $room     = $db->real_escape_string( $_POST[ 'room' ] );
        
        $insert_query = 'insert into section_meetings '
            . '( id, section, day, start, end, building, room ) '
            . "values( null, $section, $day, "
            . '"' . date( 'H:i:s', strtotime( $start ) ) . '", '
            . '"' . date( 'H:i:s', strtotime( $end ) ) . '", '
            . "\"$building\", \"$room\" )";
        $insert_result = $db->query( $insert_query );
        print $db->affected_rows == 0 ? 'ok' : 'error';
    }
}


?>