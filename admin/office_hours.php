<?php

$title_stub = 'Office Hours';
require_once( '../_header.inc' );

if( $_SESSION [ 'admin' ] == 1 ) {
    
    $days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday',
        'Friday', 'Saturday' );
    
    print "<div id=\"current_office_hours\"></div>\n";
    
    print "<div id=\"new_office_hours\">\n";
    print "<h2>Add Office Hours</h2>\n";
    print "<select id=\"day\">\n";
    foreach( $days as $key=>$value ) {
        print "  <option value=\"$key\">$value</option>\n";
    }
    print "</select>\n";
    print "From <input type=\"text\" id=\"start\" title=\"From\" size=\"5\" />\n";
    print " to <input type=\"text\" id=\"end\" title=\"To\" size=\"5\" />\n";
    print " in <input type=\"text\" id=\"building\" title=\"Building Name\" size=\"8\"/>\n";
    print " <input type=\"text\" id=\"room\" title=\"Room Number\" size=\"5\"/>\n";
    print "<input type=\"submit\" id=\"add\" value=\"Add\" />\n";
    print "<div id=\"instructions\" style=\"padding: 0.5em 2em; font-size: 0.75em\">"
        . "Enter times like \"2 pm\" or \"3:15 pm\"</div>\n";
    print "</div>\n";
    
?>

<script type="text/javascript">
$(document).ready(function(){
	$.post( 'list_office_hours.php',
		function(data){
		   $('div#current_office_hours').html(data);
	    }
	);

	$('input#add').click(function(){
		var day = $('select#day').val();
		var start = $('input#start').val();
		var end = $('input#end').val();
		var building = $('input#building').val();
		var room = $('input#room').val();

		$.post( 'list_office_hours.php',
			{ day: day, start: start, end: end, building: building, room: room },
			function(data) {
				$('div#current_office_hours').html(data);
				$('select#day').val(0);
				$('input:text').val('');
			}
		);
	})
})
</script>

<?php
    
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>