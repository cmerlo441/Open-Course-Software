<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $sum = 0;
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    $student = $db->real_escape_string( $_POST[ 'student' ] );
    
    $weights_query = 'select w.grade_type, w.grade_weight '
        . 'from sections as s, grade_weights as w '
        . 'where w.course = s.course '
        . "and s.id = $section";
    $weights_result = $db->query( $weights_query );
    while( $weights_row = $weights_result->fetch_assoc( ) ) {
        $local_sum = 0;
        $count = 0;
        $weight = $weights_row[ 'grade_weight' ];
            
        // Find all the grade events of this type assigned to this section
        
        $events_query = 'select id from grade_events '
            . "where grade_type = {$weights_row[ 'grade_type' ]} "
            . "and section = $section";
        $events_result = $db->query( $events_query );
        while( $event = $events_result->fetch_assoc( ) ) {
            
            // How many have been graded?
            $graded_query = 'select grade from grades '
                . "where grade_event = {$event[ 'id' ]}";
            $graded_result = $db->query( $graded_query );
            if( $graded_result->num_rows > 0 ) {
                
                // Grading has started.  Count this assignment in the average.
                $count++;
                $grade_query = 'select grade from grades '
                    . "where grade_event = {$event[ 'id' ]} "
                    . "and student = $student";
                $grade_result = $db->query( $grade_query );
                if( $grade_result->num_rows == 1 ) {
                    $grade_row = $grade_result->fetch_assoc( );
                    $local_sum += $grade_row[ 'grade' ];
                }
            }
        }
        $sum += ( ( ( $count > 0 ? $local_sum / $count : 100 ) * 1.0 ) * ( $weight / 100 ) );
    }
    print number_format( $sum, 2 );
}   
?>
