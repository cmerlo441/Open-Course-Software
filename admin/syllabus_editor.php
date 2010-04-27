<?php

$title_stub = 'Syllabus Editor';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    print "<h2>Current Syllabus Sections</h2>\n";
    print "<div id=\"current_sections\">\n";
    print "</div>  <!-- div#current_sections -->\n";
    
    print "<h2>Customize a Syllabus Section For a Course</h2>\n";
    print "<div id=\"customize_section\">\n";
    print "</div>  <!-- div#customize_section -->\n";
    
    print "<h2>Create New Syllabus Section</h2>\n";
    print "<div id=\"new_section\">\n";
    
    print "<p>New Section Title: <input type=\"text\" id=\"section\" /></p>\n";
    print "<div id=\"new_section_details\" style=\"display: none\">\n";
    print "<p><textarea id=\"default_value\"></textarea></p>\n";
    print "<input type=\"submit\" value=\"Create New Section\" id=\"create\" />\n";
    print "</div>  <!-- div#new_section_details -->\n";
    
    print "</div>  <!-- div#new_section -->\n";
?>
<script type="text/javascript">
$(document).ready(function(){
    $.post( 'list_syllabus_sections.php',
        function(data){
            $('div#current_sections').html(data);
        }
    )
    
    $.post( 'customize_syllabus_section.php',
        function(data){
            $('div#customize_section').html(data);
        }
    )
    
    $("textarea").markItUp(mySettings);
    
    $("div#new_section input#section").focus(function(){
        $("div#new_section div#new_section_details").slideDown();
    })
    
    $('div#new_section input#create').click(function(){
        var section = $('div#new_section input[id=section]').val();
        var default_value = $('textarea#default_value').val();
        $.post( 'list_syllabus_sections.php',
            { new_title: section, new_value: default_value },
            function( data ) {
                $('div#current_sections').html(data);
                $('div#new_section input[id=section]').val('');
                $('div#new_section textarea#default_value').val('');
                $('div#new_section div#new_section_details').slideUp();
            }
        )
    })
    
})
</script>
<?php

} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
require_once( '../_footer.inc' );
   
?>
