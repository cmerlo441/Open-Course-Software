<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    if( isset( $_POST[ 'dept' ] ) and isset( $_POST[ 'course' ] ) ) {
        $dept = strtoupper( $db->real_escape_string( $_POST[ 'dept' ] ) );
        $course = $db->real_escape_string( $_POST[ 'course' ] );
        $db->query( 'insert into courses( id, dept, course ) values '
            . "( null, \"$dept\", \"$course\" )" );
    }
    
    if( isset( $_POST[ 'delete' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'delete' ] );
        $db->query( "delete from sections where course = $id" );
        $db->query( "delete from courses where id = $id" );
    }
    
	$courses_query = 'select id, dept, course, short_name as s, long_name as l, credits '
		. 'from courses '
		. 'order by dept, course';
	$courses_result = $db->query( $courses_query );
	if( $courses_result->num_rows == 0 ) {
		print 'You have not added any courses yet.';
	} else { 
		while( $course = $courses_result->fetch_object( ) ) {
			print "<li id=\"course$course->id\">";
			print "<a class=\"course_details\" id=\"$course->id\" href=\"javascript:void(0)\" title=\"Edit $course->dept $course->course\">";
			print "<span style=\"font-weight: bold;\">$course->dept $course->course</span>: $course->l ($course->credits credits)";
			print "</a></li>\n";
		}
	}
	
	print "<div id=\"course_details\" style=\"display: none; padding-top: 1em\"></div>\n";
	
}

?>

<script type="text/javascript">
$(document).ready(function(){
	
	$('a.course_details').click(function(){
		var id = $(this).attr('id');
		$.post('./course_details.php',
			{ course_id: id },
			function(data) {
				$('div#course_details').hide().html( data ).slideDown();
			}
		)
	})
	
})
</script>