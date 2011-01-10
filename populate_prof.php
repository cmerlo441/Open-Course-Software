<?php

$no_header = 1;
require_once( './_header.inc' );

if( isset( $_POST[ 'first' ] ) && isset( $_POST[ 'last' ] ) &&
    isset( $_POST[ 'username' ] ) && isset( $_POST[ 'password' ] ) ) {
        
    // Make sure we're not overwriting something that's there already
    
    $rows_query = 'select count( id ) as c from prof';
    $rows_result = $db->query( $rows_query );
    $rows_row = $rows_result->fetch_object( );
    if( $rows_row->c == 0 ) {
    
        $first = $db->real_escape_string( $_POST[ 'first' ] );
        $last = $db->real_escape_string( $_POST[ 'last' ] );
        $username = $db->real_escape_string( $_POST[ 'username' ] );
        $password = $db->real_escape_string( $_POST[ 'password' ] );
        $db->query( 'lock tables prof' );
        $db->query( 'truncate table prof' );
        $db->query( 'insert into prof( id, first, last, username, password ) '
            . "values( null, \"$first\", \"$last\", \"$username\", "
            . "\"$password\" )" );
        $db->query( 'unlock tables' );
    }
}

?>