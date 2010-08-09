<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $ten_minutes_ago = date( 'Y-m-d H:i:s',
			     mktime( date( 'H' ), date( 'i' ) - 10 ) );
    $logged_in_query = 'select l.datetime, l.address, '
	. 's.first, s.middle, s.last '
	. 'from logins as l, students as s '
	. 'where l.student = s.id '
	. "and l.datetime >= \"$ten_minutes_ago\" "
	. 'order by l.datetime desc, s.last, s.first, s.middle ';
    $logged_in_result = $db->query( $logged_in_query );
    $students = array( );
    $listed = array( );
    $i = 0;
    while( $row = $logged_in_result->fetch_assoc( ) ) {
	if( ! isset( $listed[ $row[ 'id' ] ] ) ) {
	    $listed[ $row[ 'id' ] ] = 1;
	    $students[ $i ][ 'name' ] = name( $row );
	    $students[ $i ][ 'when' ] = $row[ 'datetime' ];
	    $students[ $i ][ 'address' ] = $row[ 'address' ];
	    $i++;
	}
    }

    print "<pre>$logged_in_query;</pre>\n";
    print_r( $students );
}
?>
