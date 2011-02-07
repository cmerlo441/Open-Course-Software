<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $update_value = $db->real_escape_string( trim( $_POST[ 'update_value' ] ) );
    $original_html = $db->real_escape_string( trim( $_POST[ 'original_html' ] ) );
    $id = $db->real_escape_string( trim( $_POST[ 'id' ] ) );
    $column = $db->real_escape_string( trim( $_POST[ 'column' ] ) );
    
    if( $update_value != $original_html ) {
	$update_query = "update assignments set $column = \"$update_value\" "
            . "where id = \"$id\"";
	$update_result = $db->query( $update_query );
	if( $db->affected_rows == 1 ) {
	    $new_data_query = "select $column as x from assignments "
		. "where id = \"$id\"";
	    $new_data_result = $db->query( $new_data_query );
            $new_data_row = $new_data_result->fetch_assoc( );
            $new_data_result->close( );
	    print stripslashes( nl2br( $new_data_row[ 'x' ] ) );
	} else {
	    print $original_html;
	}
    } else {
	print $original_html;
    }
}

?>