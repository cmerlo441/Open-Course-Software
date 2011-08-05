?php

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

    /* This whole section is going to go away */
    /*
     *
     */

    
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
	. "name=\"day_eve\" value=\"day\" checked=\"checked\" /> Day "
        . "<input type=\"radio\" name=\"day_eve\" value=\"eve\" /> "
	. "Evening "
        . "<input type=\"radio\" name=\"day_eve\" value=\"weekend\" /> "
	. "Weekend</td>\n";
    print "  </tr>\n";
    print "</table>\n";
    print "</div> <!-- .dialog#add_section -->\n";
    print "</li></ul>\n";
    
    /*
     * 
     * End of section that's going to go away
     */
    
    print "<div id=\"new_section_dialog\" class=\"dialog\"></div>\n";
    
    print "<p style=\"text-align: center; padding-top: 1em\">"
        . "<button id=\"new_section\">Add A Section</button></p>\n";
    
?>
<script type="text/javascript">
$(document).ready(function(){

/*    
    $.post('list_sections.php', function(data){
        $("div#sections_list").html(data);
    })
*/
    
    $.post('list_sections.php', function(data){
        $("div#sections_list").html(data);
    })
    
    $('button#new_section').button().click(function(){
        $.post('add_section_dialog.php', function(data){
            $('div#new_section_dialog').dialog('destroy').html(data).dialog({
                autoOpen: true,
                hide: 'puff',
                modal: true,
                title: 'Add New Section',
                width: 400,
                buttons: {
                    'Add This Section': function(){
                        var course  = $('div#new_section_dialog select#course option:selected').attr('id');
                        var section = $('div#new_section_dialog input#section').val();
                        var crn     = $('div#new_section_dialog input#crn').val();
                        var day     = $('div#new_section_dialog input#day').attr('checked') ? 1 : 0;
                        
                        $.post('add_section.php',
                            { course: course, section: section, crn: crn, day: day },
                            function(){
                                $.post('list_sections.php', function(data){
                                    $('div#sections_list').html(data);
                                })
                            }
                        )
                        
                        $('div#new_section_dialog input:text').val('');
                        $('div#new_section_dialog input:radio').attr('checked',false);
                        $('div#new_section_dialog select').val(0);
                        
                        $('div#new_section_dialog').dialog('destroy');
                    }
                }
            });
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
