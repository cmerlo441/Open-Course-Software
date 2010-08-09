<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    // Remove a section from the DB

    $action   = $db->real_escape_string( $_POST[ 'action' ] );
    $section  = $db->real_escape_string( $_POST[ 'section' ] );
    $meeting  = $db->real_escape_string( $_POST[ 'meeting' ] );
    $day      = $db->real_escape_string( $_POST[ 'day' ] );
    $start    = $db->real_escape_string( $_POST[ 'start' ] );
    $end      = $db->real_escape_string( $_POST[ 'end' ] );
    $building = $db->real_escape_string( $_POST[ 'building' ] );
    $room     = $db->real_escape_string( $_POST[ 'room' ] );

    if( $action == 'remove_section' ) {
        // First, remove all meetings that go with this section
        $meetings_query = 'select * from section_meetings '
            . "where section = \"$section\"";
        $meetings_result = $db->query( $meetings_query );
        while( $row = $meetings_result->fetch_assoc( ) ) {
            $delete_query = 'delete from section_meetings '
                . "where id = {$row[ 'id' ]}";
            $delete_result = $db->query( $delete_query );
            $delete_result->close( );
        }
        $meetings_result->close( );
        
        // Then, remove the section itself
        $section_query = 'delete from sections '
            . "where id = \"$section\"";
        $section_result = $db->query( $section_query );
        $section_result->close( );
    }
    
    // Remove a meeting from the DB
    if( $action == 'remove_meeting' ) {
        $delete_query = 'delete from section_meetings '
            . "where id = \"$meeting\"";
        $delete_result = $db->query( $delete_query );
        $delete_result->close( );
    }
    
    // Add a meeting to the DB
    if( $action == 'add_meeting' ) {
        $insert_query = 'insert into section_meetings '
            . '( id, section, day, start, end, building, room ) values '
            . "( null, \"$section\", \"$day\", "
            . '"' . date( 'H:i:s',
			  strtotime( htmlentities( trim ( $start ) ) ) ) . '", '
            . '"' . date( 'H:i:s',
			  strtotime( htmlentities( trim ( $end ) ) ) ) . '", '
            . '"' . htmlentities( trim( $building ) ) . '", '
            . '"' . htmlentities( trim( $room ) ) . '" )';
        $insert_result = $db->query( $insert_query );
        
        print "New meeting added.  Refresh page to see changes.";        
    }
}
 
?>