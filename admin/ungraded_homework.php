<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $found = false;

    print "<div id=\"ungraded_homework_internal\">\n";

    $sections_query = 'select c.dept, c.course, s.id, s.section '
	. 'from courses as c, sections as s '
	. 'where s.course = c.id '
	. 'order by c.dept, c.course, s.section';
    $sections_result = $db->query( $sections_query );
    while( $section = $sections_result->fetch_object( ) ) {

	$section_name = "$section->dept $section->course $section->section";
	
	$hw_query = 'select a.id, a.due_date, a.title '
	    . 'from assignments as a, grade_events as e, grade_types as t, grade_weights as w, sections as s '
	    . "where s.id = $section->id "
	    . 'and a.section = s.id '
	    . 'and e.assignment = a.id '
	    . 'and a.grade_type = t.id '
	    . 'and w.grade_type = t.id '
	    . 'and t.grade_type = "Homework" '
	    . 'and w.course = s.course '
	    . 'and w.collected = 1 '
	    . 'and a.due_date < "' . date( 'Y-m-d H:i:s' ) . '" '
	    . 'order by due_date';
	
	$hw_result = $db->query( $hw_query );
	
	$assignment_count = 0;
	while( $hw = $hw_result->fetch_object( ) ) {
	    $grades_query = 'select count( g.id ) as c from grades as g, grade_events as e '
		. 'where g.grade_event = e.id '
		. "and e.assignment = $hw->id";
	    $grades_result = $db->query( $grades_query );
	    $count = $grades_result->fetch_object( );
	    if( $count->c == 0 ) {
		$found = true;
		if( ++$assignment_count == 1 ) {
		    print "<h3>$section_name</h3>\n";
		    print "<ul>\n";
		}
		print "  <li><span style=\"font-weight: bold;\">";
		print_link( "$docroot/admin/assignment.php?assignment=$hw->id",
			    ( $hw->title == '' ? 'No title' : $hw->title ) );
		print "</span> (Due "
		    . date( 'l, F j', strtotime( $hw->due_date) ) . ")</li>\n";
	    }
	}
	print "</ul>\n";
    } // for all the sections

    print $found;
    if( ! $found )
	print 'None.';
	
    print "</div>  <!-- div#ungraded_homework_internal -->\n";

}