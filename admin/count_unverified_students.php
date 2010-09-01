<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $student_query = 'select * from students '
        . 'where verified = 0 and password != "Fake Password" '
        . 'order by last, first, middle';
    $student_result = $db->query( $student_query );
    $count = $student_result->num_rows;
    if( $count > 0 ) {
        print "<a href=\"$admin/unverified.php\">There "
            . ( $count == 1 ? 'is' : 'are' ) . " $count unverified student"
            . ( $count == 1 ? '' : 's' ) . ".</a>\n";
    } else {
        print '';
    }
}
   
?>
