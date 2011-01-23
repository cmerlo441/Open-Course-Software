<?php

$no_header = 1;
require_once( '../_header.inc' );
require_once( './jsonwrapper/jsonwrapper.php' );

$sections = array( );
    
$login_query = 'select c.dept, c.course, s.section, s.id '
    . 'from courses as c, sections as s, logins as l, student_x_section as x '
    . 'where l.student = x.student '
    . 'and x.section = s.id '
    . 'and s.course = c.id';
$login_result = $db->query( $login_query );
while( $row = $login_result->fetch_object( ) ) {
    $section_name = $row->dept . ' ' . $row->course . ' ' . $row->section;
    $sections[ $section_name ][ 'name' ] = $section_name;
    $sections[ $section_name ][ 'count' ]++;
}

foreach( $sections as $key=>$row ) {
    $names[ $key ] = $row[ 'name' ];
    $counts[ $key ] = $row[ 'count' ];
}

array_multisort( $counts, SORT_DESC, $names, SORT_ASC, $sections );
    
$json = json_encode( $sections );
print $json;

?>
