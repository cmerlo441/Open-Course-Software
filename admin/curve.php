<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
  $grade_event = $db->real_escape_string( $_REQUEST[ 'grade_event' ] );
  $curve_query = 'select * from curves '
    . "where grade_event = $grade_event";
  $curve_result = $db->query( $curve_query );

  $points = 0;
  $percent = 0;

  // Figure out what curve already exists, if any

  if( $curve_result->num_rows == 1 ) {
    $curve_row = $curve_result->fetch_assoc( );
    if( $curve_row[ 'points' ] != '' and $curve_row[ 'points' ] > 0 ) {
      $points = $curve_row[ 'points' ];
    } else if( $curve_row[ 'percent' ] != '' and $curve_row[ 'percent' ] > 0 ) {
      $percent = $curve_row[ 'percent' ];
    }
  }

  // Display curve sliders

  print "<table>\n";

  print "<tr>\n";
  print "<td style=\"padding: 0.5em; width: 150px;\">Curve: <span id=\"points\">$points</span> points</td>\n";
  print "<td style=\"padding: 0.5em; width: 300px;\"><div class=\"slider\" id=\"points\" style=\"width: 200px;\"></div></td>\n";
  print "</tr>\n";

  print "<tr>\n";
  print "<td style=\"padding: 0.5em; width: 150px;\">Curve: <span id=\"percent\">$percent</span> percent</td>\n";
  print "<td style=\"padding: 0.5em; width: 300px;\"><div class=\"slider\" id=\"percent\" style=\"width: 200px;\"></div></td>\n";
  print "</tr>\n";

  print "</table>\n";

?>

<script type="text/javascript">
$(document).ready(function(){

  $('div#points').slider({
    value: <?php echo $points; ?>,
    min: 0,
    max: 100,
    step: 1,
    slide: function( event, ui ) {
      $('span#points').html(ui.value);
      $.post( 'set_curve.php',
        {
          grade_event: <?php echo $grade_event; ?>,
          points: ui.value
        }
      );
      if( ui.value >= 0 ) {
	$('div#percent').slider( "option", "value", 0 );
	$('span#percent').html(0);
	$('div#grades_table > table#grades > tbody > tr > td.curved' ).each(function(){
          var id = $(this).attr('id');
	  var curved = $(this).html();
	  if( curved != '--' ) {
	    var orig = $('div#grades_table > table#grades > tbody > tr > td#' + id + '.grade span').html();
	    var new_curved = ( orig * 1.0 + ui.value );
	    $(this).html( new_curved );
	  }
        })
      }
    }
  });

  $('div#percent').slider({
    value: <?php echo $percent; ?>,
    min: 0,
    max: 100,
    step: 1,
    slide: function( event, ui ) {
      $('span#percent').html(ui.value);
      $.post( 'set_curve.php',
	{
          grade_event: <?php echo $grade_event; ?>,
          percent: ui.value
        }
      );
      if( ui.value >= 0 ) {
	$('div#points').slider( "option", "value", 0 );
	$('span#points').html(0);
	$('div#grades_table > table#grades > tbody > tr > td.curved' ).each(function(){
          var id = $(this).attr('id');
	  var curved = $(this).html();
	  if( curved != '--' ) {
	    var orig = $('div#grades_table > table#grades > tbody > tr > td#' + id + '.grade span').html();
	    var new_curved = ( orig * ( 1.0 + ui.value * 0.01 ) ).toFixed( 0 );
	    $(this).html( new_curved );
	  }
        })
      }
    }
  });
})
</script>

<?php
}