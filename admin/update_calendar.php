<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    // Changing semester name
    if( isset( $_POST[ 'update_value' ] ) ) {
    
	$update_query = 'select count( * ) as c from semester';
	$update_result = $db->query( $update_query );
	$update_row = $update_result->fetch_assoc( );
	$update = $semester_row[ 'c' ];

    	if( trim( $_POST[ 'update_value' ] ) != trim( $_POST[ 'original_html' ] ) ) {

	    $update_value = $db->real_escape_string( $update_value );

	    if( $update == 1 ) {
		$update_query = "update semester set name = \"$update_value\"";
		$update_result = $db->query( $update_query );
	    } else {
		$insert_query = 'insert into semester( name ) '
		    . "values( \"$update_value\" )";
	    }
    
            $select_query = 'select name from semester';
            $select_result = $db->query( $select_query );
            $select_row = $select_result->fetch_assoc( );
            $select_result->close( );
            print $select_row[ 'name' ];

    	} else {
    		print $_POST[ 'original_html' ];
    	}
    	
	// Changing semester start and end dates
    } else {
        preg_match( "/^semester_(.*)$/",
		    $db->real_escape_string( $_POST[ 'column' ] ),
		    $matches );
        $column = $matches[ 1 ];

	if( $update == 1 ) {
	    $update_query = "update semester set $column = \""
		. date( 'Y-m-d',
			strtotime( $db->real_escape_string( $_POST[ 'date' ] ) ) ) . "\"";
	    $update_result = $db->query( $update_query );
	    $update_result->close( );
	} else {
	    $insert_query = "insert into semester ( $column ) "
		. "values( \""
		. date( 'Y-m-d',
			strtotime( $db->real_escape_string( $_POST[ 'date' ] ) ) )
		. "\" )";
	    $insert_result = $db->query( $insert_query );
	}
        
        $select_query = "select $column from semester";
        $select_result = $db->query( $select_query );
        $select_row = $select_result->fetch_assoc( );
        $select_result->close( );
        print date( 'l, F j, Y', strtotime( $select_row[ $column ] ) );
    }
}

?>