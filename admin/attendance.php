<?php

$title_stub = 'Attendance';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $types = array( );
    $types[ 0 ] = '-';
    $types_query = 'select * from attendance_types';
    $types_result = $db->query( $types_query );
    while( $type_row = $types_result->fetch_assoc( ) ) {
      $types[ $type_row[ 'id' ] ] = substr( $type_row[ 'type' ], 0, 1 );
    }
    
    $reverse_types = array( );
    foreach( $types as $id=>$type )
        $reverse_types[ $type ] = $id;
    
    $section = $db->real_escape_string( $_GET[ 'section' ] );
    $section_query = 'select c.dept, c.course, s.section, s.day '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = $section";
    $section_result = $db->query( $section_query );
    if( $section_result->num_rows == 0 ) {
        print 'Unknown section.';
    } else {
        
        $student_count_query = 'select * from student_x_section '
            . "where section = $section and active = 1";
        $student_count_result = $db->query( $student_count_query );
        $num_students = $student_count_result->num_rows;
        
        $section_row = $section_result->fetch_assoc( );
        $section_name = "{$section_row[ 'dept' ]} {$section_row[ 'course' ]} "
            . $section_row[ 'section' ];
	$day_eve = $section_row[ 'day' ];
        
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
				   date( 'Y' ) ) ) )
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

        $month_count = 0;        
        foreach( $meetings as $month=>$days ) {
            
            $display_date = date( 'F Y', strtotime( date( 'Y' ) . "-$month-01" ) );
            $id_date = date( 'Y-m', strtotime( date( 'Y' ) . "-$month-01" ) );
            
            print "<h3 class=\"attendance\" id=\"$id_date\">$display_date</h3>\n";
            print "<div class=\"attendance_table\" id=\"$id_date\">\n";

            print "<table class=\"attendance\">\n";
            print "  <thead>\n";
            print "    <tr>\n";
            print "      <th>Student</th>\n";
            foreach( $days as $day=>$val ) {
                $date = date( 'Y-m-d', strtotime( date( 'Y' ) . "-$month-$day" ) );
                $print_date = date( 'D j', strtotime( date( 'Y' ) . "-$month-$day" ) );
                print "      <th>$print_date<br />";
                print "<a href=\"javascript:void(0)\" class=\"p\" "
                    . "date=\"$date\" title=\"Mark everyone present\">P</a>\n";
                print ' | ';
                print "<a href=\"javascript:void(0)\" class=\"a\" "
                    . "date=\"$date\" title=\"Mark all unmarked students absent\">A</a>\n";
                print "</th>\n";
            }
            print "    </tr>\n";
            print "  </thead>\n\n";

            print "  <tbody>\n";                        
            $students_query = 'select s.id, s.first, s.middle, s.last '
                . 'from students as s, student_x_section as x '
                . 'where x.student = s.id '
                . "and x.section = $section "
                . 'and x.active = 1 '
                . 'order by s.last, s.first, s.middle';
            $students_result = $db->query( $students_query );
            $student_count = 1;
            while( $student = $students_result->fetch_assoc( ) ) {
                print "    <tr id=\"{$student[ 'id' ]}\">\n";
                print "      <td class=\"name\">" . lastfirst( $student ) . "</td>\n";

                /* This gets the tabindex right for every <select>.  Basically,
                 * later on we count what column # we're in, and based on that
                 * and the number of (active) students in this section, we
                 * calculate an offset into the grid.
                 * 
                 * The point here is for the tab key to move *down* one place,
                 * not across.
                 * 
                 * The reason for looking at how many days there were last month
                 * (which is what sizeof( $meetings[ $month - 1 ] ) tells us)
                 * is because if we start $column at 0 each month, then the
                 * tab key does the wrong thing, jumping back and forth between
                 * months.  Go ahead, prove it to yourself by changing the
                 * assignment below to $column = 0;  But now, by making the
                 * first column of the second month have a number one more than
                 * the number of the last column in the first month, everything
                 * works out.
                 */

                $column = sizeof( $meetings[ $month - 1 ] );
                
                foreach( $days as $day=>$val ) {
                    
                    $tabindex = ( $column * $num_students ) + $student_count;
                    
                    $date = date( 'Y-m-d', strtotime( date( 'Y' ) . "-$month-$day" ) );
                    print "      <td class=\"attendance\" date=\"$date\" "
                        . "student=\"{$student[ 'id' ]}\" count=\"$student_count\" "
                        . "id=\"date=$date&student={$student[ 'id' ]}&section=$section\">\n";
                    print "        <select class=\"attendance\" date=\"$date\" "
                        . "student=\"{$student[ 'id' ]}\" count=\"$student_count\" "
                        . "id=\"date=$date&student={$student[ 'id' ]}&section=$section\" "
                        . "tabindex=\"$tabindex\">\n";
            
                    $attendance_query = 'select presence from attendance '
                        . "where student = {$student[ 'id' ]} "
                        . "and section = $section "
                        . "and date = \"$date\"";
                    $attendance_result = $db->query( $attendance_query );
                    if( $attendance_result->num_rows == 0 ) {
                      $attendance = 0;
                    } else {
                        $attendance_row = $attendance_result->fetch_assoc( );
                        $attendance = $attendance_row[ 'presence' ];
                    }
                    
                    foreach( $types as $id=>$type ) {
                        print "          <option value=\"$id\"";
                        if( $id == $attendance ) {
                            print ' selected';
                        }
                        print ">$type</option>\n";
                    }

                    print "        </select>\n";
                    print "      </td>\n";
                    $column++;
                }
                
                print "    </tr>\n\n";
                $student_count++;
            }
            print "  </tbody>\n";
            print "</table>\n";
            print "</div>  <!-- div.attendance_table#$id_date -->\n\n";
            $month_count++;
        }
    }
    
