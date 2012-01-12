<?php

$no_header = 1;
require_once ('../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
	$course = $db->real_escape_string( $_REQUEST[ 'course' ] );
	
	if( isset( $_POST[ 'delete' ] ) ) {
		$id = $db->real_escape_string( $_POST[ 'delete' ] );
		$db->query( "delete from grade_weights where id = $id" );
	}
	
	else if( isset( $_POST[ 'type' ] ) and isset( $_POST[ 'weight' ] ) ) {
		$type = $db->real_escape_string( $_POST[ 'type' ] );
		$weight = $db->real_escape_string( $_POST[ 'weight' ] );
		$db->query( 'insert into grade_weights( id, course, grade_type, grade_weight, collected ) '
			. "values( null, $course, $type, $weight, 0 )" );
	}
	
	$weights_query = 'select w.id, w.grade_type as type, t.grade_type as t, w.grade_weight as w, w.collected '
		. 'from grade_weights as w, grade_types as t '
		. 'where w.grade_type = t.id '
		. "and w.course = $course "
		. 'order by t.grade_type';
	$weights_result = $db->query( $weights_query );
	if( $weights_result->num_rows == 0 ) {
		print "<p>You have not defined any grade weights for this class.</p>\n";
	} else {
		print "<ul>\n";
		$ids = array( );
		while( $weight = $weights_result->fetch_object( ) ) {
			$ids[ ] = $weight->type;
			
			$drop = 0;
			$drop_query = 'select id from drop_lowest '
				. "where course = $course "
				. "and grade_type = $weight->type";
			//print "<pre>$drop_query;</pre>\n";
			$drop_result = $db->query( $drop_query );
			if( $drop_result->num_rows == 1 )
				$drop = 1;
			
			print "<li class=\"weight\" id=\"$weight->id\">";
			print "<span style=\"font-weight: bold\">$weight->t</span>: ";
			print "$weight->w%";
			print " <a class=\"delete\" id=\"$weight->id\" href=\"javascript:void(0)\">";
			print "<img src=\"$docroot/images/silk_icons/cross.png\" height=\"16\" width=\"16\" "
				. "title=\"Remove $weight->t\" />";
			print "</a>";
			
			print "<br /><input type=\"checkbox\" ";
			if( $weight->collected == 1 )
				print 'checked ';
			print "id=\"collected\" style=\"margin-left: 3em\"> Submitted over the web?\n";
			print "<br /><input type=\"checkbox\" ";
			if( $drop == 1 )
				print 'checked ';
			print "id=\"drop_lowest\" style=\"margin-left: 3em\"> Drop the lowest grade?\n";
			print "</li>\n";
		}
		print "</ul>\n";
		
		$sum_query = "select sum(grade_weight) as sum from grade_weights where course = $course";
		$sum_result = $db->query( $sum_query );
		$sum = $sum_result->fetch_object( );
		if( $sum->sum != 100 ) {
			print "<div class=\"warning\" style=\"margin: 0.5em 0.5em 0.5em 3em; padding: 1em; background-color: red; font-weight: bold;\">"
				. "These grade weights add up to $sum->sum%.</div>\n";
		}
	}
		
	// What a hack putting this in a <table>.  I'm sorry, Internet.
	
	print "<div id=\"new_weight_div\" style=\"margin-left: 3em;\">\n";
	print "<table><tr>";
	print "<td><select id=\"new_weight\">\n";
	print "<option value=\"0\">Add new grade weight</option>\n";
	$weight_types_query = 'select id, grade_type as t from grade_types order by grade_type';
	$weight_types_result = $db->query( $weight_types_query );
	while( $weight = $weight_types_result->fetch_object( ) ) {
		if( ! in_array( $weight->id, $ids ) ) {
			print "<option value=\"$weight->id\">$weight->t</option>\n";
		}
	}
	print "</select></td>\n";
	print "<td>Weight:</td><td><div class=\"slider\" id=\"new_weight_pct\" style=\"width:100px\"></div></td>\n";
	print "<td><input type=\"text\" id=\"new_weight_amount\" size=\"3\"/></td>\n";
	print "<td><input type=\"submit\" value=\"Add\" id=\"new_weight_button\" /></tr></table>\n";
	
	print "</div>  <!-- div#new_weight_div -->\n";

}

?>

<script type="text/javascript">
$(document).ready(function(){
	
	var course = "<?php echo $course; ?>";

    $("div#new_weight_pct").slider({
        value: 20,
        min: 0,
        max: 100,
        step: 5,
        slide: function(event, ui){
            $("input#new_weight_amount").val(ui.value + '%');
        }
    });
    
    $("input#new_weight_amount").val($("div#new_weight_pct").slider("value") + '%');
    
    $('a.delete').click(function(){
    	var id = $(this).attr('id');
    	$.post( 'list_grade_weights.php',
    		{ course: course, delete: id },
    		function(data){
    			$('div#weightsdiv').html(data);
    		}
    	)
    })

	$('input#new_weight_button').click(function(){
		var type = $('select#new_weight').val();
		var weight = $('input#new_weight_amount').val().replace('%','');
		$.post( 'list_grade_weights.php',
			{ course: course, type: type, weight: weight },
			function(data){
				$('div#weightsdiv').html(data);
			}
		)
		var sum = "<?php echo $sum->sum; ?>";
		if( sum == 100 )
			$('div.warning').fadeOut( 100 );
	})

})
</script>
