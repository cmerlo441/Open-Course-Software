<?php

$no_header = 1;
require_once( '../_header.inc' );
require_once( '../graph_data/jsonwrapper/jsonwrapper.php' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $students = array();

    $term = $db->real_escape_string( $_REQUEST[ 'term' ] );

    $query = 'select s.first, s.last, s.banner '
        . 'from students as s, student_x_section as x '
        . 'where x.student = s.id '
        . 'and ( x.status = ( select id from student_statuses where status = "Grade" ) '
        . 'or x.status = ( select id from student_statuses where status = "Audit" ) '
        . 'or x.status = ( select id from student_statuses where status = "INC" ) ) '
        . "and ( first like '%$term%' or last like '%$term%' or banner like '%$term%' ) "
        . 'order by s.last, s.first';
    $result = $db->query( $query );
    $first = true;
    while( $row = $result->fetch_object( ) ) {
        if( $first == false )
            print "\n";
        $first = false;
        $students[ ] = ucwords( "$row->first $row->last ($row->banner)" );
    }
    
    $json = json_encode($students);
    print $json;
}

?>
