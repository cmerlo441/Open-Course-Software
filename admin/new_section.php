<?php
   
$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $values = array();
    
    if( $_POST[ 'course' ] != 'null' ) {
        $values[ 'course' ] = $db->real_escape_string( $_POST[ 'course' ] );
    } else {
        print "Invalid request: course";
        die();
    }
    
    if( preg_match( "/^([A-Za-z0-9]+)$/", trim( $_POST[ 'section' ] ), $matches ) == 1 ) {
        $values[ 'section' ] = strtoupper( $matches[ 1 ] );
    } else {
		print "Invalid request: section";
		die();
    }
    
    if( preg_match( "/^([0-9]+)$/", trim( $_POST[ 'banner' ] ), $matches ) == 1 ) {
        $values[ 'banner' ] = $matches[ 1 ];
    } else {
		print "Invalid request: banner";
		die();
    }
    
    if( trim( strtolower( $_POST[ 'day_eve' ] ) ) == 'day' ) {
        $values[ 'day' ] = 1;
    } else if( trim( strtolower( $_POST[ 'day_eve' ] ) ) == 'evening' ) {
        $values[ 'day' ] = 0;
    } else {
        print "Invalid request: day_eve";
        die();
    }
    
    $db->query( 'lock table sections' );
    $insert_query = 'insert into sections ( id, course, section, banner, day ) '
        . "values( null, \"{$values[ 'course' ]}\", "
        . "\"{$values[ 'section' ]}\", \"{$values[ 'banner' ]}\", "
        . "\"{$values[ 'day' ]}\" )";
    $insert_result = $db->query( $insert_query );
	if( $db->affected_rows == 0 ) {
		print "Invalid request: inserting section into DB";
		$db->query( 'unlock tables' );
		die();
	}

    $id_query = 'select id from sections order by id desc limit 1';
    $id_result = $db->query( $id_query );
    $id_row = $id_result->fetch_assoc( );
    $id_result->close( );
    $section = $id_row[ 'id' ];
    $db->query( 'unlock tables' );

    $id = 1;
    while( isset( $_POST[ "meeting{$id}day" ] ) ) {
        $day        = $db->real_escape_string( $_POST[ "meeting{$id}day" ] );
    	$post_start = trim( $db->real_escape_string( $_POST[ "meeting{$id}start" ] ) );
    	$post_end   = trim( $db->real_escape_string( $_POST[ "meeting{$id}end" ] ) );
        $start      = date( 'H:i:s', strtotime( $post_start ) );
        $end        = date( 'H:i:s', strtotime( $post_end ) );
        $building   = $db->real_escape_string( $_POST[ "meeting{$id}building" ] );
        $room       = $db->real_escape_string( $_POST[ "meeting{$id}room" ] );
        
        $insert_query = 'insert into section_meetings '
            . '( id, section, day, start, end, building, room ) values '
            . "( null, \"$section\", \"$day\", \"$start\", \"$end\", "
            . "\"$building\", \"$room\" )";
        $insert_result = $db->query( $insert_query );
        if( $db->affected_rows == 0 ) {
            print "Invalid request: inserting meeting into DB";
        }
        $id++;
        print $insert_query;
    }
    print "Ok";
    
} else {
    print "Invalid request: not admin";
}
	
?>
