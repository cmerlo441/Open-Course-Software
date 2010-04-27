<?php

$title_stub = 'Contact Information';
require_once( './_header.inc' );

$contact_query = 'select * from contact_information order by sequence';
$contact_result = $db->query( $contact_query );
while( $contact_row = $contact_result->fetch_assoc( ) ) {
    print "<div class=\"contact_info\" id=\"{$contact_row[ 'type' ]}\">\n";
    print "<h2>{$contact_row[ 'description' ]}</h2>\n";
    print wordwrap( "<p>{$contact_row[ 'contact_info' ]}</p>\n" );
    print "</div>\n";
}
   
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
