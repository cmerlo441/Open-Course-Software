<?php

$title_stub = 'Schedule';
require_once( './_header.inc' );

$day_names = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday',
		    'Friday', 'Saturday' );

$table_query = 'create temporary table meetings '
    . '( id int not null auto_increment primary key, '
    . 'what varchar( 50 ), day int, start time, end time, '
    . 'building varchar( 25 ), room varchar( 10 ) )';
$table_result = $db->query( $table_query );
$meetings_query = 'select c.dept, c.course, s.section, '
    . 'sm.day, sm.start, sm.end, sm.building, sm.room '
    . 'from courses as c, sections as s, section_meetings as sm '
    . 'where s.course = c.id '
    . 'and sm.section = s.id '
    . 'order by sm.day, sm.start, sm.end ';
$meetings_result = $db->query( $meetings_query );
while( $meeting_row = $meetings_result->fetch_assoc( ) ) {
    $insert_query = 'insert into meetings '
	. '( id, what, day, start, end, building, room ) '
	. "values( null, "
	. "\"{$meeting_row[ 'dept' ]} {$meeting_row[ 'course' ]} "
	. "{$meeting_row[ 'section' ]}\", "
	. "\"{$meeting_row[ 'day' ]}\", "
	. "\"{$meeting_row[ 'start' ]}\", "
	. "\"{$meeting_row[ 'end' ]}\", "
	. "\"{$meeting_row[ 'building' ]}\", "
	. "\"{$meeting_row[ 'room' ]}\" )";
    $insert_result = $db->query( $insert_query );
 }
    
$office_hours_query = 'select day, start, end, building, room '
    . 'from office_hours '
    . 'order by day, start, end';
$office_hours_result = $db->query( $office_hours_query );
while( $hours_row = $office_hours_result->fetch_assoc( ) ) {
    $insert_query = 'insert into meetings '
	. '( id, what, day, start, end, building, room ) '
	. "values( null, 'Office Hours', "
	. "\"{$hours_row[ 'day' ]}\", "
	. "\"{$hours_row[ 'start' ]}\", "
	. "\"{$hours_row[ 'end' ]}\", "
	. "\"{$hours_row[ 'building' ]}\", "
	. "\"{$hours_row[ 'room' ]}\" )";
    $insert_result = $db->query( $insert_query );
 }

foreach( $day_names as $number=>$day ) {

    $meetings_query = 'select * from meetings '
	. "where day = $number "
	. 'order by start, end';
    $meetings_result = $db->query( $meetings_query );
    if( $meetings_result->num_rows > 0 ) {

        print "<h3>$day</h3>\n";
	while( $meeting_row = $meetings_result->fetch_assoc( ) ) {

            print '<div class="meeting"';
	    if( date( 'w' ) == $number
		and $meeting_row[ 'start' ] <= date( 'H:m:s' )
		and $meeting_row[ 'end' ] >= date( 'H:m:s' ) )
		print ' id="now"';
	    print ">\n";
            print "<span class=\"time\">"
                . date( 'g:i a', strtotime( $meeting_row[ 'start' ] ) )
		. '</span> to '
                . "<span class=\"time\">"
                . date( 'g:i a', strtotime( $meeting_row[ 'end' ] ) )
		. "</span>:\n";
            print "<span class=\"course\">{$meeting_row[ 'what' ]}</span> ";
            print "in <span class=\"room\">{$meeting_row[ 'building' ]} "
		. "{$meeting_row[ 'room' ]}</span>\n";
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
