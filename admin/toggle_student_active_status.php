<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $active_query = 'select s.first, s.last, x.active '
        . 'from students as s, student_x_section as x '
        . "where x.student = s.id "
        . "and student = {$_POST[ 'id' ]} and section = {$_POST[ 'section' ]}";
    $active_result = $db->query( $active_query );
    $active_row = $active_result->fetch_assoc( );
    
    $update_query = 'update student_x_section set active = '
        . ( $active_row[ 'active' ] == 0 ? '1': '0' )
        . " where student = {$_POST[ 'id' ]} and section = {$_POST[ 'section' ]}";
    $update_result = $db->query( $update_query );

    print "<a href=\"javascript:void(0)\" class=\"active\" id=\"{$_POST[ 'id' ]}\">"
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
