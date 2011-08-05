<?php

$no_header = 1;
require_once ('../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
    
    print "<div class=\"dialog\" id=\"add_meeting\"></div>\n";
    print "<div class=\"dialog\" id=\"edit_meeting\"></div>\n";
    print "<div class=\"dialog\" id=\"delete_meeting\"></div>\n";
    
    $sections_query = 'select c.dept, c.course, s.section, s.id '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . 'order by c.dept, c.course, s.section';
    $sections_result = $db->query( $sections_query );
    if( $sections_result->num_rows == 0 ) {
        print 'There are no sections.';
    } else {
        print "<div class=\"accordion\" id=\"sections\">\n";
        while( $row = $sections_result->fetch_object( ) ) {
            print "<h3><a href=\"#\">$row->dept $row->course $row->section</a></h3>\n";
            print "<div class=\"section\" id=\"$row->id\">\n";
            
            print "<div class=\"meeting_list\" id=\"$row->id\">\n";
            print "</div>  <!-- div.meeting_list#$row->id -->\n";
            
            // Add a meeting button
            
            print "<div class=\"add_meeting\" id=\"$row->id\" "
                . "style = \"text-align: center; padding: 1em 0 0.5em 0\">\n";
            print "<button class=\"add_meeting\" id=\"$row->id\">Add a Meeting</button>\n";
            print "</div>  <!-- div.add_meeting#$row->id -->\n\n";
            
            // Delete this section button
            print "<div class=\"delete_section\" id=\"$row->id\" "
                . "style=\"text-align: center; padding: 1em 0 0.5em 0\">\n";
            print "<button class=\"delete_section\" id=\"$row->id\">Delete This Section</button>\n";
            print "</div>  <!-- div.delete_section#$row->id -->\n\n";
                        
            print "</div>  <!-- div.section#$row->id -->\n";
        }
    }
?>

<script type="text/javascript">

$(document).ready(function(){
    
    $('div.meeting_list').each(function(){
        var section = $(this).attr('id');
        $.post('list_meetings.php',
            { section: section },
            function(data){
                $('div.meeting_list[id=' + section + ']').html(data);
            }
        )
    })
    
    $("div#sections").accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });
    
    $('div.meeting').css('padding', '1em 0').css('border-bottom', '1px dotted');
    
    $('button.add_meeting').button().click(function(){
        var section = $(this).attr('id');
        $.post('add_meeting_dialog.php',
            {section: section},
            function(data){
                $('div#add_meeting').dialog('destroy');
                $('div#add_meeting').html(data).dialog({
                    autoOpen: true,
                    hide: 'puff',
                    modal: true,
                    title: 'Add Meeting',
                    buttons: {
                        'Add This Meeting': function(){
                            var day = $('div#add_meeting select#day option:selected').attr('value') * 1;
                            var start = $('div#add_meeting input#start').val();
                            var end = $('div#add_meeting input#end').val();
                            var building = $('div#add_meeting input#building').val();
                            var room = $('div#add_meeting input#room').val();
                            
                            $.post( 'add_meeting.php',
                                { section: section, day: day, start: start, end: end, building: building, room: room },
                                function(data){
                                    $.post('list_meetings.php',
                                        { section: section },
                                        function(data){
                                            $('div.meeting_list[id=' + section + ']').html(data);

					    /*
					     * Wherever I put this code, it doesn't seem to run.

					    $('div#add_meeting select:day > option:first').attr('selected',true);
					    $('div#add_meeting input:text').val('');
					    $('div#add_meeting').dialog('destroy');

					    *
					    */
                                        }
				   );
                                }
			    );
                        }
                    }
                })
            }
        )
    })

    $('button.edit_meeting').button({
        /*
        icons: {
            secondary: 'ui-icon-gear'
        }
        */
    }).click(function(){
        var id = $(this).attr('id');
        $.post( 'edit_meeting_dialog.php',
            { id: id },
            function(data) {
                var accordion_id = $('button.edit_meeting[id=' + id + ']').parent().parent().parent().attr('id');
                console.log('accordion id is ' + accordion_id);
                $('div#edit_meeting').html("").dialog('destroy');
                $('div#edit_meeting').html(data).dialog({
                    autoOpen: true,
                    hide: 'puff',
                    modal: true,
                    title: 'Edit Meeting',
                    buttons: {
                        'Save Changes': function() {
                            var day = $('div#edit_meeting select#day').val();
                            var start = $('div#edit_meeting input#start').val();
                            var end = $('div#edit_meeting input#end').val();
                            var building = $('div#edit_meeting input#building').val();
                            var room = $('div#edit_meeting input#room').val();

                            $.post('edit_meeting.php',
                                { id: id, day: day, start: start, end: end, building: building, room: room },
                                function(data){
                                    /* Get the HTML back from edit_meeting and only update div.meeting[id= meeting_id ] */
                                    $('div.meeting[id=' + id + '] > div.info').html(data);
                                }
                            )
                            
                            $('div#edit_meeting').html("").dialog('destroy');
                        },
                        'Cancel': function(){
                            $('div#edit_meeting').html("").dialog('destroy');
                        }
                    }
                })
                return false;
            }
        )
        return false;
    })
    
    $('button.delete_meeting').button({
        /*
        icons: {
            secondary: 'ui-icon-gear'
        }
        */
    }).click(function(){
        var id = $(this).attr('id');
        $.post( 'delete_meeting_dialog.php',
            function(data) {
                $('div#delete_meeting').html(data).dialog();
            }
        )    
    });
    
    $('button.delete_section').button({
    }).click(function(){
        alert( 'Hi!' );
    })
    

})

</script>

<?php
}

?>