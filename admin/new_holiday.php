<?php

require_once( '/home/cmerlo/.htpasswd' );
session_start( );

if( $_SESSION[ 'admin' ] == 1 ) {
    $insert_query = 'insert into holidays ( date, description, day, evening ) values '
        . '( "' . htmlentities( trim( $_POST[ 'date' ] ) ) . '", '
        . '"' . htmlentities( trim( $_POST[ 'description' ] ) ) . '", '
        . "{$_POST[ 'day' ]}, {$_POST[ 'evening' ]} )";
    print "<script type=\"text/javascript\">alert( $insert_query )</script>\n";
    $insert_result = $db->query( $insert_query );
    
    if( $db->affected_rows == 1 ) {
        print "ok";
    } else {
        print "Invalid request: Something bad happened";
    }        
}
?>