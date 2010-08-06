<?php

$title_stub = 'Sign-In Sheet';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $section = $db->real_escape_string( $_GET[ 'section' ] );
    $section_query = 'select c.dept, c.course, s.section, s.day '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = $section";
    $section_result = $db->query( $section_query );
    if( $section_result->num_rows == 0 ) {
        print 'Unknown section.';
    } else {
        $section_row = $section_result->fetch_assoc( );
        $section_name = "{$section_row[ 'dept' ]} {$section_row[ 'course' ]} "
            . $section_row[ 'section' ];
        
        /* $days will contain numbers representing the days of the week this
         * class meets
         */
        
        $days = array( );
        
        $days_query = 'select day from section_meetings '
            . "where section = $section";
        $days_result = $db->query( $days_query );
        while( $days_row = $days_result->fetch_assoc( ) ) {
            $days[ ] = $days_row[ 'day' ];
        }
        
        /* $meetings will contain $month=>$day, one for each meeting of this class
         * between the start of the semester and now/end of semester, whichever
         * is earlier
         */
        
        $meetings = array( );
        
        $today = date( 'Y-m-d' );
        $tomorrow = date( 'Y-m-d', mktime( 0, 0, 0, date( 'n' ), date( 'j' ) + 1 ) );
        for( $date = $today; $date <= $semester_end;
             $date = date( 'Y-m-d', mktime( 0, 0, 0,
                           date( 'n', strtotime( $date ) ), date( 'j', strtotime( $date ) ) + 1, date( 'Y' ) ) ) )
        {
            $day = date( 'w', strtotime( $date ) );
            $resched_query = 'select follow from rescheduled_days '
                . "where date=\"$date\" "
                . 'and ' . ( $section_row[ 'day' ] == 1 ? 'day' : 'evening' ) . ' = 1';
            $resched_result = $db->query( $resched_query );
            if( $resched_result->num_rows == 1 ) {
                $row = $resched_result->fetch_assoc( );
                $day = $row[ 'follow' ];
            }
            
            $holiday_query = 'select '
                . ( $section_row[ 'day' ] == 1 ? 'day' : 'evening' )
                . ' from holidays '
                . "where date = \"$date\"";
            $holiday_result = $db->query( $holiday_query );
            if( $holiday_result->num_rows == 1 ) {
                $day = -1;
            }
            
            if( in_array( $day, $days ) ) {
                $meetings[ date( 'n', strtotime( $date ) ) ][ date( 'j', strtotime( $date ) ) ] = 1;
            }
        }
        
        print "<p class=\"noprint\">Create sign-in sheet for: <select id=\"date\">\n";
        foreach( $meetings as $month=>$days ) {
            foreach( $days as $day=>$ignore ) {
                $date = date( 'Y-m-d', strtotime( date( 'Y' ) . "-$month-$day" ) );
                print "<option value=\"$date\">";
                if( $date == $today ) {
                    print 'Today';
                } else if( $date == $tomorrow ) {
                    print 'Tomorrow';
                } else {
                    print date( 'l', strtotime( $date ) );
                }
                print ', ' . date( 'F j', strtotime( $date ) ) . "</option>\n";
            }
        }
        print "</select></p>\n\n";
        
        print "<div id=\"sign_in_list\"></div>\n";
    }
?>

<script type="text/javascript">
$(document).ready(function(){
    document.title = document.title + " :: <?php echo $section_name; ?>";
    $("h1").html( $("h1").html() + " for <?php echo $section_name; ?>" );
    
    var section = "<?php echo $section; ?>";

    var date = $('select#date').val();
    var dateString = $('select#date :selected').text();
    $.post( 'sign_in_list.php',
            { section: section, date: date },
            function( data ) {
                $('div#sign_in_list').html(data);
            }
    )

    $('select#date').change(function(){
        var date = $(this).val();
        var dateString = $('select#date :selected').text();
        $.post( 'sign_in_list.php',
            { section: section, date: date },
            function( data ) {
                $('div#sign_in_list').html(data);
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
include( "$fileroot/_footer.inc" );

?>