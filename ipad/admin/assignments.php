<?php

$title_stub = 'Assignments';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $section = $db->real_escape_string( $_GET[ 'section' ] );
    
    $section_query = 'select c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = $section";
    $section_result = $db->query( $section_query );
    $section_row = $section_result->fetch_object( );
    $section_name = "$section_row->dept $section_row->course $section_row->section";
    
    print "<div data-role=\"header\" data-inset=\"true\">\n";
    print "<h1>$section_name Assignments</h1>\n";
    print "</div>\n";

    print "<ul data-role=\"listview\" data-theme=\"c\" data-inset=\"true\">\n";

    $grade_types_query = 'select t.grade_type, t.plural, t.id, '
        . 'w.collected, w.grade_weight as w ' 
    	. 'from grade_types as t, grade_weights as w, sections as s '
    	. "where s.id = $section "
    	. 'and s.course = w.course '
    	. 'and w.grade_type = t.id '
    	. 'order by t.grade_type';
    $grade_types_result = $db->query( $grade_types_query );
    while( $grade_type = $grade_types_result->fetch_object( ) ) {

    	$assignments_query = 'select id, due_date, title, description '
    	    . 'from assignments '
    	    . "where section = $section "
    	    . "and grade_type = $grade_type->id "
    	    . 'order by due_date';
    	$assignments_result = $db->query( $assignments_query );
    	if( $assignments_result->num_rows > 0 ) {
    	    print "<li data-role=\"list-divider\">"
        		. ( $assignments_result->num_rows == 1 ? $grade_type->grade_type : $grade_type->plural )
        		. "<span class=\"ui-li-count\">$assignments_result->num_rows</span>"
        		. "</li>\n";
    	    $count = 1;
    	    while( $assignment = $assignments_result->fetch_object( ) ) {
        		print "<li>";
                print "<a href=\"assignment.php?assignment=$assignment->id\">\n";
        		print "<h3>$grade_type->grade_type #$count</h3>";
        		print "<p>$assignment->title</p>\n";
        		print "<p class=\"ui-li-aside\">" . date( 'D, M j', strtotime( $assignment->due_date ) ) . "<br />";
        		if( $grade_type->grade_type == 'Project' ) {
        		    $uploads_query = 'select u.id '
        			. 'from assignment_uploads as u, assignment_upload_requirements as r '
        			. "where r.assignment = $assignment->id "
        			. 'and u.assignment_upload_requirement = r.id '
        			. 'group by u.student';
        		    $uploads_result = $db->query( $uploads_query );
        		    print "$uploads_result->num_rows submissions.";
        		} else if( $grade_type->collected ) {
        		    $submit_query = 'select id '
        			. 'from assignment_submissions '
        			. "where assignment = $assignment->id";
        		    $submit_result = $db->query( $submit_query );
        		    print "$submit_result->num_rows submissions.";
        		} else if( $grade_type->w > 0 and $assignment->due_date < date( 'Y-m-d G:i:s' ) ) {
        		    $grades_query = 'select g.id '
        			. 'from grades as g, grade_events as e '
        			. "where e.assignment = $assignment->id "
        			. 'and g.grade_event = e.id';
        		    $grades_result = $db->query( $grades_query );
        		    print "$grades_result->num_rows grades entered.";
        		}
        		print "</p>\n";
                print "</a></li>\n";
        		$count++;
    	    }
    	} // if there are any assignments of that type

    }

    /*
    if( $assignments_result->num_rows > 0 ) {
        print "<ul data-role=\"listview\" data-theme='c' data-inset=\"true\">\n";
	$type = '';
	$count = 1;

	while( $assignment = $assignments_result->fetch_object( ) ) {
	    if( $assignment->grade_type != $type ) {
		$type = $assignment->grade_type;
		print "<li data-role=\"list-divider\">$type</li>\n";
		$first = false;
		$count = 1;
	    }
	    print "<li>";
	    if( $assignment->title != '' ) {
		print "<strong>$assignment->title</strong>\n";
	    } else {
		print "<strong>$type #$count</strong>\n";
	    }
	    print '<p class="ui-li-aside">' . date( 'D, M j', strtotime( $assignment->due_date ) );
	    print "</p>\n";
	    print "</li>\n";
	    $count++;
	}
	print "</ul>\n";

    } // if there are any assignments
    */
} else {
    print 'You are not authorized to view this page.';
}

require_once( '../_footer.inc' );
?>