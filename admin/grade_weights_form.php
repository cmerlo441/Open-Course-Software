<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $id = $db->real_escape_string( $_POST[ 'course' ] );
    $weights_query = 'select w.id, t.plural, w.grade_weight, w.collected '
	. 'from grade_weights as w, grade_types as t '
	. 'where w.grade_type = t.id '
	. "and w.course = $id";
    $weights_result = $db->query( $weights_query );
    print "<table id=\"grade_weights_table\" style=\"margin: auto\">";
    print "<thead>\n";
    print "<tr>\n";
    print "  <th>Grade Type</th>\n";
    print "  <th colspan=\"2\">Weight</th>\n";
    print "  <th>Collected?</th>\n";
    print "</tr>\n";
    print "</thead>\n\n";

    print "<tbody>\n";
    while( $row = $weights_result->fetch_assoc( ) ) {
?>

  <tr>
    <td>
      <div style="padding: 0 1em;"><?php echo $row[ 'plural' ]; ?>
      </div>
    </td>

    <td>
      <div class="slider" id="<?php echo $row[ 'id' ]; ?>"
             style="width: 50px; padding: 0 1em;">
      </div>
    </td>

    <td>
      <div style="padding: 0 1em;">
        <input type="text" id="<?php echo $row[ 'id' ]; ?>" size="3"
        value="<?php echo $row[ 'grade_weight' ]; ?>" /> %
      </div>
    </td>

    <td>
      <div class="checkbox" id="<?php echo $row[ 'id' ]; ?>"
	style="margin: auto;">
<?php
	print "        <input type=\"checkbox\" "
	    . "id={$row[ 'id' ]} ";
	if( $row[ 'collected' ] == 1 ) {
	    print 'checked ';
	}
	print "/>\n";	
?>
        <!-- <label for="<?php echo $row[ 'id' ]; ?>">No</label> -->
      </div>
    </td>
  </tr>

<?php
    }
    print "<tr>\n";
    print "  <td colspan=\"4\" style=\"text-align: center; "
	. "border: 1px solid white;\">Total:\n";
    print "    <span id=\"total\" style=\"font-weight: bold;\">x"
	. "</span> %\n";
    print "  </td>\n";
    print "</tr>\n\n";

    print "</tbody>\n";
    print "</table>\n\n";

?>

<script type="text/javascript">
function update_total(){
    var total = 0;

    $('table#grade_weights_table input:text').each(function(){
        total += $(this).val() * 1;
    })
    $('span#total').html(total);
    if( total != 100 ) {
	$('span#total').css('color', 'red');
    } else {
	$('span#total').css('color', 'white');
    }
}

$(document).ready(function(){
    update_total();

    $('table#grade_weights_table div.slider').each(function(){
        var id = $(this).attr('id');
        var value = $('table#grade_weights_table input[id=' + id + ']').val();

        $(this).slider({
            min: 0,
	    max: 100,
	    step: 5,
	    value: value,
	    slide: function( event, ui ){
                var value = $(this).slider( 'option', 'value' );
		$('input[id=' + id + ']' ).val( ui.value );
		update_total();
            }
	})
    })

    $('table#grade_weights_table input:text').change(function(){
        var id = $(this).attr('id');
        var value = $(this).val();
	$('table#grade_weights_table div.slider[id=' + id + ']')
	    .slider( 'option', 'value', value );
	update_total();
    })

    $('table#grade_weights_table input:checkbox').each(function(){
        var id = $(this).attr('id');

	//$(this).button({ label: "label" });
    })

})
</script>

<?php
}
?>