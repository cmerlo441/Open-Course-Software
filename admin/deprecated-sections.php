<?php

$title_stub = 'Sections';
require_once( '../_header.inc' );

$days = array( "Monday" => 1,
               "Tuesday" => 2,
               "Wednesday" => 3,
               "Thursday" => 4,
               "Friday" => 5,
               "Saturday" => 6,
               "Sunday" => 7 );

?>

<script type="text/javascript">
  $(document).ready(function(){
  	
  	$("div#admin_details").hide();
    $("div#new_section").hide();
    $("#show_new_section_form").click(function(){
        $("div#new_section").slideDown("slow");
    });
    
	$("div.new_meeting:gt(0)").hide();

    $("a.next_meeting :last").hide();
 	$("a.next_meeting").click(function(){
 		var id = $(this).attr('id');
 		$(this).hide();
 		$("div.new_meeting[id=" + ( id * 1 + 1 ) + "]").slideDown("slow");
 	});
    
    // Add a new section to the DB
    $("#new_section_form #new_section").click(function(){
        var data = "course=" + $("#new_section_form #course").val() +
            "&section=" + $("#new_section_form #section").val() +
            "&day_eve=" + $("#new_section_form #day_eve").val() +
            "&banner=" + $("#new_section_form #banner").val();
                    
		for (var i = 1; i <= $("select.day").size(); i++) {
            if ($("select.day[id=" + i + "]").val() == "null") {
                break;
            } // if
                
            var day = $("select.day[id=" + i + "]").val();
            var start = $("#start" + i).val();
            var end = $("#end" + i).val();
            var building = $("#bldg" + i).val();
            var room = $("#room" + i).val();
            
            data = data + "&meeting" + i + "day=" + day +
                "&meeting" + i + "start=" + start +
                "&meeting" + i + "end=" + end +
                "&meeting" + i + "building=" + building +
                "&meeting" + i + "room=" + room;
        } // for

        $.ajax({
            type: "POST",
            url: "<?php echo $docroot; ?>/admin/new_section.php",
            data: data,
            dataType: "text",
            success: function( msg ) {
               if( msg.indexOf( "Invalid" ) == 0 ) {
                	$("div#new_section_failure").fadeIn("fast", function(){
                		$("div#new_section_failure").fadeTo(5000, 1, function(){
                			$("div#new_section_failure").fadeOut("slow");
                		});
                	});
                } else {
                   	$.ajax({
                  	     type: "POST",
                  	     url: "list_sections.php",
                  	     dataType: "text",
                  	     success: function( msg ) {
                  	         $("div#sections_list").html(msg);
                             $("ul.meetings").hide();
                             $("a.show_meetings").click(function(){
                                 var id = $(this).attr("id");
                                 $("ul.meetings[id=" + id + "]" ).slideToggle("slow");
                                 return false;
                             });
                  	     }
                  	});
                    $(":text").val("");
                    $("select").val("null");
                	$("div.new_meeting:gt(0)").hide();
                    $("a.next_meeting").hide();
                    $("a.next_meeting:first").show();
					$("div#new_section_success").fadeIn("fast", function(){
                		$("div#new_section").fadeOut("fast", function(){
                			$("div#new_section_success").fadeTo(4000, 1, function(){
                				$("div#new_section_success").fadeOut("slow");
                			});
                		});
                	});
                }
            }
        }); // ajax
        return false;
  	}); // add a new section
  	
    $("input.clockpick").clockpick({
        starthour: 6,
        endhour: 23,
        showminutes: true,
        minutedivisions: 12
    });
  	
  	$.ajax({
  	     type: "POST",
  	     url: "list_sections.php",
  	     dataType: "text",
  	     success: function( msg ) {
  	         $("div#sections_list").html(msg);
             $("ul.meetings").hide();
             $("a.show_meetings").click(function(){
                 var id = $(this).attr("id");
                 $("ul.meetings[id=" + id + "]" ).slideToggle("slow");
                 return false;
             });
	     }
  	});
  
    
    
  });
</script>

<?php

if( $_SESSION[ 'admin' ] == 1 ) {
    
?>

<div id="new_section_success" class="success">
<p>Your new section was successfully added.</p>
</div> <!-- #new_section_success -->

<div id="sections_list">
</div> <!-- #sections_list -->

<p class="centered"><a href="javascript:void(0)" id="show_new_section_form">
<img src="<?= $docroot ?>/images/add_16.png" height="16" width="16"
     title="Add A Section" /> Add A Section</a></p>

<div id="new_section">

<div id="new_section_failure" class="failure">
<p>There was a problem adding your new section to PHProf.</p>
</div> <!-- #new_section_failure -->

<form id="new_section_form">
<p>Course: <select id="course">
<option value="null">Choose a course</option>
<?php
    $courses_query = 'select id, dept, course, short_name from courses '
        . 'order by dept, course';
    $courses_result = $db->query( $courses_query );
    while( $row = $courses_result->fetch_assoc( ) ) {
        print "<option value=\"{$row[ 'id' ]}\">{$row[ 'dept' ]} "
            . "{$row[ 'course' ]}: {$row[ 'short_name' ]}</option>\n";
    }
    $courses_result->close( );
?>
</select></p>
<p>Section: <input type="text" id="section" size="5" />
Day: <input type="radio" name="day_eve" id="day_eve" value="Day" checked />
Evening: <input type="radio" name="day_eve" id="day_eve" value="Evening" /></p>

<p>Banner ID: <input type="text" id="banner" /></p>

<p>When does this class meet?</p>

<?php
for( $meeting = 1; $meeting <= 10; $meeting++ ) {
?>

<div class="new_meeting" id="<?= $meeting ?>">

<select class="day" id="<?= $meeting ?>">
<p><option value="null">Choose a day</option>
<?php
foreach( $days as $name=>$value ) {
    print "<option value=\"$value\">$name</option>\n";
}
?>
</select>

From <input class="clockpick" id="start<?= $meeting ?>" size="7" />
to <input class="clockpick" id="end<?= $meeting ?>" size="7" />

in Building: <input type="text" id="bldg<?= $meeting ?>" size="5" />
Room: <input type="text" id="room<?= $meeting ?>" size="5" />

&nbsp;&nbsp;<a href="javascript:void(0)" class="next_meeting" id="<?= $meeting ?>">
<img src="<?= $docroot ?>/images/add_16.png" height="16" width="16"
     title="Add another meeting" /></a></p>

</div> <!-- .new_meeting #<?= $meeting ?> -->
<?php
} // for
?>

<p class="centered"><input type="submit" id="new_section" value="Create Section" /></p>
</form>
</div> <!-- #new_section -->

<?php
	
} else {
	print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
