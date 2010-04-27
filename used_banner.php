<?php

$no_header = 1;
require_once( './_header.inc' );

$first = $db->real_escape_string( $_POST[ 'first' ] );
$email = $db->real_escape_string( $_POST[ 'email' ] );

print wordwrap( "$first, it seems that you have already requested an "
    . "account.  Please check your e-mail for a message to finish "
    . "creating that account." );
   
?>
