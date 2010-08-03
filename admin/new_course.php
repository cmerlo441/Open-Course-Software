<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
	
	if( preg_match( "/^[A-Za-z]{3}$/", trim( $_POST[ 'dept' ] ) ) == 1 ) {
		$values[ 'dept' ] = strtoupper( trim( $_POST[ 'dept' ] ) );
	} else {
		print "Invalid request: dept";
		die();
	}
	
	if( preg_match( "/^[A-Za-z0-9]+$/", trim( $_POST[ 'course' ] ) ) == 1 ) {
		$values[ 'course' ] = strtoupper( trim( $_POST[ 'course' ] ) );
	} else {
		print "Invalid request: course";
		die();
	}

	if( preg_match( "/^[0-9]+$/", trim( $_POST[ 'credits' ] ) ) == 1 ) {
		$values[ 'credits' ] = $_POST[ 'credits' ];
	} else {
		print "Invalid request: credits";
		die();
	}
	
	if( strlen( trim( $_POST[ 'short_name' ] ) ) <= 25 ) {
		$values[ 'short_name' ] = htmlentities( trim( $_POST[ 'short_name' ] ) );
	} else {
		print "Invalid request: short_name";
		die();
	}
	
	$values[ 'long_name' ] = htmlentities( trim( $_POST[ 'long_name' ] ) );
	
	if( strlen( trim( $_POST[ 'prereq' ] ) ) <= 250 ) {
		$values[ 'prereq' ] = htmlentities( trim( $_POST[ 'prereq' ] ) );
	} else {
		print "Invalid request: short_name";
		die();
	}
	
	$values[ 'catalog' ] = htmlentities( trim( $_POST[ 'catalog' ] ) );

	$insert_query = 'insert into courses '
		. '( id, dept, course, credits, short_name, long_name, prereq, catalog ) values '
		. "( null, \"{$values[ 'dept' ]}\", \"{$values[ 'course' ]}\", "
		. "\"{$values[ 'credits' ]}\", \"{$values[ 'short_name' ]}\", "
		. "\"{$values[ 'long_name' ]}\", \"{$values[ 'prereq' ]}\", "
		. "\"{$values[ 'catalog' ]}\" )";
	$insert_result = $db->query( $insert_query );
	
	if( $db->affected_rows == 1 ) {
		
		/* This creates the same kind of table as in admin/courses.php, so it
		 * can get sent back and displayed
		 */
		 
		$courses_query = 'select * from courses';
		$courses_result = $db->query( $courses_query );
		if( $courses_result->num_rows == 0 ) {
			print "<p>There are no courses in the database.</p>\n";
		} else {
	
			print "<table class=\"tablesorter\" id=\"courses_table\">\n";
			print "<thead>\n";
			print "<tr>\n";
			print "  <th>Department</th>\n";
			print "  <th>Course</th>\n";
			print "  <th>Credits</th>\n";
			print "  <th>Short Name</th>\n";
			print "</tr>\n";
			print "</thead>\n\n";
			
			print "<tbody>\n";
			while( $row = $courses_result->fetch_assoc( ) ) {
				print "<tr>\n";
				print "  <td name=\"{$row[ 'id' ]}\" class=\"dept\">{$row[ 'dept' ]}</td>\n";
				print "  <td name=\"{$row[ 'id' ]}\" class=\"course\">{$row[ 'course' ]}</td>\n";
				print "  <td name=\"{$row[ 'id' ]}\" class=\"credits\">{$row[ 'credits' ]}</td>\n";
				print "  <td name=\"{$row[ 'id' ]}\" class=\"short_name\">{$row[ 'short_name' ]}</td>\n";
				print "</tr>\n";
			}
            $courses_result->close( );
			print "</tbody>\n";
			print "</table>\n";
	
		}
	} else {
		print "Invalid request: unknown DB problem";
	}
	
} else {
	print "Invalid request: not admin";
}

?>