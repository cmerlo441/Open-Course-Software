<?php

$no_header = 1;
require_once( '../_header.inc' );
require_once( './jsonwrapper/jsonwrapper.php' );

$hours = array( );
for( $i = 0; $i < 24; $i++ ) {
    $hours[ $i ] = 0;
}

$login_query = 'select datetime from logins';
$login_result = $db->query( $login_query );
while( $login_row = $login_result->fetch_assoc( ) ) {
  $hour = date( 'G', strtotime( $login_row[ 'datetime' ] ) );
  $hours[ $hour ]++;
}

ksort( $hours );
print json_encode( $hours );
?>