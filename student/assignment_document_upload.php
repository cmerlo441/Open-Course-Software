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

    $requirement = $db->real_escape_string( $_POST[ 'requirement' ] );
    $student = $db->real_escape_string( $_POST[ 'student' ] );

    $insert_query = 'insert into assignment_uploads '
        . '( id, student, assignment_upload_requirement, filename, '
	. 'filesize, filetype, datetime, file ) '
	. "values ( null, \"$student\", \"$requirement\", "
	. "\"{$_FILES[ 'file' ][ 'name' ]}\", "
        . "\"{$_FILES[ 'file' ][ 'size' ]}\", "
	. "\"{$_FILES[ 'file' ][ 'type' ]}\", "
	.'"' . date( 'Y-m-d H:i:s' ) . '", '
        . "\"$file\" )";
    $insert_result = $db->query( $insert_query );

    print 'Done.';
} else {
    print 'Error.';
}

?>