<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
	if( trim( $_POST[ 'update_value' ] ) != trim( $_POST[ 'original_html' ] ) ) {
		$update_query = "update authors set {$_POST[ 'column' ]} = \""
			. htmlentities( trim( $_POST[ 'update_value' ] ) )
			. "\" where id = \"{$_POST[ 'element_id' ]}\"";
		$update_result = $db->query( $update_query );
		if( $db->affected_rows == 1 ) {
			$new_data_query = "select {$_POST[ 'column' ]} as x from authors "
				. "where id = \"{$_POST[ 'element_id' ]}\"";
			$new_data_result = $db->query( $new_data_query );
            $new_data_row = $new_data_result->fetch_assoc( );
            $new_data_result->close( );
            if( $_POST[ 'column' ] == 'edition' ) {
                print number_suffix( $new_data_row[ 'x' ] );
            } else {
    			print $new_data_row[ 'x' ];
            }
		} else {
			print $_POST[ 'original_html' ];
		}
	} else {
		print $_POST[ 'original_html' ];
	}
}

?>
