<?php

$title_stub = 'All Courses';
require_once( '../_header.inc' );

?>

<script type="text/javascript">
  $(document).ready(function(){
  	
  	$("div#admin_details").hide();
  	
	$("#courses_table").tablesorter( { sortList: [ [0,0], [1,0] ], widgets: [ 'phprof', 'clickable_rows' ] } );
  	
  	$("div#new_course").hide();
  	
  	$("div.course_details").hide();
  	$("tr").click(function(){
  		var id = $(this).attr("id");

        $.ajax({
            type: "POST",
            url: "course_details.php",
            data: "id=" + id,
            dataType: "text",
            success: function( msg ) {
                $(".course_details").html(msg);
          		$(".course_details").slideDown("slow");

          		$("span.dept").editInPlace({
          			url: "update_course.php",
          			params: "ajax=yes&column=dept",
          			saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
          		});
          		$("span.course").editInPlace({
          			url: "update_course.php",
          			params: "ajax=yes&column=course",
          			saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
          		});
          		$("span.short_name").editInPlace({
          			url: "update_course.php",
          			params: "ajax=yes&column=short_name",
          			saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
          		});
          		$("span.long_name").editInPlace({
          			url: "update_course.php",
          			params: "ajax=yes&column=long_name",
          			saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
          		});
          		$("span.prereq").editInPlace({
          			url: "update_course.php",
          			params: "ajax=yes&column=prereq",
          			saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
          		});
          		$("span.catalog").editInPlace({
          			url: "update_course.php",
          			params: "ajax=yes&column=catalog",
          			field_type: "textarea",
          			textarea_rows: "5",
          			textarea_cols: "80",
          			saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
          		});
			    $("span.outline").editInPlace({
			        url: "update_course.php",
                    params: "ajax=yes&column=outline",
                    field_type: "textarea",
                    textarea_rows: "5",
                    textarea_cols: "80",
                    saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
			    });
              	$(".hide_course_details").click(function(){
            	    $("div.course_details").slideUp("slow");
               	});
            }
        });
        
  	});
  	
  	$("#show_new_course_form").click(function(){
  		$("#new_course").slideToggle("slow");
  	});
  	
  	$("#new_course_form :submit").click(function(){

        var dept       = $("#new_course_form #dept").val();
        var course     = $("#new_course_form #course").val();
        var credits    = $("#new_course_form #credits").val();
        var short_name = $("#new_course_form #short_name").val();
        var long_name  = $("#new_course_form #long_name").val();
        var prereq     = $("#new_course_form #prereq").val();
        var catalog    = $("#new_course_form #catalog").val();
        
        $.ajax({
            type: "POST",
            url: "<?= $docroot ?>/admin/new_course.php",
            data: "dept=" + dept + "&course=" + course + "&credits=" + credits +
            	"&short_name=" + short_name + "&long_name=" + long_name +
            	"&prereq=" + prereq + "&catalog=" + catalog,
            dataType: "text",
            success: function( msg ) {
                if( msg.indexOf( "Invalid" ) == 0 ) {
                	$("div#new_course_failure").fadeIn("fast", function(){
                		$("div#new_course_failure").fadeTo(5000, 1, function(){
                			$("div#new_course_failure").fadeOut("slow");
                		});
                	});
                	//alert( msg );
                } else {
                	$("div#courses_list").html(msg);
                	$("#courses_table").tablesorter( { sortList: [ [0,0], [1,0] ] } );
					$("div#new_course_success").fadeIn("fast", function(){
                		$("div#new_course").fadeOut("fast", function(){
                			$("div#new_course_success").fadeTo(4000, 1, function(){
                				$("div#new_course_success").fadeOut("slow");
                			});
                		});
                	});
                }
            }
        });
        return false;
    });
    
  });
</script>

<div id="new_course_success" class="success">
<p>Your new class was successfully added.</p>
</div> <!-- #new_course_success -->

<?php

if( $_SESSION[ 'admin' ] == 1 ) {
	print "<div id=\"courses_list\">\n";
	$courses_query = 'select id, dept, course, credits, short_name from courses';
	$courses_result = $db->query( $courses_query );
	if( $courses_result->num_rows == 0 ) {
		print "<p>There are no courses in the database.</p>\n";
	} else {

		print "<table class=\"tablesorter\" id=\"courses_table\">\n";
		print "<thead>\n";
		print "<tr>\n";
		print "  <th>Department</th>\n";
		print "  <th>Course</th>\n";
		print "  <th>Credits</th>\n";
		print "  <th>Short Name</th>\n";
		print "</tr>\n";
		print "</thead>\n\n";
		
		print "<tbody>\n";
		while( $row = $courses_result->fetch_assoc( ) ) {
			print "<tr id=\"{$row[ 'id' ]}\">\n";
			print "  <td name=\"{$row[ 'id' ]}\" class=\"dept\">{$row[ 'dept' ]}</td>\n";
			print "  <td name=\"{$row[ 'id' ]}\" class=\"course\">{$row[ 'course' ]}</td>\n";
			print "  <td name=\"{$row[ 'id' ]}\" class=\"credits\">{$row[ 'credits' ]}</td>\n";
			print "  <td name=\"{$row[ 'id' ]}\" class=\"short_name\">{$row[ 'short_name' ]}</td>\n";
			print "</tr>\n";
		}
        $courses_result->close( );
		print "</tbody>\n";
		print "</table>\n";
		
		print "<div class=\"course_details\">\n";
		print "</div> <!-- .course_details #{$row[ 'id' ]} -->\n";

	}
	print "</div> <!-- #courses_list -->\n";
	
	print "<p class=\"centered\">"
	    . "<a id=\"show_new_course_form\" href=\"javascript:void(0)\">Add a "
	    . "new course</a>.</p>\n";

?>
<div id="new_course">

<div id="new_course_failure" class="failure">
<p>There was a problem adding your new class to PHProf.</p>
</div> <!-- #new_course_failure -->

<form id="new_course_form">
<p>Department: <input type="text" id="dept" size="5" />
Course ID/Number: <input type="text" id="course" size="5" />
Credits: <select id="credits"></p>

<?php
	for( $c = 0; $c <= 10; $c++ ) {
		print "<option value=\"$c\">$c</option>\n";
	}
?>
</select></p>
<p>Short Course Name (25 Chars. Max.): <input type="text" id="short_name" /></p>
<p>Long Course Name: <input type="text" id="long_name" /></p>
<p>Prerequisites: <input type="text" id="prereq" /></p>
<p>Catalog Description:</p>
<p><textarea id="catalog" rows="5" cols="80"></textarea></p>

<p class="centered"><input type="submit" id="new_course" value="Create New Course" /></p>

</form>
</div> <!-- #new_course -->

<?php
	
} else {
	print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
