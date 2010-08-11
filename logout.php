<?php

$no_header = 1;
require_once( './_header.inc' );

foreach( $_SESSION as $key=>$value ) {
    unset( $_SESSION[ $key ] );
}

?>