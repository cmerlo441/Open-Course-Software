<?php

$no_header = 1;
require_once( '../_header.inc' );
require_once( '../graph_data/jsonwrapper/jsonwrapper.php' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $last_growled_query = 'select login from last_login_growled '
	. 'order by login desc limit 1';
    $last_growled_result = $db->query( $last_growled_query );
    if( $last_growled_result->num_rows == 0 )
	$last_growled = 1;
    else {
	$lgrow = $last_growled_result->fetch_assoc( );
	$last_growled = $lgrow[ 'login' ];
    }

    $login_query = 'select l.id, l.datetime, s.first, s.middle, s.last '
	. 'from logins as l, students as s '
	. 'where l.student = s.id '
	. "and l.id > $last_growled "
	. 'order by datetime desc '
	. 'limit 10';
    //print "<pre>$login_query;</pre>\n";
    $login_result = $db->query( $login_query );

    $count = 0;
    $data = array( );
    while( $row = $login_result->fetch_assoc( ) ) {
	$data[ $count ][ 'title' ] = ucwords( $row[ 'first' ] ) . ' '
	    . ucwords( $row[ 'last' ] );
	$data[ $count ][ 'text' ] = 'Logged in on '
	    . date( 'l \a\t g:i a', strtotime( $row[ 'datetime' ] ) )
	    . '.';
	if( $count == 0 )
	    $last_growled = $row[ 'id' ];
	++$count;
    }

    print json_encode( $data );

    if( $count > 0 )
	$db->query( 'insert into last_login_growled '
		    . "( id, login, datetime ) values ( null, $last_growled, "
		    . '"' . date( 'Y-m-d H:i:s' ) . '" )' );

}

?>