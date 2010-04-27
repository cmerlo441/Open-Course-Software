<?php

$no_header = 1;
require_once( './_header.inc' );

print wordwrap( "<p>If you would like to reset your password, type your N "
    . "number into the box below.</p>\n" );
print "<br />\n";
print wordwrap( "<p><b>Please Note:</b>  A message will be sent to your "
    . "NCC student e-mail account.  If you have not set that account up, "
    . "you need to <a href=\"http://www.ncc.edu/studentemail/\">do that first</a>."
    . "</p>\n" );
print "<br />\n";
print "<p>Enter your N number: <input type=\"text\" id=\"banner_id\" /></p>\n";

?>
