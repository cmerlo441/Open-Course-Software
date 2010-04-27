<?php

$title_stub = '';
require_once( './_header.inc' );

$title_slug = $db->real_escape_string( $_GET[ 'slug' ] );
$page_query = "select * from pages where slug = \"$title_slug\"";
$page_result = $db->query( $page_query );
if( $page_result->num_rows == 0 ) {
    print "<h1>Page Not Found</h1>\n";
    print "<p>That page can't be found.</p>\n";
} else {
    $page = $page_result->fetch_assoc( );
    print html_entity_decode( $page[ 'text' ] );
    print "<script type=\"text/javascript\">\n";
    print "$(document).ready(function(){\n";
    print "    $(document).attr('title', $(document).attr('title') + ' :: {$page[ 'title' ]}');\n";
    print "    $(\"h1\").html(\"{$page[ 'title' ]}\");\n";
    print "})\n";
    print "</script>\n";
}

$lastmod = strtotime( $page[ 'last_modified' ] );
include( "$fileroot/_footer.inc" );

?>
