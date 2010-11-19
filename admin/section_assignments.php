<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $grade_type = $db->real_escape_string( $_POST[ 'grade_type' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    
    $grade_type_query = 'select * from grade_types '
        . "where id = \"$grade_type\"";
    $grade_type_result = $db->query( $grade_type_query );
    $grade_type_row = $grade_type_result->fetch_assoc( );
    
    $collected_query = 'select w.collected '
        . 'from grade_weights as w, sections as s '
        . 'where s.course = w.course '
        . "and s.id = \"$section\" "
        . "and w.grade_type = {$grade_type_row[ 'id' ]}";
    $collected_result = $db->query( $collected_query );
    $collected_row = $collected_result->fetch_assoc( );
    $collected = $collected_row[ 'collected' ];

    $total_assignments_query = 'select count( id ) as c from assignments '
	. "where section = \"$section\" and grade_type = \"$grade_type\"";
    $total_assignments_result = $db->query( $total_assignments_query );
    $total_assignments_row = $total_assignments_result->fetch_assoc( );
    $total_assignments = $total_assignments_row[ 'c' ];

    foreach( explode( ',', 'Due Already,Due in the Future' ) as $when ) {
        print "<h3>$when</h3>";
        $previous_assignments_query = 'select * from assignments '
             . "where section = \"$section\" "
             . "and grade_type = \"$grade_type\" "
             . 'and due_date '
             . ( $when == 'Due Already' ? '<' : '>=' )
             . ' "' . date( 'Y-m-d H:i:s' ) . '" '
             . 'order by due_date, posted_date';
         $previous_assignments_result =
	     $db->query( $previous_assignments_query );
         if( $previous_assignments_result->num_rows == 0 ) {
             print "<p>None assigned.</p>\n";
         } else {
             while( $a = $previous_assignments_result->fetch_assoc( ) ) {
                $sequence_query = 'select * from assignments '
                    . "where section = \"$section\" "
                    . "and grade_type = \"$grade_type\" "
                    . 'and due_date < "'
		    . date( 'Y-m-d H:i:s', strtotime( $a[ 'due_date' ] ) )
		    . '"';
		//print "<pre>$sequence_query;</pre>\n";
                $sequence_result = $db->query( $sequence_query );
                $sequence = $sequence_result->num_rows + 1;
                
                print "<p><a href=\"$admin/assignment.php?"
		    . "assignment={$a[ 'id' ]}\">"
                    . "<b>{$grade_type_row[ 'grade_type' ]}";
		if( $total_assignments > 1 ) {
		    print " #$sequence";
		}
		print ": ";
                if( isset( $a[ 'title' ] ) and $a[ 'title' ] != '' ) {
                    print "{$a[ 'title' ]}: ";
                }
                if( $collected == 1 ) {
                    print 'Due ';
                }
                print date( 'n/d \a\t g:i a', strtotime( $a[ 'due_date' ] ) )
                    . "</a></b><br />";
                    
                if( $collected == 1 ) {
                    if( $grade_type_row[ 'grade_type' ] == 'Project' ) {
                        $uploads_query = 'select student, datetime '
			    . 'from assignment_uploads '
			    . 'where assignment_upload_requirement in '
			    . '( select id from assignment_upload_requirements '
                            . "where assignment = {$a[ 'id' ]} ) "
                            . "group by student order by datetime desc";
                        $uploads_result = $db->query( $uploads_query );
                        $num = $uploads_result->num_rows;
                        if( $num == 0 ) {
                            print 'No submissions.';
                        } else {
                            $row = $uploads_result->fetch_assoc( );
                            print "$num submission"
				. ( $num == 1 ? '' : 's' ) . '.  '
                                . 'Last one on '
                                . date( 'l, M j \a\t g:i a',
					strtotime( $row[ 'datetime' ] ) )
				. '.';
                        }
                    } else {
                        $submissions_query =
			    'select * from assignment_submissions '
                            . "where assignment = {$a[ 'id' ]} "
			    . 'order by time desc';
                        $submissions_result = $db->query( $submissions_query );
                        $num = $submissions_result->num_rows;
                        if( $num == 0 ) {
                            print "No submissions.</p>\n";
                        } else {
                            $row = $submissions_result->fetch_assoc( );
                            print "$num submission" . ( $num == 1 ? '' : 's' )
				. ".  Last one on "
                                . date( 'l, M j \a\t g:i a',
					strtotime( $row[ 'time' ] ) ) . '.';
                        }
                    }

		    // Were they graded yet?

		    $event_query = 'select id from grade_events '
			. "where assignment = {$a[ 'id' ]}";
		    $event_result = $db->query( $event_query );
		    if( $event_result->num_rows == 1 ) {
			$event_row = $event_result->fetch_assoc( );
			$event = $event_row[ 'id' ];

			$grades_query = 'select grade from grades '
			    . "where grade_event = $event";
			$grades_result = $db->query( $grades_query );
			if( $grades_result->num_rows > 0 ) {
			    $count = 0;
			    $sum = 0;
			    while( $grade_row =
				   $grades_result->fetch_assoc( ) ) {
				$grade = $grade_row[ 'grade' ];
				$sum += ( $grade * 1.0 );
				$count++;
			    }
			    print "<br />Average grade "
				. number_format( $sum / $count, 2 ) . ".";
			}
		    }

                } // if these are collected
		print "</p>\n";
             }
         }
    }

} else {
    print $no_admin;
}
   
?>