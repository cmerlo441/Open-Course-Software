<?php

$title_stub = 'Contact Information';
require_once( './_header.inc' );

$contact_query = 'select contact_info from contact_info';
$contact_result = $db->query( $contact_query );
$contact_row = $contact_result->fetch_object( );
$info = $contact_row->contact_info;
print nl2br( $info );
   
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
