<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    $grade_type = $db->real_escape_string( $_POST[ 'grade_type' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    $name = $db->real_escape_string( $_POST[ 'grade_name' ] );
    
    $events_query = 'select * from grade_events '
        . "where grade_type = $grade_type "
        . "and section = $section "
        . 'order by date';
    $events_result = $db->query( $events_query );
    if( $events_result->num_rows == 0 ) {
        print 'None.  Class average uses an average ' . $name . ' grade of 100%.';
    } else {
        $sequence = 0;
        $graded = 0;
        $sum = 0;
        while( $event = $events_result->fetch_assoc( ) ) {
            $sequence++;
            print "<div class=\"assignment\" id=\"{$event[ 'id' ]}\">\n";
            print "<div class=\"date\">";
            if( $events_result->num_rows > 1 ) {
                print "$name #{$sequence}: ";
            }
            print date( 'F j', strtotime( $event[ 'date' ] ) ) . "</div>\n";
            
            // Is this something that gets collected?  If so, find the submission
            
            $collected_query = 'select w.collected '
                . 'from sections as s, grade_weights as w '
                . 'where w.course = s.course '
                . "and w.grade_type = $grade_type "
                . "and s.id = $section";
            $collected_result = $db->query( $collected_query );
            $collected_row = $collected_result->fetch_assoc( );
            $collected = $collected_row[ 'collected' ];

	    // See if they've been graded
                
	    $graded_query = 'select * from grades '
		. "where grade_event = \"{$event[ 'id' ]}\"";
	    $graded_result = $db->query( $graded_query );
	    $graded_yet = ( $graded_result->num_rows );
	    if( $graded_yet > 0 ) {

		// See if this student submitted

		// What kind of assignment?

		$project_query = 'select * from grade_types '
		    . "where id = $grade_type and grade_type = 'project'";
		$project_result = $db->query( $project_query );
		if( $project_result->num_rows == 1 ) {
		    $submission_query = 'select u.id '
			. 'from assignment_uploads as u, grade_events as e '
			. 'where u.assignment = e.assignment '
			. "and u.student = {$_SESSION[ 'student' ]}";
		} else {

		    $submission_query = 'select s.id '
			. 'from assignment_submissions as s, grade_events as e '
			. "where e.assignment = s.assignment "
			. "and e.id = {$event[ 'id' ]} "
			. "and s.student = {$_SESSION[ 'student' ]}";
		}
		$submission_result = $db->query( $submission_query );
		if( $submission_result->num_rows > 0 ) {

		    // The student submitted
                
		    $grade_query = 'select id, grade from grades '
			. "where student = {$_SESSION[ 'student' ]} "
			. "and grade_event = {$event[ 'id' ]}";
		    $grade_result = $db->query( $grade_query );
		    if( $grade_result->num_rows == 1 ) {
                    
			// The submission was graded
                    
			$graded++;
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

			print "<div class=\"grade\">Grade: "
			    . "<span class=\"grade\">$grade</span>.</div>\n";
			$sum += $grade;
		    } else {
			print 'Not graded yet.';
		    }
		} else {

		    // The student has not submitted

		    if( $collected == 1 ) {
			print 'Not submitted.';
		    } else {
			print 'No grade.';
		    }
		    if( $graded_result->num_rows == 1 ) {
			$graded++;
		    }
		}
	    } else {
		print 'Not graded yet.';
	    }

            print "</div>  <!--div.assignment#{$events[ 'id' ]} -->\n";
        } // for all the assignments
        
        $average = ( $graded > 0 ? $sum / ( $graded * 1.0 ) : 0 );
        print "<div class=\"average\">Average grade: "
            . number_format( $average, 2 ) . ".</div>\n";
    }
 }
   
?>
