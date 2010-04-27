<?php

$no_header = 1;
require_once( '../_header.inc' );
require_once( './jsonwrapper/jsonwrapper.php' );

$days = array( );
for( $i = 0; $i < 7; $i++ ) {
    $days[ $i ] = 0;
}

$login_query = 'select datetime from logins';
$login_result = $db->query( $login_query );
while( $login_row = $login_result->fetch_assoc( ) ) {
  $day = date( 'w', strtotime( $login_row[ 'datetime' ] ) );
  $days[ $day ]++;
}

ksort( $days );
print json_encode( $days );
?>