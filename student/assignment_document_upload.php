<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_FILES[ 'file' ][ 'error' ] == 0 ) {

    $fh = fopen( $_FILES[ 'file' ][ 'tmp_name' ], 'r' );
    $line = fgets( $fh );
    $file = '';
    
    while( $line ) {
        $file .= $line;
        $line = fgets( $fh );
    }
    
    $file = $db->real_escape_string( $file );

    $requirement = $db->real_escape_string( $_POST[ 'requirement' ] );
    $student = $db->real_escape_string( $_POST[ 'student' ] );

    $db->query( 'lock table assignment_uploads' );
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
    $id = $db->insert_id;
    $db->query( 'unlock tables' );

    $files_query = 'select filename, filesize, datetime '
	. 'from assignment_uploads '
	. "where id = $id";
    $files_result = $db->query( $files_query );
    if( $files_result->num_rows == 0 ) {
	print $files_query;
    } else {
	$file_row = $files_result->fetch_object( );
	print "You uploaded $file_row->filename "
	    . "($file_row->filesize bytes) on "
	    . date( 'l, F j, Y \a\t g:i a',
		    strtotime( $file_row->datetime ) )
	    . ".";
    }
} else {
    print 'Error.';
}

?>