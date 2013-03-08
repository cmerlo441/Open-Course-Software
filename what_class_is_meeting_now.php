<?php

$no_header = 1;
require_once( './_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    function what_class_is_meeting_now( ) {
	global $db;

	// If today is a holiday, just return 0
	$holiday_query = 'select * from holidays '
	    . 'where date = "' . date( 'Y-m-d' ) . '"';
	$holiday_result = $db->query( $holiday_query );
	if( $holiday_result->num_rows == 1 )
	    return 0;

	$day = $evening = date( 'w' );
	$time = date( 'H:i:s' );

	// Is today rescheduled?
	$resched_query = 'select day, evening, follow '
	    . 'from rescheduled_days '
	    . 'where date = "' . date( 'Y-m-d' ) . '"';
	$resched_result = $db->query( $resched_query );
	if( $resched_result->num_rows == 1 ) {
	    $r = $resched_result->fetch_object( );
	    if( $r->day == 1 )
		$day = $r->follow;
	    if( $r->evening == 1 )
		$evening = $r->follow;
	}

	$meetings_query = 'select s.id '
	    . 'from sections as s, section_meetings as m '
	    . 'where m.section = s.id '
	    . "and ( ( s.day = 1 and m.day = $day ) "
	    . "or ( s.day = 0 and m.day = $evening ) ) "
	    . "and m.start <= \"$time\" and m.end >= \"$time\" ";

	$meetings_result = $db->query( $meetings_query );
	if( $meetings_result->num_rows == 0 )
	    return 0;
	else {
	    $s = $meetings_result->fetch_object( );
	    return $s->id;
	}
    }  // what_class_is_meeting_now()

    //print "Class " . what_class_is_meeting_now( ) . " is meeting now.";

}

?>