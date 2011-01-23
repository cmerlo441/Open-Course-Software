<?php 

$no_header = 1;
require_once( '../_header.inc' );

$days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
               'Saturday', 'Sunday' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $section_id = $db->real_escape_string( $_POST[ 'id' ] );
    $section_query = 'select c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . "where s.id = $section_id "
        . 'and s.course = c.id';
    $section_result = $db->query( $section_query );
    $section_row = $section_result->fetch_object( );
    $section = "$section_row->dept $section_row->course $section_row->section";
    
    $meetings_query = 'select * from section_meetings '
        . "where section = $section_id "
        . 'order by day, start';
    $meetings_result = $db->query( $meetings_query );
    if( $meetings_result->num_rows == 0 )
        print "No class meetings for $section have been defined.";
    else {
        print "Class Meetings for $section<br />\n";
        print "<ul>\n";
        while( $meeting = $meetings_result->fetch_object( ) ) {
            print "<li id=\"$meeting->id\">";
            print $days[ $meeting->day ] . ' '
                . date( "g:i a", strtotime( $meeting->start ) ) . ' to '
                . date( "g:i a", strtotime( $meeting->end ) ) . ' in '
                . "$meeting->building $meeting->room.</li>\n";
        }
        print "</ul>\n";
    }
        
}

?>