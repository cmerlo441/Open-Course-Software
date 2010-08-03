<?php

$title_stub = 'OCSW Configuration Options';
require_once( "../_header.inc" );

if( $_SESSION[ 'admin' ] == 1 ) {

    // Basic options
    
    print "<h2>Basic Options</h2>\n";
    print "<table>\n";

    $basic_options_query = 'select id, k, v, q from ocsw '
        . 'where advanced = 0';
    $basic_options_result = $db->query( $basic_options_query );
    while( $option = $basic_options_result->fetch_assoc( ) ) {
        print "    <tr>\n";
        print "        <td>{$option[ 'q' ]}</td>\n";
        print "        <td><input type=\"checkbox\" id=\"{$option[ 'k' ]}\""
            . ( $option[ 'v' ] == 1 ? ' checked' : '' ) . " /></td>\n";
        print "    </tr>\n";
    }
    print "</table>\n";
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
