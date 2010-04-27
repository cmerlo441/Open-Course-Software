<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $query = 'select * from syllabus_section_customization '
        . 'where course = "' . $db->real_escape_string( $_POST[ 'course' ] ) . '" '
        . 'and syllabus_section = "'
        . $db->real_escape_string( $_POST[ 'section' ] ) . '"';
    $result = $db->query( $query );
    if( $result->num_rows == 1 ) {
        $row = $result->fetch_assoc( );
        print $row[ 'value' ];
    }    
}
   
?>
