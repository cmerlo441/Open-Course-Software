<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $course = $db->real_escape_string( $_POST[ 'course' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    $crn = $db->real_escape_string( $_POST[ 'crn' ] );
    $day = $db->real_escape_string( $_POST[ 'day' ] );
    
    if( $course > 0 and trim( $section ) != '' and trim( $crn ) != '' and ( $day == 0 or $day == 1 ) ) {
        $insert_query = 'insert into sections (id, course, section, banner, day) '
            . "values( null, \"$course\", \"$section\", \"$crn\", \"$day\" )";
        $insert_result = $db->query( $insert_query );
    }
}

?>