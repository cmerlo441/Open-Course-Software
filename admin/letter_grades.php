<?php

$title_stub = 'Letter Grades';
require_once( '../_header.inc' );

if( isset( $_SESSION[ 'admin' ] ) ) {
    $grades_query = 'select * from letter_grades '
        . 'order by grade desc';
    $grades_result = $db->query( $grades_query );
    print "<div id=\"letter_grades_table\">\n";
    print "<table class=\"tablesorter\" id=\"letter_grades\">\n";
    print "<thead>\n";
    print "  <tr>\n";
    print "    <th>Letter Grade</th>\n";
    print "    <th>Numerical Grade</th>\n";
    print "    <th>Actions</th>\n";
    print "  </tr>\n";
    print "</thead>\n\n";
    
    print "<tbody>\n";
    while( $grade_row = $grades_result->fetch_assoc( ) ) {
        print "  <tr class=\"letter_grade\" id=\"{$grade_row[ 'id' ]}\">\n";
        print "    <td class=\"letter\">{$grade_row[ 'letter' ]}</td>\n";
        print "    <td class=\"grade\">{$grade_row[ 'grade' ]}</td>\n";
        print "    <td class=\"actions\">Edit | ";
        print "<a href=\"javascript:void(0)\" class=\"delete_letter_grade\" id=\"{$grade_row[ 'id' ]}\" >"
            . "<img src=\"$docroot/images/cancel_16.png\" height=\"16\" width=\"16\" title=\"Delete This Letter Grade\" /></a></td>\n";
        print "  </tr>\n";
    }
    print "</tbody>\n</table>\n";
    print "</div>  <!-- div#letter_grades_table -->\n";
    
    print "<p>New letter grade:<br />\n";
    print "<input type=\"text\" id=\"new_letter_grade\" width=\"5\" /></p>\n";
    print "<p>Numerical equivalent:<br />\n";
    print "<input type=\"text\" id=\"new_numerical_grade\" "
	. "width=\"5\" /></p>\n";
    print "<p><input type=\"submit\" value=\"Add new letter grade\" id=\"new_grade\" /></p>\n";

?>
<script type="text/javascript">
$(document).ready(function(){
    $("table#letter_grades").tablesorter(
        { sortList: [ [1,1] ],
          widgets: [ 'ocsw' ]
        }
    );

    $('input#new_grade').click(function(){
        var letter = $('input#new_letter_grade').val();
	var number = $('input#new_numerical_grade').val();
	$.post('edit_letter_grades.php',
	    { letter: letter, number: number },
	    function(data){
		$('div#letter_grades_table').html(data);
		$('table#letter_grades').tablesorter(
		    { sortList: [ [1,1] ],
		      widgets: [ 'ocsw' ]
		    }
		)
	    }
        );
    })

    $('a.delete_letter_grade').click(function(){
        var id = $(this).attr('id');
	$.post('edit_letter_grades.php',
            { delete: id },
            function(data){
		$('div#letter_grades_table').html(data);
		$('table#letter_grades').tablesorter(
		    { sortList: [ [1,1] ],
		      widgets: [ 'ocsw' ]
		    }
		)
	    }
        );
    })
})

</script>
<?php

} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
