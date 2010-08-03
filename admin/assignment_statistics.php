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
    $date = date( 'n/j/Y',
		  strtotime( $db->real_escape_string( $_POST[ 'date' ] ) ) );
    $type = $db->real_escape_string( $_POST[ 'type' ] );
    $sequence = $db->real_escape_string( $_POST[ 'sequence' ] );
    $course_name = $db->real_escape_string( $_POST[ 'course_name' ] );

    $section_query = 'select section, grade_type from grade_events '
	. "where id = $event";
    $section_result = $db->query( $section_query );
    $section_row = $section_result->fetch_assoc( );
    $section = $section_row[ 'section' ];
    $grade_type = $section_row[ 'grade_type' ];

    $count_query = 'select count( id ) as count from grade_events '
	. "where section = $section "
	. "and grade_type = $grade_type";
    $count_result = $db->query( $count_query );
    $count_row = $count_result->fetch_assoc( );
    $count = $count_row[ 'count' ];

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

	$twitter_query = 'select twitter_username as u, twitter_password as p '
	  . 'from prof';
	$twitter_result = $db->query( $twitter_query );
	if( $twitter_result->num_rows == 1 ) {
	  $tweeted_query = 'select a.id as a_id, a.grade_summary_tweeted as t '
	    . 'from assignments as a, grade_events as e '
	    . 'where e.assignment = a.id '
	    . "and e.id = $event";
	  $tweeted_result = $db->query( $tweeted_query );
	  $tweeted = $tweeted_result->fetch_assoc( );
	  if( $tweeted[ 't' ] == 0 ) {
	    // Provide tweeting links

	    print "<div id=\"tweet_links\">\n";

	    $message = "Grades posted for $course_name $type";
	    if( $count > 1 ) {
		$message .= " $sequence";
	    }
	    $message .= " ($date).";
	    $mean_message = $message . '  Mean grade ' . number_format( $mean, 2 ) . '.';
	    print "<ul>\n";
	    print "<li><a class=\"tweet\" href=\"javascript:void( 0 )\">"
	      . "Tweet \"$message\"</a></li>\n";
	    print "<li><a class=\"tweet_with_mean\" href=\"javascript:void( 0 )\">"
	      . "Tweet \"$mean_message\"</a></li>\n";
	    print "</ul>\n";

	    print "</div> <!-- div#tweet_links -->\n";
	  } // if not tweeted yet
	} // if the prof has twitter
    } else {
        print 'No grades recorded.';
    }

?>

<script type="text/javascript">

$(document).ready(function(){
  $('a.tweet').click(function(){
      $.post( 'tweet.php',
	{
	  update_string: "<?php echo $message; ?>"
	 },
	 function( data ) {
	   $('div#tweet_links').slideUp();
	 });

      $.post( 'tweeted_grade.php',
	{
	  assignment: "<?php echo $tweeted[ 'a_id' ]; ?>"
	 });
   })

  $('a.tweet_with_mean').click(function(){
      $.post( 'tweet.php',
	{
	  update_string: "<?php echo $mean_message; ?>"
	 },
	 function( data ) {
	   $('div#tweet_links').slideUp();
	 });

      $.post( 'tweeted_grade.php',
	{
	  assignment: "<?php echo $tweeted[ 'a_id' ]; ?>"
	 });
   })
})
</script>

<?php

}

?>