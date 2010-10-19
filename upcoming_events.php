<?php

$no_header = 1;
//$title_stub = 'Upcoming Events';
require_once( './_header.inc' );

if( $_SESSION[ 'admin' ] == 1 or $_SESSION[ 'student' ] > 0 ) {
    $events_query = 'select a.id, g.grade_type, g.plural, a.due_date, '
	. 'a.title, a.description, c.dept, c.course, s.section, '
	. 's.id as section_id '
	. 'from assignments as a, grade_types as g, sections as s, '
	. 'courses as c '
	. 'where g.id = a.grade_type '
	. 'and c.id = s.course '
	. 'and s.id = a.section '
	. 'and a.due_date > "' . date( 'Y-m-d H:i:s' ) . '" ';
    if( $_SESSION[ 'student' ] > 0 ) {
	$sections_query = 'select section from student_x_section '
	    . "where student = {$_SESSION[ 'student' ]} "
	    . 'and active = 1';
	$sections_result = $db->query( $sections_query );
	$num_sections = $sections_result->num_rows;
	if( $num_sections == 0 ) {
	    $events_query .= 'and a.section = 0 ';
	} else {
	    $sections_row = $sections_result->fetch_assoc( );
	    $events_query .= "and ( a.section = {$sections_row[ 'section' ]} ";
	    while( $sections_row = $sections_result->fetch_assoc( ) ) {
		$events_query .= " or a.section = "
		    . "{$sections_row[ 'section' ]} ";
	    }
	    $events_query .= ')';
	}
    }
    $events_query .= 'order by a.due_date, c.dept, c.course, s.section, '
	. 'g.grade_type';

    $events_result = $db->query( $events_query );

    print "<div id=\"upcoming_internal\">\n";
    if( $events_result->num_rows == 0 )
	print 'None.';

    $day = '';
    while( $event_row = $events_result->fetch_assoc( ) ) {
	$temp_day = date( 'l, F j', strtotime( $event_row[ 'due_date' ] ) );
	if( $day != $temp_day ) {
	    $day = $temp_day;
	    print "<h3>$day</h3>\n";
	}

	$section = $event_row[ 'dept' ] . ' ' . $event_row[ 'course' ]
	    . ' ' . $event_row[ 'section' ];
	print "<div class=\"upcoming_event\" id=\"{$event_row[ 'id' ]}\">\n";
	print "<span class=\"section\">$section</span> \n";
	print "<span class=\"title\">";
	if( $_SESSION[ 'admin' ] == 1 )
	    print_link( "$admin/assignment.php?"
			. "assignment={$event_row[ 'id' ]}",
			$event_row[ 'title' ] == ''
			? $event_row[ 'grade_type' ]
			: $event_row[ 'title' ] );
	else {
	    if( $event_row[ 'grade_type' ] == 'Homework' )
		$url = "$student/homework.php?";
	    else if( $event_row[ 'grade_type' ] == 'Lab Assignment' )
		$url = "$student/lab_assignments.php?";
	    else if( $event_row[ 'grade_type' ] == 'Project' )
		$url = "$student/projects.php?";
	    else
		$url = '';
	    if( $url != '' ) {
		$url .= "section={$event_row[ 'section_id' ]}";
		print_link( $url, $event_row[ 'title' ] == ''
			    ? $event_row[ 'grade_type' ]
			    : $event_row[ 'title' ] );
		print ' due at '
		    . date( 'g:i a', strtotime( $event_row[ 'due_date' ] ) );
	    } else {
		print $event_row[ 'title' ] == ''
		    ? $event_row[ 'grade_type' ]
		    : $event_row[ 'title' ];
		if( $event_row[ 'description' ] != '' ) {
		    print "</span><br />\n";
		    print "<span class=\"description\">{$event_row[ 'description' ]}";
		}
	    }
	}
	print "</span>\n";
	print "</div>  <!-- div.upcoming_event#{$event_row[ 'id' ]} -->\n";
    }
	print "</div>\n";
}
