<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    // Remove a section from the DB
    if( $_POST[ 'action' ] == 'remove_section' ) {
        // First, remove all meetings that go with this section
        $meetings_query = 'select * from section_meetings '
            . "where section = \"{$_POST[ 'section' ]}\"";
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
            . "where id = \"{$_POST[ 'section' ]}\"";
        $section_result = $db->query( $section_query );
        $section_result->close( );
    }
    
    // Remove a meeting from the DB
    if( $_POST[ 'action' ] == 'remove_meeting' ) {
        $delete_query = 'delete from section_meetings '
            . "where id = \"{$_POST[ 'meeting' ]}\"";
        $delete_result = $db->query( $delete_query );
        $delete_result->close( );
    }
    
    // Add a meeting to the DB
    if( $_POST[ 'action' ] == 'add_meeting' ) {
        $insert_query = 'insert into section_meetings '
            . '( id, section, day, start, end, building, room ) values '
            . "( null, \"{$_POST[ 'section' ]}\", \"{$_POST[ 'day' ]}\", "
            . '"' . date( 'H:i:s', strtotime( htmlentities( trim ( $_POST[ 'start' ] ) ) ) ) . '", '
            . '"' . date( 'H:i:s', strtotime( htmlentities( trim ( $_POST[ 'end' ] ) ) ) ) . '", '
            . '"' . htmlentities( trim( $_POST[ 'building' ] ) ) . '", '
            . '"' . htmlentities( trim( $_POST[ 'room' ] ) ) . '" )';
        $insert_result = $db->query( $insert_query );
        
        print "New meeting added.  Refresh page to see changes.";        
    }
}
 
?>