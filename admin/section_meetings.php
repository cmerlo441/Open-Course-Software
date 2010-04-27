<?php

$no_header = 1;
require_once( '../_header.inc' );

$short_days = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );

$days = array( "Monday" => 1,
               "Tuesday" => 2,
               "Wednesday" => 3,
               "Thursday" => 4,
               "Friday" => 5,
               "Saturday" => 6,
               "Sunday" => 7 );

if( $_SESSION[ 'admin' ] == 1 ) {
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    
    if( isset( $_POST[ 'day' ] ) ) {
        $day = $db->real_escape_string( $_POST[ 'day' ] );
        $start = $db->real_escape_string( $_POST[ 'start' ] );
        $end = $db->real_escape_string( $_POST[ 'end' ] );
        $building = $db->real_escape_string( $_POST[ 'building' ] );
        $room = $db->real_escape_string( $_POST[ 'room' ] );
        
        $insert_query = 'insert into section_meetings '
            . '( id, section, day, start, end, building, room ) '
            . "values( null, $section, $day, "
            . '"' . date( 'H:i:s', strtotime( $start ) ) . '", '
            . '"' . date( 'H:i:s', strtotime( $end ) ) . '", '
            . "\"$building\", \"$room\" )";
        $insert_result = $db->query( $insert_query );
    }
    
    else if( isset( $_POST[ 'remove' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'remove' ] );
        $remove_query = 'delete from section_meetings '
            . "where id = $id";
        $remove_result = $db->query( $remove_query );
    }
    
    print "<ul class=\"section_meetings\">\n";
    $meetings_query = "select * from section_meetings where section = $section "
        . "order by day, start";
    $meetings_result = $db->query( $meetings_query );
    while( $meeting_row = $meetings_result->fetch_assoc( ) ) {
        print "<li id=\"{$meeting_row[ 'id' ]}\">\n";
        print "<a href=\"javascript:void(0)\" class=\"remove_meeting\" "
            . "id=\"{$meeting_row[ 'id' ]}\" title=\"Remove this meeting\">"
            . "<img src=\"$docroot/images/cancel_16.png\" height=\"16\" "
            . "width=\"16\" title=\"Remove this meeting\" /></a>";
        print $short_days[ $meeting_row[ 'day' ] % 7 ] . ' ';
        print date( 'g:i a', strtotime( $meeting_row[ 'start' ] ) );
        print ' to ';
        print date( 'g:i a', strtotime( $meeting_row[ 'end' ] ) );
        print " in {$meeting_row[ 'building' ]} {$meeting_row[ 'room' ]}\n";
        print "</li>\n";
    }
    $meetings_result->close( );
    print "<li><a href=\"javascript:void(0)\" class=\"add_meeting\" id=\"{$_POST[ 'section' ]}\">"
        . "<img src=\"$docroot/images/add_16.png\" height=\"16\" width=\"16\" "
        . "title=\"Add new meeting\" /></a>\n";
    print " <a href=\"javascript:void(0)\" class=\"add_meeting\" id=\"{$_POST[ 'section' ]}\">"
        . "Add new meeting</a></li>\n";
    print "</ul>\n";
    
?>

<div class="dialog" id="add_section_meeting" title="Add Meeting"></div>

<script type="text/javascript">
$(document).ready(function(){
    var section = "<?php echo $section; ?>";

    $('a.add_meeting').click(function(){
        var id = $(this).attr('id');
        $.post( 'add_meeting_dialog.php',
            { id: id },
            function(data){
                $('div#add_section_meeting').html(data).dialog({
                    autoOpen: true,
                    hide: 'puff',
                    modal: true,
                    buttons: {
                        'Add Meeting': function(){
                            var day = $('select#day').val();
                            var start = $('input#start').val();
                            var end = $('input#end').val();
                            var building = $('input#building').val();
                            var room = $('input#room').val();
                            
                            $.post( 'section_meetings.php',
                                {
                                    section: section,
                                    day: day,
                                    start: start,
                                    end: end,
                                    building: building,
                                    room: room
                                },
                                function(data){
                                    $("div.section_meetings[id=" + section + "]").html(data);
                                }
                            )
                            $(this).dialog('destroy');
                        },
                        'Cancel': function(){
                            $(this).dialog('destroy');
                        }
                    }
                })
            }
        )
    })
    
    $('a.remove_meeting').click(function(){
        var id = $(this).attr('id');
        
        $.post( 'section_meetings.php',
            { section: section, remove: id },
            function(data){
                $("div.section_meetings[id=" + section + "]").html(data);
            }
        )
    })
})
</script>

<?php
}   
?>
