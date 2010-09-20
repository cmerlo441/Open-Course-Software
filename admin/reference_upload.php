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
    $section = $db->real_escape_string( $_POST[ 'section' ] );

    $insert_query = 'insert into reference '
        . '( id, filename, size, type, section, uploaded, available, file ) values '
        . "( null, \"{$_FILES[ 'file' ][ 'name' ]}\", \"{$_FILES[ 'file' ][ 'size' ]}\", "
        . "\"{$_FILES[ 'file' ][ 'type' ]}\", "
        . "\"$section\", \"" . date( 'Y-m-d H:i:s' ) . "\", 0, \"$file\" )";
    $insert_result = $db->query( $insert_query );

    print 'Done.';
} else {
    print 'Error.';
}

?>