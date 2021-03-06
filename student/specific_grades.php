<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    $grade_type = $db->real_escape_string( $_POST[ 'grade_type' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    $name = $db->real_escape_string( $_POST[ 'grade_name' ] );
    $sum = 0;
    $count = 0;
    $sequence = 0;

    // Make sure this student is in this section
    $section_query = 'select id from student_x_section '
        . "where student = {$_SESSION[ 'student' ]} "
        . "and section = $section";
    $section_result = $db->query( $section_query );
    if( $section_result->num_rows != 1 )
        die( 'Database error.' );

    // Get all the grade events of this grade type for this section
    $events_query = 'select a.title, e.id, e.section, e.grade_type, e.date, e.assignment '
        . 'from grade_events as e, assignments as a '
	    . "where e.grade_type = $grade_type "
	    . 'and e.assignment = a.id '
	    . "and e.section = $section "
	    . 'order by e.date, e.id';
    $events_result = $db->query( $events_query );

    if( $events_result->num_rows == 0 )
	exit( 'None.  Class average uses an average ' . $name
	      . ' grade of 100%.' );

    $min_grade = 999;
    $min_grade_event = 0;

    while( $event = $events_result->fetch_assoc( ) ) {
	$sequence++;
	print "<div class=\"assignment\" id=\"{$event[ 'id' ]}\">\n";
	print "<div class=\"date\">";
	if( $events_result->num_rows > 1 ) {
	    print "$name #{$sequence}: ";
	}
    if( $event[ 'title' ] != '' ) {
        print "{$event[ 'title' ]}, ";
    }
	print date( 'F j', strtotime( $event[ 'date' ] ) ) . "</div>\n";

	$grade_event = $event[ 'id' ];

	// Are there any grades recorded yet for this assignment?
	$grades_query = 'select * from grades '
	    . "where grade_event = $grade_event";
	$grades_result = $db->query( $grades_query );
	if( $grades_result->num_rows > 0 ) {

	    // Yes.  Does this kind of assignment get collected?
	    $collected_query = 'select w.collected '
		. 'from grade_weights as w, sections as s '
		. "where s.id = $section "
		. "and s.course = w.course "
		. "and w.grade_type = $grade_type";
	    $collected_result = $db->query( $collected_query );
	    $collected_row = $collected_result->fetch_assoc( );
	    $collected = $collected_row[ 'collected' ];
	    if( $collected == 1 ) {

		/* Yes.  Has the student submitted it?
		 * If the grade_type is 'Project', then we have to look
		 * in assignment_uploads.  Otherwise, we look in
		 * assignment_submissions.
		 */

		if( preg_match( '/Project/i', $name ) == 1 ) {
		    $submitted_query = 'select count( u.id ) as c '
			. 'from assignment_uploads as u, '
			. 'assignment_upload_requirements as r, '
			. 'grade_events as e '
			. "where u.assignment_upload_requirement = r.id "
			. "and r.assignment = e.assignment "
			. "and e.id = $grade_event "
			. "and u.student = {$_SESSION[ 'student' ]}";
		} else {
		    $submitted_query = 'select count( s.id ) as c '
			. 'from assignment_submissions as s, '
			. 'grade_events as e '
			. "where s.student = {$_SESSION[ 'student' ]} "
			. 'and s.assignment = e.assignment '
			. "and e.id = $grade_event";
		}
		$submitted_result = $db->query( $submitted_query );
		$submitted_row = $submitted_result->fetch_assoc( );
		if( $submitted_row[ 'c' ] > 0 ) {

		    // Yes.  Has it been graded?
		    $grade_query = 'select grade from grades '
			. "where grade_event = $grade_event "
			. "and student = {$_SESSION[ 'student' ]}";
		    $grade_result = $db->query( $grade_query );
		    if( $grade_result->num_rows == 1 ) {

			// Yes.  Display the grade and add it to the sum.
			$grade_row = $grade_result->fetch_assoc( );
			print "<div class=\"grade\" id=\"$grade_event\">"
			    . 'Grade: ';
			print "<span class=\"grade\" id=\"$grade_event\">"
			    . "{$grade_row[ 'grade' ]}</span></div>\n";
			$sum += $grade_row[ 'grade' ];
			$count++;

			// Is there a curve?  If so, display it

			$curve_query = 'select * from curves '
			    . "where grade_event = $grade_event";
			$curve_result = $db->query( $curve_query );
			if( $curve_result->num_rows == 1 ) {
			    $curve_row = $curve_result->fetch_assoc( );
			    if( $curve_row[ 'points' ] > 0 ) {
				$curved_grade = $grade_row[ 'grade' ] + $curve_row[ 'points' ];
			    } else {
				$curved_grade = $grade_row[ 'grade' ] *
				    ( 1 + $curve_row[ 'percent' ] * 0.01 );
			    }
			    print " &rarr; $curved_grade";
			    $sum += $curved_grade;

			    if( $curved_grade < $min_grade ) {
				$min_grade = $curved_grade;
				$min_grade_event = $grade_event;
			    }

			}  // if there's a curve
			else {
			    $sum += $grade_row[ 'grade' ];
			    if( $grade_row[ 'grade' ] < $min_grade ) {
				$min_grade = $grade_row[ 'grade' ];
				$min_grade_event = $grade_event;
			    }
			}

			print "</span>.</div>\n";
			$count++;

		    } else {

			// No, it hasn't been graded.
			print 'Not graded yet.';
		    }
		} else {

		    // No, it wasn't submitted.  Average in a 0.
		    print 'Not submitted.';
		    $count++;
		    if( $min_grade > 0 ) {
			$min_grade = 0;
			$min_grade_event = $grade_event;
		    }
		}
	    } else {

		// OK, so these don't get submitted.  See if there's a grade.
		$grade_query = 'select grade from grades '
		    . "where grade_event = $grade_event "
		    . "and student = {$_SESSION[ 'student' ]}";
		//print "<pre>$grade_query;</pre>\n";
		$grade_result = $db->query( $grade_query );
		if( $grade_result->num_rows == 1 ) {

		    // Yes.  Display the grade and add it to the sum.
		    $grade_row = $grade_result->fetch_assoc( );
		    print "<div class=\"grade\" id=\"$grade_event\">Grade: ";
		    print "<span class=\"grade\" id=\"$grade_event\">"
			. "{$grade_row[ 'grade' ]}";

		    // Is there a curve?  If so, display it

		    $curve_query = 'select * from curves '
			. "where grade_event = $grade_event";
		    $curve_result = $db->query( $curve_query );
		    if( $curve_result->num_rows == 1 ) {
			$curve_row = $curve_result->fetch_assoc( );
			if( $curve_row[ 'points' ] > 0 ) {
			    $curved_grade = $grade_row[ 'grade' ] + $curve_row[ 'points' ];
			} else {
			    $curved_grade = $grade_row[ 'grade' ] *
				( 1 + $curve_row[ 'percent' ] * 0.01 );
			}
			print " &rarr; $curved_grade";
			$sum += $curved_grade;

			if( $curved_grade < $min_grade ) {
			    $min_grade = $curved_grade;
			    $min_grade_event = $grade_event;
			}
		    }  // if there's a curve
		    else {
			$sum += $grade_row[ 'grade' ];
			if( $grade_row[ 'grade' ] < $min_grade ) {
			    $min_grade = $grade_row[ 'grade' ];
			    $min_grade_event = $grade_event;
			}		    
		    }

		    print "</span>.</div>\n";
		    $count++;

		} else {

		    /* This student's assignment hasn't been graded yet.
		     * We're going to assume here that since these don't
		     * get submitted, it's something like an exam or a quiz,
		     * where you grade them all on paper and then just enter
		     * the grades.  So, if other students have grades but
		     * this student doesn't, let's call it a zero, and factor
		     * that into to the average.
		     */
		    print "<div class=\"grade\" id=\"$grade_event\">"
			. 'Grade: ';
		    print "<span class=\"grade\" id=\"$grade_event\">"
			. "0</span></div>\n";
		    $count++;
		    if( $min_grade > 0 ) {
			$min_grade = 0;
			$min_grade_event = $grade_event;
		    }
		}
	    }
	} else {
	    // No grades have been recorded yet for anyone.
	    print 'Not graded yet.';
	}
	print "</div>  <!-- div.assignment#$grade_event -->\n";
    } // for each event of this type for this section

    // Are we dropping the lowest grade?
    $drop = 0;
    $drop_query = 'select d.id '
	. 'from drop_lowest as d, sections as s '
	. 'where s.course = d.course '
	. "and s.id = $section "
	. "and d.grade_type = $grade_type ";
    $drop_result = $db->query( $drop_query );
    if( $drop_result->num_rows == 1 ) {
	$drop = $min_grade_event;
	$count--;
	$sum -= $min_grade;
    }

    $average = ( $count > 0 ? $sum / ( $count * 1.0 ) : 100 );
    print "<div class=\"average\">Average $name Grade: "
	. number_format( $average, 2 ) . "</div>\n";

?>

<script type="text/javascript">
$(document).ready(function(){

    var event = <?php echo $drop; ?>;

    if( event > 0 ) {
	$('div.grade[id=' + event + ']')
	    .css('background-color','#5c5d60')
	    .css('color','#8f8b88')
	    .html( $('div.grade[id=' + event + ']').html() +
		   '<br />This grade has been dropped.' );
    }

})
</script>

<?php

}