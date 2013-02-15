<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_FILES[ 'file' ][ 'error' ] == 0 ) {

    $fh = fopen( $_FILES[ 'file' ][ 'tmp_name' ], 'r' );
    $line = fgets( $fh );
    
    while( $line ) {
        $file .= $line;
        $line = fgets( $fh );
    }
    
    $file = $db->real_escape_string( $file );
    $section = $db->real_escape_string( $_REQUEST[ 'section' ] );

    $insert_query = 'insert into reference '
        . '( id, filename, size, type, section, uploaded, available, file ) values '
        . "( null, \"{$_FILES[ 'file' ][ 'name' ]}\", \"{$_FILES[ 'file' ][ 'size' ]}\", "
        . "\"{$_FILES[ 'file' ][ 'type' ]}\", "
        . "\"$section\", \"" . date( 'Y-m-d H:i:s' ) . "\", 0, \"$file\" )";
    //print "<script type=\"text/javascript\">alert( $insert_query );</script>\n";
    $insert_result = $db->query( $insert_query );
    if( $db->affected_rows == 1 ) {
    	// Change this to be the ID of the newly-entered file
        print 'Done.';
    } else {
        print 'Database error.';
    }
} else {
    print 'Upload error.';
}

?>