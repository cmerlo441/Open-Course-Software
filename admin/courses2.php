<?php

$title_stub = 'Courses';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $courses = array( );

    print "<div class=\"dialog\" id=\"course_info_form\"></div>\n";
    print "<div class=\"dialog\" id=\"grade_weights_form\"></div>\n";

    $courses_query = 'select id, dept, course, credits, short_name '
	. 'from courses';
    $courses_result = $db->query( $courses_query );
    while( $row = $courses_result->fetch_assoc( ) ) {
	foreach( explode( ' ',
			  'id dept course credits short_name' ) as $field ) {
	    $courses[ $row[ 'id' ] ][ $field ] = $row[ $field ];
	}
    }

    if( $courses_result->num_rows == 0 ) {
	print "<p>There are no courses in the OCSW database.</p>\n";
    } else {

	// Table of courses
?>

<table class="tablesorter" id="courses">
<thead>
  <tr>
    <th>Dept.</th>
    <th>Course</th>
    <th>Credits</th>
    <th>Name</th>
  </tr>
</thead>

<tbody>

<?php

        foreach( $courses as $course ) {

?>

  <tr id=<?php echo $course[ 'id' ]; ?>>
    <td class="department"><?php echo $course[ 'dept' ]; ?></td>
    <td class="course"><?php echo $course[ 'course' ]; ?></td>
    <td class="credits"><?php echo $course[ 'credits' ]; ?></td>
    <td class="name"><?php echo $course[ 'short_name' ]; ?></td>
  </tr>

<?php
	}
	print "</tbody>\n";
	print "</table>\n\n";
    } // end of displaying table

?>

<script type="text/javascript">
$(document).ready(function(){
    $('table#courses').tablesorter({
	sortList: [ [ 0, 0 ], [ 1, 0 ] ],
	widgets: [ 'ocsw', 'clickable_rows' ]
    });

    $('table#courses tbody tr').click(function(){
        var id = $(this).attr('id');
	var name = $(this).find( 'td.department' ).html() + ' ' +
	    $(this).find( 'td.course' ).html() + ': ' +
	    $(this).find( 'td.name' ).html();

	$.post('./course_info_form.php',
	    { course: id },
	    function( data ) {
	        $('div#course_info_form').html(data).dialog({
                    width: 900,
		    title: name,
		    modal: true,
		    buttons: {
			'OK': function(){
			    $('div#course_info_form').dialog( 'destroy' );
			},
			'Cancel': function(){
			    $('div#course_info_form').dialog( 'destroy' );
			},
			'Edit Grade Weights': function(){
			    $.post('grade_weights_form.php',
                                { course: id },
				function(data){
				    $('div#grade_weights_form').html(data).dialog({
                                        width: 500,
				        title: 'Edit Grade Weights for ' + name,
					modal: true,
					buttons: {
					    'Cancel': function(){
					        $('div#grade_weights_form').dialog('destroy');
					     }
					 }
				     })
				}
			    )}
			}
		    })
	    }
        )
    })
})
</script>

<?php    

} // if an admin is logged in
else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
