<?php

$no_header = 1;
require_once( '../_header.inc' );

if( isset( $_POST[ 'p1' ] ) and isset( $_POST[ 'banner_id' ] ) ) {
    $banner_id = strtolower( $db->real_escape_string( $_POST[ 'banner_id' ] ) );
    $password = $db->real_escape_string( $_POST[ 'p1' ] );
    $reset_query = "update students set password = md5( \"$password\" ) "
        . "where banner = \"$banner_id\"";
    $reset_result = $db->query( $reset_query );
    if( $db->affected_rows == 1 ) {
        print 'Your password has been changed.';
    }
    
    $db->query( 'delete from password_reset_request '
        . "where banner_id = \"$banner_id\"" );
}

?>
