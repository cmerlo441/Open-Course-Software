<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    if( isset( $_POST[ 'custom_content' ] ) ) {
        $course = $db->real_escape_string( $_POST[ 'course' ] );
        $section = $db->real_escape_string( $_POST[ 'section' ] );
        $custom_content = $db->real_escape_string( $_POST[ 'custom_content' ] );
        
        $check_existing_query = 'select id from syllabus_section_customization '
            . "where course = $course and syllabus_section = $section";
        $check_existing_result = $db->query( $check_existing_query );
        if( $check_existing_result->num_rows == 1 ) {
            $row = $check_existing_result->fetch_assoc( );
            $update_query = 'update syllabus_section_customization '
                . "set value = \"$custom_content\" where id = {$row[ 'id' ]}";
            $update_result = $db->query( $update_query );
        } else {
            $insert_query = 'insert into syllabus_section_customization '
                . '( id, course, syllabus_section, value ) values '
                . "( null, $course, $section, \"$custom_content\" )";
            $insert_result = $db->query( $insert_query );
        }
    }

    print "<select id=\"course\">\n";
    print "<option value=\"0\">Choose a course</option>\n";
    $courses_query = 'select id, dept, course, short_name as name '
        . 'from courses order by dept, course';
    $courses_result = $db->query( $courses_query );
    while( $row = $courses_result->fetch_assoc( ) ) {
        print "<option value=\"{$row[ 'id' ]}\">"
            . "{$row[ 'dept' ]} {$row[ 'course' ]}: {$row[ 'name' ]}</option>\n";
    }
    print "</select>\n";
    
    print "<select id=\"section\">\n";
    print "<option value=\"0\">Choose a syllabus section</option>\n";
    $syllabus_query = 'select id, section from syllabus_sections '
        . 'where editable = 1 '
        . 'order by sequence';
    $syllabus_result = $db->query( $syllabus_query );
    while( $row = $syllabus_result->fetch_assoc( ) ) {
        print "<option value=\"{$row[ 'id' ]}\">{$row[ 'section' ]}</option>\n";
    }
    print "</select>\n";
    
    print "<div id=\"inputs\" style=\"display: none\">\n";
    print "<textarea id=\"custom_content\"></textarea>\n";
    print "<input type=\"submit\" id=\"submit\" value=\"Customize\" />\n";
    print "</div>  <!-- div#inputs -->\n";
?>
<script type="text/javascript">
$(document).ready(function(){
    $('select').change(function(){
        $('div#inputs').slideUp();
        var course = $('select#course').val();
        var section = $('select#section').val();
        if( course > 0 && section > 0 ) {
            $.post( 'get_custom_syllabus_content.php',
                { course: course, section: section },
                function(data){
                    $('textarea#custom_content').markItUp(mySettings).val(data);
                    $('div#inputs').slideDown( );
                }
            )            
        }
    })
    
    $("input#submit").click(function(){
        var course = $('select#course').val();
        var section = $('select#section').val();
        if( course > 0 && section > 0 ) {
            var custom_content = $('textarea#custom_content').val();
            $.post( 'customize_syllabus_section.php',
                { course: course, section: section, custom_content: custom_content },
                function(data){
                    $('div#customize_section').html(data);
                }
            )
        }

    })
})
</script>
<?php
}

?>