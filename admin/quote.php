<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $id = $db->real_escape_string( $_REQUEST[ 'id' ] );
    
    $query = 'select quote, attribution '
        . 'from quotes '
        . "where id = $id";
    $result = $db->query( $query );
    $quote = $result->fetch_object( );
?>

<label for="quote">Quote:</label><br />
<textarea id="quote" cols="40" rows="3"><?php echo $quote->quote; ?></textarea><br /><br />
<label for="attribution">By:</label><br />
<input id="attribution" type="text" size="40" value="<?php echo $quote->attribution; ?>" />

<?php
}
?>