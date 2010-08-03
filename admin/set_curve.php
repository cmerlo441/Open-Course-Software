<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

  $grade_event = $db->real_escape_string( $_REQUEST[ 'grade_event' ] );
  $points = $db->real_escape_string( $_REQUEST[ 'points' ] );
  $percent = $db->real_escape_string( $_REQUEST[ 'percent' ] );

  $curve_query = 'select * from curves '
    . "where grade_event = $grade_event";
  $curve_result = $db->query( $curve_query );

  if( $curve_result->num_rows == 0 ) {
    $insert_query = 'insert into curves( id, grade_event, points, percent ) '
      . "values( null, $grade_event, ";
    if( $points != '' and $points >= 0 and $points <= 100 ) {
      $insert_query .= "$points, 0";
    } else {
      $insert_query .= "0, $percent";
    }
    $insert_query .= " )";
    $insert_result = $db->query( $insert_query );
  } else {
    $update_query = "update curves set ";
    if( $points != '' and $points >= 0 and $points <= 100 ) {
      $update_query .= "points = $points, percent = 0 ";
    } else {
      $update_query .= "percent = $percent, points = 0 ";
    }
    $update_query .= "where grade_event = $grade_event";
    $update_result = $db->query( $update_query );
  }
}
?>