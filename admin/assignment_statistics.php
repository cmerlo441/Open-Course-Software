<?php

$no_header = 1;
require_once( '../_header.inc' );

function mode( $grades ) {
  $debug = 0;

  if( sizeof( $grades ) == 0 )
    return 'None';

  $freq = array( );
  foreach( $grades as $grade )
    $freq[ $grade ]++;
  arsort( $freq );

  $dist = array( );
  $i = 0;
  foreach( $freq as $key=>$value ) {
    $dist[ $i ][ 'grade' ] = $key;
    $dist[ $i ][ 'frequency' ] = $value;
    $i++;
  }

  if( $debug == 1 ) {
    print "<pre>Distribution: ";
    print_r( $dist );
    print "</pre>\n";
  }

  // Possibility 1: Single mode
  if( $dist[ 0 ][ 'frequency' ] > $dist[ 1 ][ 'frequency' ] )
    return $dist[ 0 ][ 'grade' ];

  // Possibility 2: Two modes
  else if( $dist[ 0 ][ 'frequency' ] == $dist[ 1 ][ 'frequency' ] and
           $dist[ 1 ][ 'frequency' ] > $dist[ 2 ][ 'frequency' ] )
    return min( $dist[ 0 ][ 'grade' ], $dist[ 1 ][ 'grade' ] )
      . ' and '
      . max( $dist[ 0 ][ 'grade' ], $dist[ 1 ][ 'grade' ] );

  else
    return "None";

}

if( $_SESSION[ 'admin' ] == 1 ) {
    $grades = array( );

    $event = $db->real_escape_string( $_POST[ 'event' ] );

    $grades_query = 'select grade from grades '
        . "where grade_event = $event";
    $grades_result = $db->query( $grades_query );
    if( $grades_result->num_rows > 0 ) {
        
        print "<h3>Grade Statistics</h3>\n";
        
        while( $grades_row = $grades_result->fetch_assoc( ) ) {
            $grades[ ] = $grades_row[ 'grade' ];
        }
        
        $mean = array_sum( $grades ) * 1.0 / count( $grades );
        sort( $grades, SORT_NUMERIC );
        $min = $grades[ 0 ];
        $max = $grades[ count( $grades ) - 1 ];
        
        if( count( $grades ) % 2 == 1 ) {
            $median = $grades[ count( $grades ) / 2 ];
        } else {
            $median = ( $grades[ floor( count( $grades ) / 2.0 ) ] +
                        $grades[ ceil( count( $grades ) / 2.0 ) ] ) / 2;
        }
        
        print "Range: $min - $max.<br />\n";
        print 'Mean grade: ' . number_format( $mean, 2 ) . ".<br />\n";
        print "Median grade: $median.<br />\n";
        print 'Mode: ' . mode( $grades ) . ".<br />\n";
    } else {
        print 'No grades recorded.';
    }
}

?>