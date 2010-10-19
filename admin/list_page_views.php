<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $start = isset( $_POST[ 'start' ] )
	? $db->real_escape_string( $_POST[ 'start' ] )
	: 0;
    $amount = isset( $_POST[ 'amount' ] )
	? $db->real_escape_string( $_POST[ 'amount' ] )
	: 25;

    $page_view_query = 'select p.id, p.page, p.get_string, p.datetime, '
	. 'p.referrer, p.ip, '
	. 's.id as student_id, s.first, s.middle, s.last '
	. 'from page_views as p, students as s '
	. 'where p.student = s.id ';
    if( isset( $_POST[ 'student' ] ) and $_POST[ 'student' ] != 0 ) {
	$page_view_query .= 'and s.id = '
	    . $db->real_escape_string( $_POST[ 'student' ] )
	    . ' ';
    } else if( isset( $_POST[ 'page' ] ) and $_POST[ 'page' ] != 0 ) {
	$page_view_query .= 'and page like "%'
	    . $db->real_escape_string( $_POST[ 'page' ] )
	    . '" ';
    }
    $page_view_query .= 'order by p.datetime desc, s.last, s.first, '
	. 's.middle '
	. "limit $start, $amount";
    $page_view_result = $db->query( $page_view_query );
    $row_number = $start;
    print "<div class=\"twenty_five_rows\" style=\"display: none\">\n";
    while( $row = $page_view_result->fetch_assoc( ) ) {
	unset( $section );
	$page = preg_replace( '|^/~[A-Za-z0-9]+/(.*)|',
			      "$1", $row[ 'page' ] );
	if( preg_match( '/section=([0-9]+)/', $row[ 'get_string' ],
			$matches ) ) {
	    $section_query = 'select c.dept, c.course, s.section '
		. 'from courses as c, sections as s '
		. 'where s.course = c.id '
		. "and s.id = {$matches[ 1 ]}";
	    $section_result = $db->query( $section_query );
	    $section_row = $section_result->fetch_assoc( );
	    $section = $section_row[ 'dept' ] . ' '
		. $section_row[ 'course' ] . ' '
		. $section_row[ 'section' ];
	} else if( preg_match( '/slug=(.*)/', $row[ 'get_string' ],
			       $matches ) ) {
	    $section = ucwords( preg_replace( '/_/', ' ', $matches[ 1 ] ) );
	} else if( isset( $row[ 'get_string' ] ) ) {
	    $section = $row[ 'get_string' ];
	}
	$name = ucwords( $row[ 'last' ] ) . ', '
	    . strtoupper( substr( $row[ 'first' ], 0, 1 ) ) . '.';
	$full_name = name( $row );

	print "<div id=\"$row_number\" class=\"page_view\">\n";
	print "  <span class=\"time\" style=\"font-size: 0.8em;\">"
	    . date( 'n/d H:i', strtotime( $row[ 'datetime' ] ) )
	    . "</span>\n";
	print "  <span class=\"student\" id=\"{$row[ 'student_id' ]}\" "
	    . "style=\"font-weight: normal\">\n";
	print "<a href=\"javascript:void(0)\" class=\"student\" "
	    . "id=\"{$row[ 'student_id' ]}\">$full_name</a>\n";
	print "</span>\n";

	print "<div class=\"page_details\" "
	    . "style=\"padding-left: 2em; font-size: 0.8em;\">"
	    . "Viewed \n";
	print "<a href=\"javascript:void(0)\" class=\"page\" "
	    . "id=\"" . urlencode( $page ) . "\">$page</a>\n";
	if( $section != '' ) 
	    print "($section)\n";
	print "<span class=\"ip\">from {$row[ 'ip' ]}</span>\n";
	print "(<a href=\"javascript:void(0)\" class=\"more\" "
	    . "id=\"$row_number\">More</a>)";
	print "</div> <!-- div.page_details -->\n";

	print "<div class=\"referrer\" id=\"$row_number\" "
	    . "style=\"padding-left: 2em; font-size: 0.8em; display: none\">";
	print "Refered from {$row[ 'referrer' ]}";
	print "</div> <!-- div.referrer -->\n";

	print "</div> <!-- div.page_view#$row_number -->\n\n";

	++$row_number;
    }
    print "</div> <!-- twenty_five_rows -->\n";
}

?>
