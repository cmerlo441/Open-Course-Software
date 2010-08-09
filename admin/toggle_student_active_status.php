<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $id = $db->real_escape_string( $_POST[ 'id' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );

    $active_query = 'select s.first, s.last, x.active '
        . 'from students as s, student_x_section as x '
        . "where x.student = s.id "
        . "and student = $id and section = $section";
    $active_result = $db->query( $active_query );
    $active_row = $active_result->fetch_assoc( );
    
    $update_query = 'update student_x_section set active = '
        . ( $active_row[ 'active' ] == 0 ? '1': '0' )
        . " where student = $id and section = $section";
    $update_result = $db->query( $update_query );

    print "<a href=\"javascript:void(0)\" class=\"active\" "
	. "id=\"$id\">"
        . "<img src=\"$docroot/images/silk_icons/";
        
    /* This test is opposite of the one you'll find in roster.php.  $active_row[ 'active' ]
     * contains the old, pre-updated value; hence if active = 0, the student *was*
     * non-active, and is *now* active, ever since that db update.  Knowhatimean?
     */
        
    if( $active_row[ 'active' ] == 0 ) {
        print "accept.png\" title=\"Deactivate {$active_row[ 'first' ]} {$row[ 'last' ]}\" ";
    } else {
        print "cross.png\" title=\"Reactivate {$active_row[ 'first' ]} {$row[ 'last' ]}\" ";
    }
    print "height=\"16\" width=\"16\" /></a>\n";
}
?>
