<?php

$title_stub = 'Attendance';
require_once( '../_header.inc' );
require_once( "$fileroot/Calendar.inc" );

if( $_SESSION[ 'student' ] > 0 ) {
    $section = $db->real_escape_string( $_GET[ 'section' ] );
    $section_query = 'select c.dept, c.course, s.section '
        . 'from courses as c, sections as s, student_x_section as x '
        . 'where s.course = c.id '
        . 'and x.section = s.id '
        . "and x.student = {$_SESSION[ 'student' ]} "
        . "and x.section = $section";
    $section_result = $db->query( $section_query );
    if( $section_result->num_rows == 1 ) {
        
        $section_row = $section_result->fetch_assoc( );
        $section_name = $section_row[ 'dept' ] . ' ' . $section_row[ 'course' ]
            . ' ' . $section_row[ 'section' ];
        
        $present_query = 'select count( a.id ) as p '
            . 'from attendance as a, attendance_types as t '
            . "where a.student = {$_SESSION[ 'student' ]} "
            . "and a.section = $section "
            . 'and a.presence = t.id '
            . 'and t.type = "present"';
        $present_result = $db->query( $present_query );
        $present_row = $present_result->fetch_assoc( );
        $present = $present_row[ 'p' ];
        
        $absent_query = 'select count( a.id ) as a '
            . 'from attendance as a, attendance_types as t '
            . "where a.student = {$_SESSION[ 'student' ]} "
            . "and a.section = $section "
            . 'and a.presence = t.id '
            . 'and t.type = "absent"';
        $absent_result = $db->query( $absent_query );
        $absent_row = $absent_result->fetch_assoc( );
        $absent = $absent_row[ 'a' ];
        
        $excused_query = 'select count( a.id ) as e '
            . 'from attendance as a, attendance_types as t '
            . "where a.student = {$_SESSION[ 'student' ]} "
            . "and a.section = $section "
            . 'and a.presence = t.id '
            . 'and t.type = "excused"';
        $excused_result = $db->query( $excused_query );
        $excused_row = $excused_result->fetch_assoc( );
        $excused = $excused_row[ 'e' ];

        print "<div id=\"attendance_summary\">\n";
        print "You have attended $present class meeting"
            . ( $present == 1 ? '' : 's' ) . ".<br />\n";
        print "<span id=\"absent\">You have been absent from $absent meeting"
            . ( $absent == 1 ? '' : 's' ) . ".</span><br />\n";
        print "You have $excused excused absence"
            . ( $excused == 1 ? '' : 's' ) . ".\n";
        print "</div>\n";
        
        $dates_query = 'select start, end from semester';
        $dates_result = $db->query( $dates_query );
        $dates_row = $dates_result->fetch_assoc( );
        $month = date( 'n', strtotime( $dates_row[ 'start' ] ) );
        $end = date( 'n', strtotime( $dates_row[ 'end' ] ) );
        if( date( 'Y-m-d' ) < $dates_row[ 'end' ] ) {
            $end = date( 'n' );
        }
        
        while( $month <= $end ) {
            $attendance = array( );
            $attendance_query = 'select a.date, t.type '
                . 'from attendance as a, attendance_types as t '
                . "where a.student = {$_SESSION[ 'student' ]} "
                . "and a.section = $section "
                . "and a.date >= \""
                . date( 'Y-m-d',
                        strtotime( date( 'Y' ) . '-' . $month . '-01' ) )
                . '" '
                . 'and a.date <= "'
                . date( 'Y-m-t', strtotime( date( 'Y' ) . '-' . $month ) )
                . '" '
                . 'and a.presence = t.id '
                . 'order by a.date';
            $attendance_result = $db->query( $attendance_query );
            while( $attendance_row = $attendance_result->fetch_assoc( ) ) {
                $attendance[ date( 'j', strtotime( $attendance_row[ 'date' ] ) ) ] =
                    $attendance_row[ 'type' ];
            }
            
            $calendar = new Calendar( $month, date( 'Y' ), $docroot, $db, $attendance );
            $calendar->disp( );
            
            $month++;
        }
        
    } else {
        print 'You are not enrolled in that section.';
    }

?>

<script type="text/javascript">
$(document).ready(function(){
    var section_name = " :: <?php echo $section_name; ?>";
    var section_id = "<?php echo $section_row[ 'id' ]; ?>";
    var student_id = "<?php echo $_SESSION[ 'student' ]; ?>";
    
    $('h1').html( $('h1').html( ) + section_name );
    $(document).attr('title', $(document).attr('title') + section_name );
})
</script>

<?php

} else {
    print $no_student;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
