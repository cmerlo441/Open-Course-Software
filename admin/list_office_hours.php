<?php 

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday',
        'Friday', 'Saturday' );
    
    if( isset( $_POST[ 'remove' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'remove' ] );
        $db->query( 'delete from office_hours '
            . "where id = $id" );
    }
    
    if( isset( $_POST[ 'day' ] ) and
        isset( $_POST[ 'start' ] ) and
        isset( $_POST[ 'end' ] ) and
        isset( $_POST[ 'building' ] ) and
        isset( $_POST[ 'room' ] ) ) {
        $day = $db->real_escape_string( $_POST[ 'day' ] );
        $start = date( 'H:i:s',
            strtotime( $db->real_escape_string( $_POST[ 'start' ] ) ) );
        $end = date( 'H:i:s',
            strtotime( $db->real_escape_string( $_POST[ 'end' ] ) ) );
        $building = $db->real_escape_string( $_POST[ 'building' ] );
        $room = $db->real_escape_string( $_POST[ 'room' ] );
        $db->query( "insert into office_hours( id, day, start, end, building, room ) "
            . "values( null, $day, \"$start\", \"$end\", \"$building\", \"$room\" )" );
    }
    
    $hours_query = 'select id, day, start, end, building, room '
        . 'from office_hours '
        . 'order by day, start';
    $hours_result = $db->query( $hours_query );
    if( $hours_result->num_rows == 0 )
        print 'No office hours defined yet.';
    else {
        while( $hour = $hours_result->fetch_object( ) ) {
            print "<a href=\"javascript:void(0)\" class=\"remove\" id=\"$hour->id\">";
            print "<img src=\"$docroot/images/silk_icons/cross.png\" "
                . "height=\"16\" width=\"16\" alt=\"Remove this entry\" "
                . "title=\"Remove this entry\" /></a>\n";
            print "{$days[ $hour->day ]}, "
                . date( 'g:i a', strtotime( $hour->start ) ) . ' to '
                . date( 'g:i a', strtotime( $hour->end ) ) . ' in '
                . "$hour->building $hour->room";
            print "<br />\n";
        }
    }
?>

<script type="text/javascript">

$(document).ready(function(){
	$('a.remove').click(function(){
		var id = $(this).attr('id');
		$.post('list_office_hours.php',
			{ remove: id },
			function(data){
				$('div#current_office_hours').html(data);
			}
		)
	})
})

</script>

<?php

}

?>