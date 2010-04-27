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

    $insert_query = 'insert into assignment_documents '
        . '( id, assignment, type, name, size, file ) values '
        . "( null, \"{$_POST[ 'assignment' ]}\", \"{$_FILES[ 'file' ][ 'type' ]}\", "
        . "\"{$_FILES[ 'file' ][ 'name' ]}\", \"{$_FILES[ 'file' ][ 'size' ]}\", "
        . "\"$file\" )";
    $insert_result = $db->query( $insert_query );

    print 'Done.';
} else {
    print 'Error.';
}

?>