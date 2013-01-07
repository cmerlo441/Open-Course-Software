<?php

$title_stub = 'List of Absences';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $sections_query = 'select s.id, c.dept, c.course, s.section '
        . 'from sections as s, courses as c '
        . 'where s.course = c.id '
        . 'order by c.dept, c.course, s.section';
    $sections_result = $db->query( $sections_query );
    
    print 'Excused absences are written in <span style="font-style: italic;">italic</span>.';
    
    while( $section = $sections_result->fetch_object( ) ) {
        print "<div class=\"section_absences\" id=\"$section->id\">\n";
        print "<h2>$section->dept $section->course $section->section</h2>\n";
        
        // Get all the meetings for this section
        
        $days = array( );
        
        $days_query = 'select day from section_meetings '
            . "where section = $section->id";
        $days_result = $db->query( $days_query );
        while( $days_row = $days_result->fetch_assoc( ) ) {
            $days[ ] = $days_row[ 'day' ];
        }
        
        /* $meetings will contain $month=>$day, one for each meeting
         * of this class between the start of the semester and now/end
         * of semester, whichever is earlier
         */
        
        $meetings = array( );
        
        $today = date( 'Y-m-d' );
        for( $date = $semester_start;
             $date <= ($today < $semester_end ? $today : $semester_end );
             $date = date( 'Y-m-d',
               mktime( 0, 0, 0, date( 'n', strtotime( $date ) ),
                   date( 'j', strtotime( $date ) ) + 1,
		       date( 'Y', strtotime( $date ) ) ) ) )
        {
            $day = date( 'w', strtotime( $date ) );
            
            $resched_query = 'select follow from rescheduled_days '
                . "where date = \"$date\" and "
                . ( $section_row[ 'day' ] == 1 ? 'day' : 'evening' )
                . ' = 1';
            $resched_result = $db->query( $resched_query );
            if( $resched_result->num_rows == 1 ) {
                $row = $resched_result->fetch_assoc( );
                $day = $row[ 'follow' ];
            }
            
            $holiday_query = 'select '
                . ( $day_eve == 1 ? 'day' : 'evening' )
                . ' from holidays '
                . "where " . ( $day_eve == 1 ? 'day' : 'evening' ) . ' = 1 '
                . "and date = \"$date\"";
            $holiday_result = $db->query( $holiday_query );
            if( $holiday_result->num_rows == 1 ) {
                $day = -1;
            }
            
            if( in_array( $day, $days ) ) {
                $meetings[ date( 'n', strtotime( $date ) ) ][ date( 'j', strtotime( $date ) ) ] = 1;
            }
        }

/*
        foreach( array_keys( $meetings ) as $month ) {
            foreach( array_keys( $meetings[ $month ] ) as $day ) {
                print "      <th>$month/$day</th>\n";
            }
        }
*/
        
        $students_query = 'select s.first, s.last, s.id '
            . 'from students as s, student_x_section as x '
            . 'where x.student = s.id '
            . "and x.section = $section->id "
            . 'order by s.last, s.first';
        $students_result = $db->query( $students_query );
        $even = 0;
        while( $student = $students_result->fetch_object( ) ) {
            $name = ucwords( "$student->first $student->last" );
            print "<div class=\"student row$even\" id=\"$student->id\">$name\n";
            $attendance_query = 'select a.date, t.type '
                . 'from attendance as a, attendance_types as t '
                . "where a.student = $student->id "
                . "and a.section = $section->id "
                . 'and a.presence = t.id '
                . 'order by a.date';
            $attendance_result = $db->query( $attendance_query );

            $absences = array();
            foreach( array_keys( $meetings ) as $month )
                foreach( array_keys( $meetings[ $month ] ) as $day )
                    $absences[ "$month-$day" ] = 1;
            while( $attendance = $attendance_result->fetch_object( ) ) {
                if( $attendance->type == 'Present' ) {
                    $m = date( 'n', strtotime( $attendance->date ) );
                    $d = date( 'j', strtotime( $attendance->date ) );
                    $absences[ "$m-$d" ] = 0;
                } else if( $attendance->type == 'Excused' ) {
                    $m = date( 'n', strtotime( $attendance->date ) );
                    $d = date( 'j', strtotime( $attendance->date ) );
                    $absences[ "$m-$d" ] = 2;
                }
            }

/*            
            print "<pre>";
            print_r( $absences );
            print "</pre>\n";
*/
            
            $values = array_count_values( $absences );

            print "<span class=\"absences\">\n";
            print '(' . ( $values[ 1 ] + $values[ 2 ] ) . ')';
            if( $values[ 1 ] + $values[ 2 ] > 0 ) {
                print ': ';
                $first = true;
                foreach( $absences as $date=>$presence ) {
                    if( $presence > 0 ) {
                        if( ! $first )
                            print ', ';
                        if( $presence == 2 ) {
                            print "<span class=\"excused\" style=\"font-style: italic;\">";
                        }
                        print str_replace( '-', '/', $date );
                        if( $presence == 2 ) {
                            print "</span>";
                        }
                        $first = false;
                    }
                }
            }
            print "</span>\n";
            print "</div>\n";

            $even = ( $even == 0 ? 1 : 0 );
        }
	print "</div>\n";
    }
?>

<script type="text/javascript">

$(function(){
    $('table.attendance_summary > tbody > tr > td.attendance_data').each(function(){
        var section = $(this).attr('section');
        var student = $(this).parent().attr('id');
        var month = $(this).attr('month');
        var day = $(this).attr('day');
        
        $.post( 'attendance_day.php',
            { section: section, student: student, month: month, day: day },
            function(data){
                $(this).html(data);
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
