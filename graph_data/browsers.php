<?php

$no_header = 1;
require_once( '../_header.inc' );
require_once( './jsonwrapper/jsonwrapper.php' );

$browsers = array( );
    
$login_query = 'select browser from logins';
$login_result = $db->query( $login_query );
while( $login_row = $login_result->fetch_assoc( ) ) {
    $name = $login_row[ 'browser' ];
    
    $browser_names = explode(' ', 'Firefox Opera Chrome AppleWebKit MSIE' );
    $found = FALSE;
    foreach( $browser_names as $browser_name ) {
        if( preg_match( "/$browser_name/", $name ) ) {
            $found = TRUE;
            if( $browser_name == 'AppleWebKit' ) $browser_name = 'Safari';
            if( $browser_name == 'MSIE' ) $browser_name = 'Explorer';
            $key = strtolower( str_replace( array( ' ', '.' ), '_', $browser_name ) );
            $browsers[ $key ][ 'name' ] = $browser_name;
            $browsers[ $key ][ 'count' ]++;
            break;
        }
    }
    if( ! $found ) {
        $browsers[ 'other' ][ 'count' ]++;
    }
}
if( $browsers[ 'other' ][ 'count' ] > 0 ) {
    $browsers[ 'other' ][ 'name' ] = 'Other';
}

foreach( $browsers as $key=>$row ) {
    $names[ $key ] = $row[ 'name' ];
    $counts[ $key ] = $row[ 'count' ];
}

array_multisort( $counts, SORT_DESC, $names, SORT_ASC, $browsers );
    
$json = json_encode( $browsers );
print $json;
?>
