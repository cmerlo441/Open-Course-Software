<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
	$pubs_query = 'select id, name from publishers '
		. 'order by name, id';
	$pubs_result = $db->query( $pubs_query );
	$first = true;
	while( $row = $pubs_result->fetch_assoc( ) ) {
		if( $first == false ) {
			$return_me .= ", ";
		}
		$first = false;
		$return_me .= "{$row[ 'name' ]}:{$row[ 'id' ]}";
	}
	print $return_me;
}
?>