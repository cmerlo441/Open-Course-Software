<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
  $assignment = $db->real_escape_string( $_POST[ 'assignment' ] );
  $update_query = 'update assignments '
    . 'set grade_summary_tweeted = 1 '
    . "where id = $assignment";
  $update_result = $db->query( $update_query );
}

?>