<?php

$title_stub = 'Courses';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
	print "<div id=\"courses_list\"></div>\n";
    
    print "<div style=\"border-top: 3px solid #5d552c; margin-top: 1em; padding-top: 0.5em\">Create a New Course:<br />\n";
    print "Department: <input size=\"5\" type=\"text\" id=\"dept\" /> ";
    print "Course Number: <input size=\"5\" type=\"text\" id=\"course\" />";
    print "<input type=\"submit\" id=\"new_course\" value=\"Go\"></div>\n";
    
} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>

<script type="text/javascript">

$(document).ready(function(){
	$.post( 'courses_list.php',
		function(data) {
			$('div#courses_list').html(data);
		}
	)
	
	$('input#new_course').click(function(){
	    var dept = $('input#dept').val();
	    var course = $('input#course').val();
	    $.post( 'courses_list.php', 
	       { dept: dept, course: course },
	       function(data){
	           $('div#courses_list').html(data);
	       }
	    )
	    $('input:text').val('');
	})
})

</script>
