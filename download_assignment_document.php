<?php

$no_header = 1;
require_once( './_header.inc' );

$id = $db->real_escape_string( $_GET[ 'id' ] );
$doc_query = 'select * from assignment_documents '
    . "where id = $id";
$doc_result = $db->query( $doc_query );

if( $doc_result->num_rows == 1 ) {
    $doc = $doc_result->fetch_assoc( );
    header( "Content-type: {$doc[ 'type' ]}" );
    header( "Content-length: {$doc[ 'size' ]}" );
    header( "Content-Disposition: attachment; "
            . "filename=" . urlencode( $doc[ 'name' ] ) );
    print $doc[ 'file' ];
}

?>
