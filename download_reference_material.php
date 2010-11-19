<?php

$no_header = 1;
require_once( './_header.inc' );

$id = $db->real_escape_string( $_GET[ 'id' ] );
$doc_query = 'select * from reference '
    . "where id = $id and available = 1";
$doc_result = $db->query( $doc_query );

if( $doc_result->num_rows == 1 ) {

    if( $_SESSION[ 'student' ] >= 1 ) {
	$insert_query = 'insert into reference_downloads '
	    . '( id, reference, student, datetime ) values '
	    . "( null, \"$id\", \"{$_SESSION[ 'student' ]}\", "
	    . '"' . date( 'Y-m-d H:i:s' ) . '" )';
	$insert_result = $db->query( $insert_query );
    }

    $doc = $doc_result->fetch_assoc( );
    header( "Content-type: {$doc[ 'type' ]}" );
    header( "Content-length: {$doc[ 'size' ]}" );
    header( "Content-Disposition: attachment; "
            . "filename=" . urlencode( $doc[ 'filename' ] ) );
    print $doc[ 'file' ];
}

?>