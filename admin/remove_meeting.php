<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $meeting = $db->real_escape_string( $_POST[ 'meeting' ] );
    $db->query( 'delete from section_meetings '
        . "where id = \"$meeting\"");
}

?>