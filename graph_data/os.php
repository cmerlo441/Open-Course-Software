<?php

$no_header = 1;
require_once( '../_header.inc' );
require_once( './jsonwrapper/jsonwrapper.php' );

$systems = array( );
    
$login_query = 'select browser from logins';
$login_result = $db->query( $login_query );
while( $login_row = $login_result->fetch_assoc( ) ) {
    $name = $login_row[ 'browser' ];
    
    $os_names = explode(' ', 'iPod iPhone iPad Mac Windows Linux BlackBerry' );
    $found = FALSE;
    foreach( $os_names as $os_name ) {
        if( preg_match( "/$os_name/", $name ) ) {
            $found = TRUE;
	    if( preg_match( '/iP/', $name ) )
		$os_name = 'iOS';
	    else if( preg_match( '/BlackBerry/', $name ) )
		$os_name = 'BBerry';
            else if( preg_match( '/Windows/', $name ) ) {
		if( preg_match( '/NT 6.1/', $name ) )
		    $os_name = 'Win7';
		else if( preg_match( '/NT 6.0/', $name ) )
		    $os_name = 'Vista';
		else if( preg_match( '/NT 5.2/', $name ) )
		    $os_name = 'XP x64';
		else if( preg_match( '/NT 5.1/', $name ) )
		    $os_name = 'XP';
		else if( preg_match( '/NT 5.01/', $name ) )
		    $os_name = 'Win2K SP1';
		else if( preg_match( '/NT 5.0/', $name ) )
		    $os_name = 'Win2K';
	    }

	    else if( preg_match( '/Linux/', $name ) ) {
		if( preg_match( '/Android/', $name ) )
		    $os_name = 'Android';
		/*
		else if( preg_match( '|Ubuntu/([0-9]+\.[0-9]+)|', $name,
				     $matches ) )
		    // $os_name = "Ubuntu {$matches[ 1 ]}";
		    $os_name = "Ubuntu";
		*/
	    }
            $key = strtolower( str_replace( array( ' ', '.' ), '_', $os_name ) );
            $systems[ $key ][ 'name' ] = $os_name;
            $systems[ $key ][ 'count' ]++;
            break;
        }
    }
    if( ! $found ) {
        $systems[ 'other' ][ 'count' ]++;
    }
}
if( $systems[ 'other' ][ 'count' ] > 0 ) {
    $systems[ 'other' ][ 'name' ] = 'Other';
}

foreach( $systems as $key=>$row ) {
    $names[ $key ] = $row[ 'name' ];
    $counts[ $key ] = $row[ 'count' ];
}

array_multisort( $counts, SORT_DESC, $names, SORT_ASC, $systems );
    
$json = json_encode( $systems );
print $json;
?>
