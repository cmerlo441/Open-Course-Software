<?php

$no_header = 1;
require_once( './_header.inc' );

$first = $db->real_escape_string( $_POST[ 'first' ] );
$email = $db->real_escape_string( $_POST[ 'email' ] );

print wordwrap( "$first, there was a problem creating your account.  Please "
    . "try again in a few minutes.  If the problem persists, please contact "
    . "your instructor via e-mail." );
   
?>
