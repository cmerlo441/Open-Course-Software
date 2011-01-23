<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $course = $db->real_escape_string( $_REQUEST[ 'course' ] );
    $grade_type = $db->real_escape_string( $_REQUEST[ 'grade_type' ] );

    // Is there already an entry in the database for this?
    $drop_query = 'select id from drop_lowest '
	. "where course = $course and grade_type = $grade_type";
    $drop_result = $db->query( $drop_query );
    if( $drop_result->num_rows == 1 ) {

	// Yes.  Remove it.
	$drop_row = $drop_result->fetch_object( );
	$toggle_query = 'delete from drop_lowest '
	    . "where id = $drop_row->id";

    } else {

	// No.  Add it.
	$toggle_query = 'insert into drop_lowest( id, course, grade_type ) '
	    . "values( null, $course, $grade_type )";
    }
    $toggle_result = $db->query( $toggle_query );
}