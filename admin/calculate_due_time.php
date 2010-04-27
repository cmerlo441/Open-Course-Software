<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    if( isset( $error ) ) {
        unset( $error );
    }
    
    $assignment_type = $db->real_escape_string( $_POST[ 'assignment_type' ] );
    $date = $db->real_escape_string( $_POST[ 'date' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );

    // Is this a day section or an evening section?
    
    $day_eve_query = 'select day from sections '
        . "where section = \"$section\"";
    $day_eve_result = $db->query( $day_eve_query );
    $day_eve_row = $day_eve_result->fetch_assoc( );
    $day_eve = $day_eve_row[ 'day' ];
    
    // Is the chosen day a holiday?
    
    $holiday_query = 'select description from holidays '
        . "where date = \"$date\" "
        . 'and ' . ( $day_eve == 1 ? 'day' : 'evening' ) . ' == 1';
    $holiday_result = $db->query( $holiday_query );
    if( $holiday_result->num_rows == 1 ) {
        $holiday_row = $holiday_result->fetch_assoc( );
        $error = "Holiday: {$holiday_row[ 'description' ]}";
    }
    
    // Is the chosen day rescheduled as another day of the week?
    
    $resched_query = "select follow from rescheduled_days "
        . "where date = \"$section\" "
        . 'and ' . ( $day_eve == 1 ? 'day' : 'evening' ) . ' == 1';
    $resched_result = $db->query( $resched_query );
    if( $resched_result->num_rows == 1 ) {
        $resched_row = $resched_result->fetch_assoc( );
        $day = $resched_row[ 'follow' ];
    } else {
        $day = date( 'w', strtotime( $date ) );
    }
    
    // Does this section meet on the chosen day?
    $day_query = 'select start from section_meetings '
        . "where section = \"$section\" and day = \"$day\"";
    $day_result = $db->query( $day_query );
    if( $day_result->num_rows == 0 ) {
        $error = "This section does not meet on this day";
    } else {
        $row = $day_result->fetch_assoc( );
        $time = $row[ 'start' ];
    }
    
    if( isset( $error ) ) {
        print $error;
    } else {
        print date( 'g:i a', strtotime( $time ) );
    }
}

?>
