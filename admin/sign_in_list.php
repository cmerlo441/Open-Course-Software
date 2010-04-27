<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    $date = $db->real_escape_string( $_POST[ 'date' ] );
    
    $room_query = 'select building, room from section_meetings '
        . "where section = $section "
        . 'and day = ' . date( 'w', strtotime( $date ) );
    $room_result = $db->query( $room_query );
    $room_row = $room_result->fetch_assoc( );
    $room = $room_row[ 'building' ] . ' ' . $room_row[ 'room' ];
    
    $students_query = 'select s.first, s.middle, s.last '
        . 'from students as s, student_x_section as x '
        . 'where x.student = s.id '
        . "and x.section = $section "
        . 'and x.active = 1 '
        . 'order by s.last, s.first, s.middle';
    $students_result = $db->query( $students_query );
    
    print "<h2>" . date( 'l, F jS', strtotime( $date ) )
        . " in $room</h2>\n";
    print "<table class=\"sign_in\">\n";
    while( $student = $students_result->fetch_assoc( ) ) {
        print "  <tr>\n";
        print "    <td>" . lastfirst( $student ) . "</td>\n";
        print "    <td class=\"sign_here\">&nbsp;</td>\n";
        print "  </tr>\n";
    }
    print "</table>\n";
}

?>