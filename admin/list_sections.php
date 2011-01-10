<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $sections_query = 'select c.dept, c.course, s.section, s.id '
        . 'from courses as c, sections as s '
        . 'where c.id = s.course '
        . 'order by c.dept, c.course, s.section';
    $sections_result = $db->query( $sections_query );
    print "<ul id=\"sections_list\">\n";
    if( $sections_result->num_rows == 0 ) {
        print "<p>No sections defined.</p>\n";
    } else {
        while( $section_row = $sections_result->fetch_assoc( ) ) {
            $id = $section_row[ 'id' ];
            $name = "{$section_row[ 'dept' ]} {$section_row[ 'course' ]} "
                . "{$section_row[ 'section' ]}";
            print "<li class=\"section\" id=\"$id\">\n";
            print "<a href=\"javascript:void(0)\" class=\"remove_section icon\" "
                . "id=\"{$section_row[ 'id' ]}\" title=\"Remove $name\">"
                . "<img src=\"$docroot/images/cancel_16.png\" height=\"16\" "
                . "width=\"16\" title=\"Remove $name\" /></a>\n";
            print "<a href=\"javascript:void(0)\" class=\"section\" id=\"$id\">"
                . "$name</a>\n";
            print "<div class=\"section_meetings\" id=\"$id\">\n";
            print "</div>  <!-- div.section_meetings#$id -->\n";
            print "</li>\n";
        }
        $sections_result->close( );
    }

    print "<li id=\"add_section\">";
    print "<a href=\"javascript:void(0)\" class=\"add_section icon\">"
        . "<img src=\"$docroot/images/add_16.png\" height=\"16\" "
        . "width=\"16\" title=\"Add new section\" /></a>\n";
    print "<a href=\"javascript:void(0)\" class=\"add_section\">"
        . " Add new section</a>\n";
    print "</ul>\n";

?>

<script type="text/javascript">
$(document).ready(function(){
    $("a.section").click(function(){
       var id = $(this).attr('id');

       if ($("div.section_meetings[id=" + id + "]").css("display") == 'none') {
           $.ajax({
               type: "POST",
               url: "<?php echo $admin; ?>/section_meetings.php",
               data: "section=" + id,
               dataType: "text",
               success: function(msg){
                   $("div.section_meetings[id=" + id + "]").html(msg).slideDown('500');
               }
           });
       } else {
           $("li.section[id=" + id + "]")
           $("div.section_meetings[id=" + id + "]").slideUp('500').html('');
       }
    });
    
    $("a.add_section").click(function(){
        alert( 'hi' );
        $("div#add_section").dialog({
            autoOpen: true,
            modal: true,
            width: 400,
            buttons: {
                'Add Section': function(){
    
                    var course;
                    var section_name;
                    var crn;
                    var day_eve;
        
                    course = $('select#course').val( );
                    section_name = $('input#section_name').val( ).trim();
                    crn = $('input#banner').val().trim();
                    day_eve = $('input:radio').val();
    
                    if( section_name == "" || crn == "" ) {
                        if( section_name == "" )
                            $.pnotify({
                                pnotify_title: 'Section Name',
                                pnotify_text: 'You must supply the section name.',
                                pnotify_shadow: true,
                                pnotify_type: 'error'
                            })
                        if( crn == "" )
                            $.pnotify({
                                pnotify_title: 'CRN',
                                pnotify_text: 'You must supply this section\'s CRN.',
                                pnotify_shadow: true,
                                pnotify_type: 'error'
                            })
                    }
                    else {
                        alert( "section_name is '" + section_name + "'" );
                        $.post('new_section.php',
                            {
                                course: course,
                                section: section_name,
                                banner: crn,
                                day_eve: day_eve
                            }, function(data){
                                $.post('list_sections.php', function(data){
                                    $("div#sections_list").html(data);
                                })
                            }
                        )
                        $("div#add_section").dialog('destroy');
                    }
                },
                'Cancel': function(){
                    $("div#add_section").dialog('destroy');
                }
            }
        });
    });
    
    $("a.remove_section").click(function(){
        var id = $(this).attr('id');
    
        $.post('edit_section.php',
            { action: 'remove_section', section: id },
            function(){
                $.post('list_sections.php', function(data){
                    $("div#sections_list").html(data);
                })
            }
        )
    })

})
</script>

<?php

} else {
    print $no_admin;
}

?>
