<?php

require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $student = $db->real_escape_string( $_GET[ 'student' ] );
    $section = $db->real_escape_string( $_GET[ 'section' ] );
    
    $student_query = 'select s.id, s.first, s.last, s.banner, x.active '
        . 'from students as s, student_x_section as x '
        . 'where x.student = s.id '
        . "and x.section = $section "
        . "and s.id = $student"; 
    $student_result = $db->query( $student_query );
    $student = $student_result->fetch_object();
    
    $course_query = 'select c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . "where s.id = $section "
        . 'and s.course = c.id';
    $course_result = $db->query( $course_query );
    $course = $course_result->fetch_object( );
    $course_name = "$course->dept $course->course $course->section";
    
    print "<div data-role=\"fieldcontain\" data-inset=\"true\">\n";
    print "<div data-role=\"header\" data-theme=\"a\"><h2>$course_name :: "
        . ucwords( $student->first ) . ' ' . ucwords( $student->last )
        . "</h2></div>\n";
    
    print "<div class=\"ui-grid-a\">\n";

    /*
     * Class Average
     */

    print "<div class=\"ui-block-a\">\n";
    print "<ul data-role=\"listview\" data-inset=\"true\">\n";
    print "<li data-role=\"list-divider\">Class Average</li>\n";
    print "<li id=\"average\" data-theme=\"e\">Average</li>\n";
    print "</ul>\n";
    print "</div>  <!-- ui-block-a -->\n";
    
    /*
     * Logins
     */
    
    print "<div class=\"ui-block-b\">\n";
    $logins_query = 'select * '
        . 'from logins '
        . "where student = $student->id "
        . 'order by datetime desc';
    $logins_result = $db->query( $logins_query );
    print "<ul data-role=\"listview\" data-inset=\"true\">\n";
    print "<li data-role=\"list-divider\">Last Login<span class=\"ui-li-count\">"
        . "$logins_result->num_rows total</span></li>\n";
    if( $logins_result->num_rows == 0 ) {
	print "<li>" . ucwords( $student->first ) . " has never logged in.</li>\n";
    } else {
	$login = $logins_result->fetch_object( );
	print "<li>" . date( 'D, M j, g:i A', strtotime( $login->datetime ) );
	print "<p class=\"ui-li-aside\">$login->address</p>\n";
	print "</li>\n";
    }
    print "</ul>\n";
    print "</div>  <!-- ui-block-b -->\n";
    
    print "</div>  <!-- ui-grid-a -->\n";
    
    /*
     * Absences
     */
    
    print "<div class=\"ui-grid-a\">\n";
    
    print "<div class=\"ui-block-a\">\n";
    $attendance_query = 'select date from attendance '
        . "where student = $student->id "
        . "and section = $section "
        . 'and presence = ( select id from attendance_types where type = "absent" ) '
        . 'order by date';
    $attendance_result = $db->query( $attendance_query );
    $count = $attendance_result->num_rows;
    print "<ul data-role=\"listview\" data-inset=\"true\">\n";
    print "<li data-role=\"list-divider\">Absences"
        . "<span class=\"ui-li-count\">$count</span></li>\n";
    if( $count == 0 ) {
        print "<li>" . ucwords( $student->first ) . " has never been absent!</li>\n";
    } else {
        while( $a = $attendance_result->fetch_object( ) ) {
            print "<li>" . date( 'D, M j', strtotime( $a->date ) ) . "</li>\n";
        }
    }
    print "</ul>\n";
    print "</div>\n";
    
    /*
     * Excused Absences
     */
    
    print "<div class=\"ui-block-b\">\n";
    $attendance_query = 'select date from attendance '
        . "where student = $student->id "
        . "and section = $section "
        . 'and presence = ( select id from attendance_types where type = "excused" ) '
        . 'order by date';
    $attendance_result = $db->query( $attendance_query );
    $count = $attendance_result->num_rows;
    print "<ul data-role=\"listview\" data-inset=\"true\">\n";
    print "<li data-role=\"list-divider\">Excused Absences"
        . "<span class=\"ui-li-count\">$count</span></li>\n";
    if( $count == 0 ) {
        print "<li>" . ucwords( $student->first ) . " has no excused absences.</li>\n";
    } else {
        while( $a = $attendance_result->fetch_object( ) ) {
            print "<li>" . date( 'D, M j', strtotime( $a->date ) ) . "</li>\n";
        }
    }
    print "</ul>\n";
    print "</div>\n";
    
    print "</div>\n\n";

    /*
     * Individual Grades
     */

    // What kind of grades does this class have?
    $grade_types_query = 'select t.id, t.grade_type, t.plural, w.grade_weight '
        . 'from grade_types as t, sections as s, grade_weights as w '
        . 'where w.course = s.course '
        . 'and w.grade_type = t.id '
        . "and s.id = $section "
        . 'and w.grade_weight > 0 '
        . 'order by w.grade_weight desc, t.grade_type';
    $grade_types_result = $db->query( $grade_types_query );

    while( $grade_types_row = $grade_types_result->fetch_object( ) ) {
        $grade_type = $grade_types_row->id;
        
        // Now, for each type, see if anything has been assigned
        $grade_events_query = 'select * from grade_events '
            . "where section = $section "
            . "and grade_type = $grade_type "
            . 'and date < "' . date( 'Y-m-d G:i:s' ) . '" '
            . 'order by date';
        $grade_events_result = $db->query( $grade_events_query );
        if( $grade_events_result->num_rows > 0 ) {
            
            print "<ol data-role=\"listview\" data-inset=\"true\">\n";
            print "<li data-role=\"list-divider\">"
                . ( $grade_events_result->num_rows == 1 ? $grade_types_row->grade_type : $grade_types_row->plural )
                . " ($grade_types_row->grade_weight% of total)"
                . "<span class=\"ui-li-count\">$grade_events_result->num_rows</span></li>\n";
            while( $grade_event = $grade_events_result->fetch_object( ) ) {

		/* Have any of these been graded? */

		$been_graded_query = 'select grade from grades '
		    . "where grade_event = $grade_event->id";
		$been_graded_result = $db->query( $been_graded_query );
		$been_graded = $been_graded_result->num_rows;

                $grade_query = 'select grade '
                    . 'from grades '
                    . "where student = $student->id "
                    . "and grade_event = $grade_event->id";
                $grade_result = $db->query( $grade_query );
                if( $grade_result->num_rows == 1 ) {
                    $grade = $grade_result->fetch_object( );
                    print '<li';
                    if( $grade->grade == 1 )
                        print ' data-theme="e"';
                    print '>';
                    print $grade->grade;
                } else {
                    print '<li>' . ( $been_graded == 0 ? 'Not graded yet' : 'No grade' );
                }
                print "<p class=\"ui-li-aside\">" . date( 'n/j', strtotime( $grade_event->date ) ) . "</p>";
                print "</li>\n";
            }
            print "</ol>\n\n";
        } // if there were grades of that type
    }

?>

<script type="text/javascript">

$('#the_page').live('pageinit',function(){
    
    var student = "<?php echo $student->id; ?>";
    var section = "<?php echo $section; ?>";

    $.post( "<?php echo $main_site; ?>/admin/calculate_student_average.php",
        { section: section, student: student },
        function(data){
            $('li#average').html(data);
        }
    )
})

</script>

<?php
    
} else {
    print 'You are not authorized to view this page.';
}

require_once( '../_footer.inc' );
?>
