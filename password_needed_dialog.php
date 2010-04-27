<?php

$no_header = 1;
require_once( './_header.inc' );

$first = $db->real_escape_string( $_POST[ 'first' ] );
$email = $db->real_escape_string( $_POST[ 'email' ] );

print wordwrap( "$first, an e-mail has been sent to $email.  You must follow "
    . "the instructions in that e-mail in order to finish creating your "
    . "account." );
   
?>
