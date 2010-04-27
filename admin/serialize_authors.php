<?php

require_once( '/home/cmerlo/.htpasswd' );
session_start( );

if( $_SESSION[ 'admin' ] == 1 ) {
	$authors_query = 'select id, first, middle, last from authors '
		. 'order by last, first, middle, id';
	$authors_result = $db->query( $authors_query );
	$first = true;
	while( $row = $authors_result->fetch_assoc( ) ) {
		if( $first == false ) {
			$return_me .= ", ";
		}
		$first = false;
		$return_me .= $row[ 'first' ] . ' ';
		if( $row[ 'middle' ] != '' ) {
			$return_me .= $row[ 'middle' ] . ' ';
		}
		$return_me .= "{$row[ 'last' ]}:{$row[ 'id' ]}";
	}
	print $return_me;
}
?>