<?php

$title_stub = 'Edit Current Sections';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    // Prepare list of courses
    $course_query = 'select id, dept, course, short_name from courses '
        . 'order by dept, course';
    $course_result = $db->query( $course_query );
    while( $course_row = $course_result->fetch_assoc( ) ) {
        $courses[ $course_row[ 'id' ] ] =
	    "{$course_row[ 'dept' ]} {$course_row[ 'course' ]}: "
            . "{$course_row[ 'short_name' ]}";
    }
    $course_result->close( );
    
    print "<div id=\"sections_list\"></div>\n";
    
    print "<div class=\"dialog\" id=\"add_section\" "
	. "title=\"Add New Section\">\n";
    print "<table>\n";
    print "  <tr>\n";
    print "    <td>Course</td><td><select id=\"course\">\n";
    foreach( $courses as $key=>$value ) {
        print "      <option value=\"$key\">$value</option>\n";
    }
    print "      </select></td>\n";
    print "  </tr>\n";
    print "  <tr>\n";
    print "    <td>Section</td><td><input type=\"text\" "
	. "id=\"section_name\" size=\"3\" /></td>\n";
    print "  </tr>\n";
    print "  <tr>\n";
    print "    <td>CRN</td><td><input type=\"text\" "
	. "id=\"banner\" size=\"8\" /></td>\n";
    print "  </tr>\n";
    print "  <tr>\n";
    print "    <td colspan=\"2\"><input type=\"radio\" "
	. "name=\"day_eve\" value=\"day\" /> Day "
        . "<input type=\"radio\" name=\"day_eve\" value=\"eve\" /> "
	. "Evening "
        . "<input type=\"radio\" name=\"day_eve\" value=\"weekend\" /> "
	. "Weekend</td>\n";
    print "  </tr>\n";
    print "</table>\n";
    print "</div> <!-- .dialog#add_section -->\n";
    print "</li></ul>\n";
    
?>
<script type="text/javascript">
$(document).ready(function(){
    
    $.post('list_sections.php', function(data){
        $("div#sections_list").html(data);
    })
    
    $("a.add_section").click(function(){
        $("div#add_section").dialog({
            autoOpen: true,
            modal: true,
            width: 400,
            buttons: {
                'Add Section': function(){
                    $.post('new_section.php',
                        {
                            course: $("select#course").val(),
                            section: $("input#section_name").val(),
                            banner: $("input#banner").val(),
                            day_eve: $("input:radio").val()
                        }, function(data){
                            $.post('list_sections.php', function(data){
                                $("div#sections_list").html(data);
                            })
                        }
                    )
                    $("div#add_section").dialog('destroy');
                },
                'Cancel': function(){
                    $("div#add_section").dialog('destroy');
                }
            }
        });
    });
})
</script>

<?php

} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
