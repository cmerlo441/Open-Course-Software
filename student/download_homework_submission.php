<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
	$query = 'select file, filesize, filename, filetype '
		. 'from assignment_uploads '
		. 'where id = "' . $db->real_escape_string( $_REQUEST[ 'id' ] ) . '" '
		. "and student = \"{$_SESSION[ 'student' ]}\"";
	$result = $db->query( $query );
	$file = $result->fetch_object( );

	header( "Content-type: $file->filetype" );
	header( "Content-length: $file->filesize" );
	header( "Content-Disposition: attachment; filename=" . urlencode( $file->filename ) );
	print $file->file;
}

?>