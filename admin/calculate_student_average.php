<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $sum = 0;
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    $student = $db->real_escape_string( $_POST[ 'student' ] );

    $minimums = array( );
    
    $weights_query = 'select w.grade_type, w.grade_weight '
        . 'from sections as s, grade_weights as w '
        . 'where w.course = s.course '
        . "and s.id = $section";
    $weights_result = $db->query( $weights_query );
    while( $weights_row = $weights_result->fetch_assoc( ) ) {
        $local_sum = 0;
        $count = 0;
        $weight = $weights_row[ 'grade_weight' ];
	$grade_type = $weights_row[ 'grade_type' ];
	$min = 999;

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
		  $grade = $grade_row[ 'grade' ];

		  // Is there a curve?

		  $curve_query = 'select * from curves '
		    . "where grade_event = {$event[ 'id' ]} ";
		  $curve_result = $db->query( $curve_query );
		  if( $curve_result->num_rows == 1 ) {
		    $curve_row = $curve_result->fetch_assoc( );
		    if( $curve_row[ 'points' ] > 0 ) {
		      $grade += $curve_row[ 'points' ];
		    } else {
		      $grade *= ( 1 + $curve_row[ 'percent' ] * 0.01 );
		    }
		  }
		      
		  $local_sum += $grade;
		  if( $grade < $min )
		      $min = $grade;

                } else {
		    $min = 0;
		}
            }
        }

	// Do we drop the lowest grade of this type?
	$drop_lowest_query = 'select d.id '
	    . 'from sections as s, drop_lowest as d '
	    . "where s.id = $section "
	    . 'and s.course = d.course '
	    . "and d.grade_type = {$weights_row[ 'grade_type' ]}";
	$drop_lowest_result = $db->query( $drop_lowest_query );

	if( $drop_lowest_result->num_rows == 1 ) {
	    $local_sum -= $min;
	    $count--;
	}

        $sum += ( ( ( $count > 0 ? $local_sum / ( $count * 1.0 ) : 100 ) * 1.0 ) * ( $weight / 100 ) );
    }
    print number_format( $sum, 2 );

    // Is this a credit-level class?
    $course_number_query = 'select c.course '
	. 'from sections as s, courses as c '
	. "where s.id = $section "
	. 'and s.course = c.id';
    //print "<pre>$course_number_query;</pre>\n";
    $course_number_result = $db->query( $course_number_query );
    $course_number_row = $course_number_result->fetch_object( );
    //print "<pre>$course_number_row->course</pre>\n";

    $i_w_query = 'select active, incomplete '
	. 'from student_x_section '
	. "where student = $student and section = $section";
    $i_w_result = $db->query( $i_w_query );
    $i_w_row = $i_w_result->fetch_object( );
    $letter_grade = '';
    if( $i_w_row->active == 0 )
	$letter_grade = 'W';
    else if( $i_w_row->incomplete == 1 )
	$letter_grade = 'I';
    else if( ( $course_number_row->course * 1 ) >= 100 ) {

	$letter_grade_query = 'select letter from letter_grades '
	    . "where grade <= $sum limit 1";
	$letter_grade_result = $db->query( $letter_grade_query );
	$letter_grade_row = $letter_grade_result->fetch_object( );
	$letter_grade = $letter_grade_row->letter;
    }
    if( $letter_grade != '' )
	print " / <span style=\"font-weight: bold;\">$letter_grade</span>";

}
?>
