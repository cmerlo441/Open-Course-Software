<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday',
		   'Friday', 'Saturday' );

    if( isset( $_POST[ 'date' ] ) ) {

	$date    = $db->real_escape_string( $_POST[ 'date' ] );
	$day     = $db->real_escape_string( $_POST[ 'day' ] );
	$evening = $db->real_escape_string( $_POST[ 'evening' ] );
	$follow  = $db->real_escape_string( $_POST[ 'follow' ] );

        $insert_query = 'insert into rescheduled_days'
	        . '( id, date, day, evening, follow ) '
            . 'values( null, "'
            . date( 'Y-m-d', strtotime( $date ) ) . '", '
	        . "$day, $evening, $follow )";
        $insert_result = $db->query( $insert_query );
    }

    else if( isset( $_POST[ 'remove' ] ) ) {
	$id = $db->real_escape_string( $_POST[ 'remove' ] );
	$remove_query = 'delete from rescheduled_days '
	    . "where id = $id";
	$remove_result = $db->query( $remove_query );
    }
    
    $rescheduled_days_query = 'select * from rescheduled_days '
        . "where date >= \"" . date( 'Y-m-d', strtotime( $semester_start ) ) . '" '
        . "and date <= \"" . date( 'Y-m-d', strtotime( $semester_end ) ) . '" '
        . 'order by date';
    $rescheduled_days_result = $db->query( $rescheduled_days_query );
    if( $rescheduled_days_result->num_rows == 0 ) {
        print 'None.';
    } else {
        while( $row = $rescheduled_days_result->fetch_assoc( ) ) {
            print "<div class=\"rescheduled_day\" id=\"{$row[ 'id' ]}\">\n";
            print "<span class=\"remove\" id=\"{$row[ 'id' ]}\">";
            print "<a href=\"javascript:void(0)\" "
		        . "class=\"remove_rescheduled_day\" id=\"{$row[ 'id' ]}\" "
                . "title=\"Remove {$row[ 'date' ]}\">";
            print "<img src=\"$docroot/images/silk_icons/cancel.png\" "
		        . "height=\"16\" width=\"16\" /></a></span>\n";
            print "<span class=\"date\" id=\"{$row[ 'id' ]}\">"
                . date( 'l n/j', strtotime( $row[ 'date' ] ) ) . "</span>:\n";
            print "<span class=\"description\" id=\"{$row[ 'id' ]}\">";
            if( $row[ 'day' ] == 1 ) {
                print 'Day ';
                if( $row[ 'evening' ] == 1 ) {
                    print 'and evening ';
                }
            } else if( $row[ 'evening' ] == 1 ) {
                print 'Evening ';
            }
            print "classes follow {$days[ $row[ 'follow' ] ]} "
		. "schedule</span>.\n";
        }
    }
?>

<script type="text/javascript">
$(document).ready(function(){
    $('a.remove_rescheduled_day').click(function(){
        var id = $(this).attr('id');
	$.post( 'rescheduled_days.php', { remove: id },
            function(data) {
                $('div#rescheduled_days').html(data);
	    }
        )
    })
})
</script>

<?php
}
?>
