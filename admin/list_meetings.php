<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $days = array( "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", 
		   "Friday", "Saturday", "Sunday" );

    $section = $db->real_escape_string( $_POST[ 'section' ] );
    
    $meetings_query = 'select * from section_meetings '
        . "where section = $section "
        . 'order by day, start, end';
    $meetings_result = $db->query( $meetings_query );
    $num_meetings = $meetings_result->num_rows;

    if( $meetings_result->num_rows == 0 ) {
        print 'No meetings defined.';
    } else {
        while( $meeting = $meetings_result->fetch_object( ) ) {
            print "<div class=\"meeting\" id=\"$meeting->id\">\n";
            print "<a href=\"javascript:void(0)\" class=\"remove_meeting\" id=\"$meeting->id\" "
                . "alt=\"Remove this meeting\" title=\"Remove this meeting\">\n";
            print "<img src=\"../images/silk_icons/cross.png\" height=\"16\" width=\"16\" "
                . "alt=\"Remove this meeting\" />\n";
            print "</a>\n<span class=\"details\">";
            print $days[ $meeting->day ] . ' ' . date( 'g:i a', strtotime( $meeting->start ) )
                . ' to '
                . date( 'g:i a', strtotime( $meeting->end ) )
                . " in $meeting->building $meeting->room</span><br />\n";
            print "</div>  <!-- div.meeting#$meeting->id -->\n";
        }
    }
    print "</div>  <!-- div.meeting_list#$row->id -->\n";
?>

<script type="text/javascript">

$(document).ready(function(){
    
    var section = "<?php echo $section; ?>";
    
    $('a.remove_meeting').click(function(){
        var id = $(this).attr('id');
        $.post('remove_meeting.php',
            { meeting: id },
            function(data){
               $('div.meeting[id=' + id + ']').hide();
               if( $('div.meeting_list[id=' + section + '] div.meeting:visible').size() == 0 ) {
                   $('div.meeting_list[id=' + section + ']').html('No meetings defined.');
               }
            }
        )
    })
})

</script>

<?php
}

?>