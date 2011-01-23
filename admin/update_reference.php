<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    if( isset( $_POST[ 'id_to_delete' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'id_to_delete' ] );
        $delete_query = 'delete from reference '
            . "where id = $id";
        $delete_result = $db->query( $delete_query );
    }
    
    if( isset( $_POST[ 'available' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'available' ] );
        $avail_query = 'select available from reference '
            . "where id = $id";
        $avail_result = $db->query( $avail_query );
        $avail_row = $avail_result->fetch_assoc( );
        $avail = $avail_row[ 'available' ];
        
        $update_query = 'update reference '
            . 'set available = '
            . ( $avail == 0 ? '1' : '0' )
            . " where id = $id";
        $update_result = $db->query( $update_query );
    }
}

?>