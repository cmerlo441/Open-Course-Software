<?php

$title_stub = 'Schedule';
require_once( './_header.inc' );

$day_names = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );

for( $day = 0; $day <= 6; $day++ ) {
    $meetings_query = 'select c.dept, c.course, s.section, '
        . 'sm.day, sm.start, sm.end, sm.building, sm.room '
        . 'from courses as c, sections as s, section_meetings as sm '
        . 'where s.course = c.id '
        . 'and sm.section = s.id '
        . "and sm.day = $day "
        . 'order by sm.day, sm.start, sm.end ';
    $meetings_result = $db->query( $meetings_query );
    $seq = 0;
    while( $meeting = $meetings_result->fetch_assoc( ) ) {
        foreach( explode( ',', 'dept,course,section,start,end,building,room' ) as $field ) {
            $meetings[ $day ][ $seq ][ $field ] = $meeting[ $field ];
        }
        $seq++;
    }
    
    $office_hours_query = 'select start, end, building, room '
        . 'from office_hours '
        . "where day = $day "
        . 'order by start, end';
    $office_hours_result = $db->query( $office_hours_query );
    while( $hour = $office_hours_result->fetch_assoc( ) ) {
        $meetings[ $day ][ $seq ][ 'course' ] = 'Office Hours';
        foreach( explode( ',', 'start,end,building,room' ) as $field ) {
            $meetings[ $day ][ $seq ][ $field ] = $hour[ $field ];
        }
        $seq++;
    }
    
    if( isset( $meetings[ $day ][ 0 ] ) ) {
        print "<h3>{$day_names[ $day ]}</h3>\n";
        foreach( $meetings[ $day ] as $meeting ) {
            print '<div ';
            if( date( 'w' ) == $day ) {
                $today = 1;
                print 'id=today ';
            } else {
                $today = 0;
            }
            print "class=\"meeting\">\n";
            if( $today == 1 && date( 'H:i:s' ) >= $meeting[ 'start' ] && date( 'H:i:s' ) <= $meeting[ 'end' ] ) {
                print "&#x21D2; ";
            }
            print "<span class=\"time\">"
                . date( 'g:i a', strtotime( $meeting[ 'start' ] ) ) . '</span> to '
                . "<span class=\"time\">"
                . date( 'g:i a', strtotime( $meeting[ 'end' ] ) ) . "</span>:\n";
            print "<span class=\"course\">{$meeting[ 'dept' ]} {$meeting[ 'course' ]} {$meeting[ 'section' ]}</span> ";
            print "in <span class=\"room\">{$meeting[ 'building' ]} {$meeting[ 'room' ]}</span>\n";
            print "</div> <!-- meeting -->\n\n";
        }
    }
}

/*
print "<pre>";
print_r( $meetings );
print "</pre>\n";
*/
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
require_once( './_footer.inc' );
   
?>
