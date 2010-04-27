<?php

$no_header = 1;
require_once( './_header.inc' );

$first = $db->real_escape_string( $_POST[ 'first' ] );
$email = $db->real_escape_string( $_POST[ 'email' ] );

print wordwrap( "$first, the MyNCC ID number you typed in was not "
    . "formatted properly.  Please try again." );
   
?>
