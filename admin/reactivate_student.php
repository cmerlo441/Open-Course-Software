<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

  $x_query = 'select id from student_x_section '
    . "where student=\"{$_POST[ 'student' ]}\" and section=\"{$_POST[ 'section' ]}\"";
  $x_result = @mysql_query( $x_query );
  if( @mysql_num_rows( $x_result ) == 0 ) {
    die( 'No record found' );
  }
  $x = @mysql_result( $x_result, 0 );

  $reactivate_query = "update student_x_section "
    . "set active = 1 where id = \"$x\"";
  $reactiveate_result = @mysql_query( $reactivate_query );
  if( @mysql_affected_rows( ) == 0 ) {
    die( 'No rows affected' );
  }
  print 'Success';
} else {
  print $no_admin;
}
?>
