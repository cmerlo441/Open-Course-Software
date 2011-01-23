<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $k = $db->real_escape_string( $_POST[ 'k' ] );

    $query = "select v from ocsw where k = \"$k\"";
    $result = $db->query( $query );
    $row = $result->fetch_object( );

    $query = 'update ocsw '
	. "set v = " . ( $row->v == 1 ? 0 : 1 )
	. " where k = \"$k\"";
    $result = $db->query( $query );
}

?>
