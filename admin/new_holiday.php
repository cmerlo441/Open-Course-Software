<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $date        = $db->real_escape_string( $_POST[ 'date' ] );
    $description = $db->real_escape_string( $_POST[ 'description' ] );
    $day         = $db->real_escape_string( $_POST[ 'day' ] );
    $evening     = $db->real_escape_string( $_POST[ 'evening' ] );

    $insert_query = 'insert into holidays ( date, description, day, evening ) values '
        . "( \"$date\", \"$description\", \"$day\", \"$evening\" )";
    $insert_result = $db->query( $insert_query );
    
    if( $db->affected_rows == 1 ) {
        print "ok";
    } else {
        print "Invalid request: Something bad happened";
    }        
}
?>