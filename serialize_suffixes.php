<?php

$no_header = 1;
require_once( './_header.inc' );

$suffixes_query = 'select id, suffix from suffixes '
    . 'where id > 0 '
	. 'order by suffix, id';
$suffixes_result = $db->query( $suffixes_query );
$return_me = 'No suffix:0';
while( $row = $suffixes_result->fetch_assoc( ) ) {
	$return_me .= ", {$row[ 'suffix' ]}:{$row[ 'id' ]}";
}
print $return_me;
?>