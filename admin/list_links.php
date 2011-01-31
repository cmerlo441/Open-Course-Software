<?php 
$no_header = 1;
require_once ( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    if( isset( $_POST[ 'url' ] ) and isset( $_POST[ 'link_text' ] ) ) {
        $url = $db->real_escape_string( $_POST[ 'url' ] );
        $link_text = $db->real_escape_string( $_POST[ 'link_text' ] );
        $db->query( 'insert into links( id, url, link_text, created ) '."values( null, \"$url\", \"$link_text\", ".'"'.date( 'Y-m-d H:i:s' ).'" )' );
    }
    
    $links_query = 'select * from links order by created';
    $links_result = $db->query( $links_query );
    if( $links_result->num_rows == 0)
        print 'None.';
    else {
        print "<ul>\n";
        while( $link = $links_result->fetch_object() ) {
            print "<li id=\"$link->id\">";
            print "<a href=\"javascript:void(0)\" class=\"delete\" id=\"$link->id\">";
            print "<img src=\"$docroot/images/silk_icons/delete.png\" "
                . "width=\"16\" height=\"16\" title=\"Delete this link\" /></a>\n";
            print "\"$link->link_text\" "
                . "(<a href=\"$link->url\">$link->url</a>)";
        }
        print "</ul>\n";
    }
}
