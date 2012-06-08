<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $start = 50;
    if( isset( $_REQUEST[ 'start' ] ) ) {
        $start = $db->real_escape_string( $_REQUEST[ 'start' ] );
    }
    
    $login_query = 'select l.id as login_id, l.datetime, l.address, l.browser, s.id as student_id, s.first, s.last, x.section '
        . 'from logins as l, students as s, student_x_section as x '
        . 'where l.student = s.id '
        . 'and x.student = s.id '
        . "and l.id < $start "
        . 'order by l.datetime desc '
        . 'limit 25';
    $login_result = $db->query( $login_query );
    while( $login = $login_result->fetch_object( ) ) {
        print "        <tr class=\"$login->section\" id=\"$login->login_id\">\n";
        print '            <td>' . ucwords( "$login->last, $login->first" ) . "</td>\n";
        print "            <td>" . date( 'm/d H:i', strtotime( $login->datetime ) ) . "</td>\n";
        print "            <td>$login->address</td>\n";
        print "            <td>" . browser( $login->browser ) . "</td>\n";
        print "            <td>" . os( $login->browser ) . "</td>\n";
        print "        </tr>\n";
    }
}

?>
