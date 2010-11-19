<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    if( isset( $_POST[ 'type' ] ) and isset( $_POST[ 'weight' ] ) and
	$_POST[ 'type' ] > 0 ) {
	$post_weight = $db->real_escape_string( $_POST[ 'weight' ] );
        $weight = substr( $post_weight, 0, strlen( $post_weight ) - 1 );
	$id = $db->real_escape_string( $_POST[ 'id' ] );
	$type = $db->real_escape_string( $_POST[ 'type' ] );
	$collected = $db->real_escape_string( $_POST[ 'collected' ] );

        $insert_query = 'insert into grade_weights( id, course, grade_type, '
	    . 'grade_weight, collected ) '
            . "values( null, $id, $type, $weight, $collected )";
        $insert_result = $db->query( $insert_query );
    } else if( isset( $_POST[ 'remove_id' ] ) ) {
	$id = $db->real_escape_string( $_POST[ 'remove_id' ] );
        $remove_query = 'delete from grade_weights '
            . "where id = $id";
        $remove_result = $db->query( $remove_query );
    }
    
    $id = $db->real_escape_string( $_POST[ 'id' ] );
    $details_query = 'select id, dept, course, short_name, long_name, prereq, '
	. 'catalog, outline from courses '
        . "where id = $id";
    $details_result = $db->query( $details_query );
    $row = $details_result->fetch_assoc( );
    $details_result->close( );

    print "<form class=\"course_details_form\" id=\"{$row[ 'id' ]}\">\n";

    print "<h3><span class=\"dept\" id=\"{$row[ 'id' ]}\">"
	. "{$row[ 'dept' ]}</span> "
	. "<span class=\"course\" id=\"{$row[ 'id' ]}\">"
	. "{$row[ 'course' ]}</span>: "
	. "<span class=\"long_name\" id=\"{$row[ 'id' ]}\">"
	. "{$row[ 'long_name' ]}</span></h3>\n";

    print '<p style="text-align: center;">';
    print_link( "./syllabus.php?course={$row[ 'id' ]}",
		"View the {$row[ 'dept' ]} {$row[ 'course' ]} Syllabus" );
    print "</p>\n";

    print "<div id=\"short_name\">\n";
    print "<p><b>Short Name</b>: <span class=\"short_name\" "
	. "id=\"{$row[ 'id' ]}\">"
	. "{$row[ 'short_name' ]}</span></p></div>\n";

    print "<div id=\"prerequisite\">\n";
    print "<p><b>Prerequisite</b>: "
	. "<span class=\"prereq\" id=\"{$row[ 'id' ]}\">"
	. "{$row[ 'prereq' ]}</span></p></div>\n";

    print "<div id=\"catalog_description\">\n";
    print "<p><b>Catalog Description</b>:<br />"
	. "<span class=\"catalog\" id=\"{$row[ 'id' ]}\">"
	. "{$row[ 'catalog' ]}</p></div>\n";

    print "<div id=\"course_outline\">\n";
    print "<p><b>Course Outline</b>:<br />"
	. "<span class=\"outline\" id=\"{$row[ 'id' ]}\">"
	. "{$row[ 'outline' ]}</p></div>\n";
    
    $weights_query = 'select w.id, t.grade_type as t, '
	. 'w.grade_weight as w, collected as c '
	. 'from grade_types as t, grade_weights as w '
        . "where w.course = $id "
        . 'and w.grade_type = t.id '
        . 'order by w.grade_weight desc, t.grade_type';
    $weights_result = $db->query( $weights_query );

    print "<div id=\"grade_weights\" class=\"hover\">\n";
    print "<p><b>Grade Weights</b>:<br />";
    if( $weights_result->num_rows == 0 ) {
        print "None.\n";
    } else {
        $sum = 0;
        print "<ul id=\"grade_weights\">\n";
        while( $row = $weights_result->fetch_assoc( ) ) {
            print "<li><a href=\"javascript:void(0)\" "
		. "class=\"remove_grade_weight\" id=\"{$row[ 'id' ]}\" "
                . "title=\"Remove {$row[ 't' ]} Weight\">"
                . "<img src=\"$docroot/images/silk_icons/cancel.png\" "
		. "height=\"16\" width=\"16\" /></a>\n";
            print "<span class=\"grade_type\" "
		. "id=\"{$row[ 'id' ]}\">{$row[ 't' ]}</span>"
                . ": <span class=\"grade_weight\" "
		. "id=\"{$row[ 'id' ]}\">{$row[ 'w' ]}</span>%";
            if( $row[ 'c' ] == 1 ) {
                print ' (Collected)';
            }
            print "</li>\n";
            $sum += $row[ 'w' ];
        }
        print "<li><b>Total: ";
        if( $sum != 100 ) {
            print "<span class=\"wrong_total\">$sum%</span>";
        } else {
            print "$sum%";
        }
        print "</b></li>\n";
        print "</ul>\n";
    }
    print "</p></div>\n";


    print "<div id=\"new_grade_weight\" class=\"hover\">\n";
    print "<table><tr><td><select id=\"grade_type\">\n";
    print "<option value=\"0\">Add New Grade Weight</option>\n";
    $weights_query = 'select id, grade_type from grade_types '
	. 'order by grade_type';
    $weights_result = $db->query( $weights_query );
    while( $row = $weights_result->fetch_assoc( ) ) {
        print "<option value=\"{$row[ 'id' ]}\">"
	    . "{$row[ 'grade_type' ]}</option>\n";
    }
    print "</select></td>";
    print "<td>Weight:</td><td><div class=\"slider\" "
	. "id=\"new_weight\" style=\"width:100px\"></div></td>\n";
    print "<td><input type=\"text\" "
	. "id=\"new_weight_amount\" size=\"3\"/></td>\n";
    print "</tr><tr>\n";
    print "<td colspan=\"2\" style=\"text-align: center\">"
	. "<input type=\"checkbox\" id=\"collected\"> Do you collect these "
	. "in class?</td>\n";
    print "</tr><tr>\n";
    print "<td colspan=\"2\" style=\"text-align: center\">"
	. "<a href=\"javascript:void(0)\" "
	. "id=\"new_weight_submit\">Add This Grade Weight</a></td>\n";
    print "</tr></table></div> <!-- div#new_grade_weight -->\n";

    print "<div id=\"textbook_hover\" class=\"hover\">\n";
    print "<p><b>Textbooks</p>\n";
    print "<div id=\"textbooks\"></div></div>\n";

    print "<p><a href=\"javascript:void(0)\" "
	. "class=\"hide_course_details\">Hide</a></p>\n";

    print "</form>\n";
    
    ?>

