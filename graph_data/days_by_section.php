<?php

$no_header = 1;
require_once( '../_header.inc' );
require_once( './jsonwrapper/jsonwrapper.php' );

$sections = array( );

$section_query = 'select s.id, c.dept, c.course, s.section '
    . 'from courses as c, sections as s '
    . 'where s.course = c.id '
    . 'order by c.dept, c.course, s.section';
$section_result = $db->query( $section_query );

$section_count = 1;
while( $section = $section_result->fetch_assoc( ) ) {
    $name = $section[ 'dept' ] . ' ' . $section[ 'course' ]
        . ' ' . $section[ 'section' ];
    $sections[ $section_count ][ 'name' ] = $name;
    for( $i = 0; $i <= 6; $i++ ) {
        $sections[ $section_count ][ 'data' ][ $i ] = 0;
    }
    $login_query = 'select datetime '
        . 'from logins as l, student_x_section as x '
        . 'where x.student = l.student '
        . "and x.section = {$section[ 'id' ]} "
        . 'order by datetime';
    $login_result = $db->query( $login_query );
    while( $login = $login_result->fetch_assoc( ) ) {
        $sections[ $section_count ][ 'data' ][ date( 'w', strtotime( $login[ 'datetime' ] ) ) ]++;
    }
    $section_count++;
}

print json_encode( $sections );

?>