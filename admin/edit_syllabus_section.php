<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $default_value_query = 'select id, section, default_value '
        . 'from syllabus_sections '
        . 'where id = "' . $db->real_escape_string( $_POST[ 'id' ] ) . '"';
    $default_value_result = $db->query( $default_value_query );
    if( $default_value_result->num_rows == 1 ) {
        $row = $default_value_result->fetch_assoc( );
        print "<textarea class=\"default_value\" id=\"{$row[ 'id' ]}\">"
            . stripslashes( $row[ 'default_value' ] ) . "</textarea>\n";
        print "<input type=\"submit\" class=\"edit\" id=\"{$row[ 'id' ]}\" "
            . "value=\"Save Changes To {$row[ 'section' ]}\" />\n";
        print "<a href=\"javascript:void(0)\" id=\"{$row[ 'id' ]}\" "
            . "class=\"hide\">Hide {$row[ 'section' ]}</a>\n";
    }
}
   
?>
<script type="text/javascript">
$(document).ready(function(){
    $("textarea.default_value").markItUp(mySettings);
    
    $("a.hide").click(function(){
        var id = $(this).attr('id');
        $("div.syllabus_section[id="+id+"]").slideUp();
    })
    
    $("input.edit").click(function(){
        var id = $(this).attr('id');
        var new_content = $('textarea[id=' + id + ']').val();
        $.post( 'list_syllabus_sections.php',
            { id: id, new_content: new_content },
            function( data ) {
                $('div#current_sections').html(data);
            }
        )
    })
})
</script>