<script type="text/javascript">
$(document).ready(function(){
    $.post( 'course_textbooks.php',
        { course: "<?php echo $id; ?>" },
        function(data){
            $("div#textbooks").html(data);
        }
    );

    /*
    $('div.hover').hover(
        function(){
	    $(this).css('padding', '1em')
		.css('border', '1px solid')
		.css('backgroundColor', '#5d562c');
	    $(this).children('ul').css('padding-right','1em');
	},
	function(){
	    $(this).css('padding', '0')
		.css('border', '0')
		.css('backgroundColor', '#1e273e');
	    $(this).children('ul').css('padding-right','0');
	}
    );
    */
    
    $("div#new_weight").slider({
        value: 20,
        min: 0,
        max: 100,
        step: 5,
        slide: function(event, ui){
            $("input#new_weight_amount").val(ui.value + '%');
        }
    });
    
    $("input#new_weight_amount").val($("div#new_weight").slider("value") + '%');

    $("div#new_grade_weight a#new_weight_submit").click(function(){
        var grade_type = $("select#grade_type").val();
        var grade_weight = $("input#new_weight_amount").val();
        var id = "<?php echo $id; ?>";
        var collected = 0;
        if( $("input#collected").attr("checked") == true ) {
            collected = 1;
        }
        
        $.post( 'course_details.php',
        { id: id, type: grade_type, weight: grade_weight, collected: collected },
        function(data){
            $("div.course_details").html(data);
        })
    })
    
    $("a.remove_grade_weight").click(function(){
        var id = "<?php echo $id; ?>";
        var remove_id = $(this).attr("id");
        
        $.post( 'course_details.php',
        { id: id, remove_id: remove_id },
        function(data){
            $("div.course_details").html(data);
        })
    })

  	
})
</script>

<?php
}
?>