?>

<script type="text/javascript">
$(document).ready(function(){
    
    $('select.attendance').change(function(){
        var id = $(this).attr('id');
        var attendance = $(this).val();
        
        $.post( 'set_attendance.php',
            { id: id, attendance: attendance }
        )
    });
    
    $('select.attendance').keypress(function(e){
        if( e.which == 9 /* Tab */ ) {
            alert( e.which );
        }
    })

    $('a.p').click(function(){
        var date = $(this).attr('date');
        var p = <?php echo $reverse_types[ 'P' ]; ?>;
        
        $('select.attendance[date=' + date + ']').each(function(){
            var id = $(this).attr('id');
            var value = $(this).val();

            if( value == '0' ) {
                $(this).val(p);
                value = $(this).val();
                $.post( 'set_attendance.php',
                    { id: id, attendance: value }
                )
            }
        })
    })

    $('a.a').click(function(){
        var date = $(this).attr('date');
        var a = <?php echo $reverse_types[ 'A' ]; ?>;
        
        $('select.attendance[date=' + date + ']').each(function(){
            var id = $(this).attr('id');
            var value = $(this).val();

            if( value == '0' ) {
                $(this).val(a);
                value = $(this).val();
                $.post( 'set_attendance.php',
                    { id: id, attendance: value }
                )
            }
        })
    })
    
    $('h3.attendance').hover(function(){
        $(this).css('color','#fff').css('background-color','#5d562c');
    },function(){
        $(this).css('color','#5d562c').css('background-color','#1e273e');
    })
    
    $('div.attendance_table').hide();
    var date = new Date( );
    var month = date.getMonth() + 1;
    var year = date.getFullYear( );
    var theMonth;
    if( month < 10 ) {
        theMonth = "0" + month;
    } else {
	theMonth = month;
    }
    $('div.attendance_table[id=' + year + '-' + theMonth + ']').show();
    
    $('h3.attendance').click(function(){
        var id = $(this).attr('id');
        $('div.attendance_table[id=' + id + ']').slideToggle();
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
