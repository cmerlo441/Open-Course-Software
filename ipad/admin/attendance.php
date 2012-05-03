<?php

require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $types = array( );
    $types[ 0 ] = '-';
    $types_query = 'select * from attendance_types';
    $types_result = $db->query( $types_query );
    while( $type_row = $types_result->fetch_assoc( ) ) {
      $types[ $type_row[ 'id' ] ] = $type_row[ 'type' ];
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

        // Now $meetings is filled.
        //print "<pre>";
        //print_r( $meetings );
        //print "</pre>\n";
        
        if( isset( $_GET[ 'date' ] ) ) {
            $month = date( 'n', strtotime( $_GET[ 'date' ] ) );
            $date = date( 'j', strtotime( $_GET[ 'date' ] ) );
        } else {
            $month = date( 'n' );
            $date = date( 'j' );
        }
        
        if( $meetings[ $month ][ $date ] != 1 )
            for( ; $month >= 1; $month-- )
                for( ; $date >= 1; $date-- )
                    if( $meetings[ $month ][ $date ] == 1 )
                        break 2;
                    
        $the_date = date( 'Y-m-d', mktime( 0, 0, 0, $month, $date ) );
        
        // Find the last day before today
        $yesterday = mktime( 0, 0, 0, $month, $date - 1 );
        for( $m = date( 'n', $yesterday ); $m >= 1; $m-- )
            for( $d = date( 'd', $yesterday ); $d >= 1; $d-- )
                if( $meetings[ $month ][ $date ] == 1 )
                    break 2;

        print "<div data-role=\"header\">\n";
        print "<h1>$section_name, " . date( 'l, M jS', mktime( 0, 0, 0, $month, $date ) ) . "</h1>\n";
        
        for( $m = date( 'n', $yesterday ); $m >= 1; $m-- )
            for( $d = date( 'j', $yesterday ); $d >= 1; $d-- )
                if( array_key_exists( $d, $meetings[ $month ] ) ) {
                    $last_time = date( 'Y-m-d', mktime( 0, 0, 0, $m, $d ) );
                    print "<a href=\"$docroot/admin/attendance.php?section=$section&date=$last_time\" "
                        . "data-icon=\"back\" class=\"ui-btn-left\">Last Class</a>\n";
                    break 2;
                }
        
        // Find the first day after today
        $tomorrow = mktime( 0, 0, 0, $month, $date + 1 );
        for( $m = date( 'n', $tomorrow ); $m <= 12; $m++ )
            for( $d = date( 'j', $tomorrow ); $d <= 31; $d++ )
                if( array_key_exists($d, $meetings[ $month ] ) ) {
                    $next_time = date( 'Y-m-d', mktime( 0, 0, 0, $m, $d ) );
                    print "<a href=\"$docroot/admin/attendance.php?section=$section&date=$next_time\" "
                        . "data-icon=\"forward\" class=\"ui-btn-right\">Next Class</a>\n";
                    break 2;
                }
            
            
        print "</div>\n";
        
        $students_query = 'select s.id, s.first, s.middle, s.last '
            . 'from students as s, student_x_section as x '
            . 'where x.student = s.id '
            . "and x.section = $section "
            . 'and x.active = 1 '
            . 'order by s.last, s.first, s.middle';
        $students_result = $db->query( $students_query );
        $count = 0;
        while( $student = $students_result->fetch_object( ) ) {
?>
    <div data-role="fieldcontain" id="<?php echo $student->id; ?>" class="student ui-body ui-body-<?php echo $count % 2 == 0 ? 'd' : 'c'; ?>">
        <fieldset data-role="controlgroup" data-type="horizontal">
            <legend><strong><?php print ucwords($student->first) . ' ' . ucwords($student->last) . ": "; ?></strong></legend>
<?php
                $attendance_query = 'select presence from attendance '
                    . "where student = \"$student->id\" "
                    . "and section = $section "
                    . "and date = \"" . date( 'Y-m-d', strtotime( date( 'Y' ) . "-$month-$date" ) ) . "\"";
                $attendance_result = $db->query( $attendance_query );
                if( $attendance_result->num_rows == 0 ) {
                  $attendance = 0;
                } else {
                    $attendance_row = $attendance_result->fetch_assoc( );
                    $attendance = $attendance_row[ 'presence' ];
                }

                foreach( $types as $id=>$type ) {
                    if( $type != '-' ) {
?>
            <input type="radio" name="<?php echo $student->id; ?>" id="<?php echo "$student->id-$id"; ?>" value="<?php echo $type; ?>" <?php if( $id == $attendance ) print 'checked="checked"'; ?> />
            <label for="<?php echo "$student->id-$id"; ?>"><?php echo $type; ?></label>
<?php
                    }
                }
?>
        </fieldset>
    </div> <!-- div.student -->
    
<?php
            $count++;
        } // while there are students left to draw buttons for
?>
    <div data-role="fieldcontain" class="ui-body ui-body-e">
        <fieldset data-role="controlgroup" data-type="horizontal">
            <legend><strong>Everybody Else:</strong></legend>
<?php
        foreach( $types as $id=>$type ) {
            if( $type != '-' ) {
?>
            <input type="radio" name="everybody" id="<?php echo $id; ?>" value="<?php echo $type; ?>" />
            <label for="<?php echo $id; ?>"><?php echo $type; ?></label>
<?php
            }
        }
?>
        </fieldset>
    </div>

<?php        
    } // if this section exists
?>

<script type="text/javascript">

$('#the_page').live('pageinit',function(){
    var date = "<?php echo $the_date; ?>";
    var section = "<?php echo $section; ?>";

    $('div.student input:radio').click(function(){
        var student_id = $(this).attr('name');
        var id = $(this).attr('id');
        var presence = id.substring( id.indexOf( "-" ) + 1 );
        var post_string = "date=" + date + "&student=" + student_id + "&section=" + section;
        $.post( "<?php echo $main_site; ?>/admin/set_attendance.php",
            { attendance: presence, id: post_string }
        )
    })
    
    $('input:radio[name=everybody]').click(function(){
        var presence = $(this).attr('id');
        $(this).attr('checked',false).checkboxradio('refresh');
        $('div.student').each(function(){
            var student = $(this).attr('id');
            if( $('div.student[id=' + student + ']').find('input:radio:checked').length == 0 ) {
                var id = student + '-' + presence;
                $('input#' + id).attr('checked',true).checkboxradio('refresh');
                var post_string = "date=" + date + "&student=" + student + "&section=" + section;
                $.post( "<?php echo $main_site; ?>/admin/set_attendance.php",
                    { attendance: presence, id: post_string }
                )
            }
        })
    })

})

</script>

<?php
    
} else {
    print 'You are not authorized to view this page.';
}

require_once( '../_footer.inc' );
?>
