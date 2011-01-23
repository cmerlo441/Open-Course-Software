<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $id = $db->real_escape_string( $_GET[ 'id' ] );
    $file_query = 'select * from assignment_uploads '
	. "where id = $id";
    $file_result = $db->query( $file_query );

    if( $file_result->num_rows == 1 ) {
	$file = $file_result->fetch_assoc( );

	header( "Content-type: {$file[ 'filetype' ]}" );
	header( "Content-length: {$file[ 'filesize' ]}" );
	header( "Content-Disposition: attachment; "
		. "filename=" . urlencode( $file[ 'filename' ] ) );
	print $file[ 'file' ];
    }
}

?>
