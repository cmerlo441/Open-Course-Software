<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    if( trim( $_POST[ 'update_value' ] != trim( $_POST[ 'original_html' ] ) ) ) {
        $update_value = $db->real_escape_string( trim( $_POST[ 'update_value' ] ) );
        $student = $db->real_escape_string( $_POST[ 'element_id' ] );
        $assignment = $db->real_escape_string( $_POST[ 'assignment_id' ] );

        // If $student has a colon in it, then the assignment id is after it
        
        if( preg_match( '/([0-9]+):([0-9]+)/', $student, $matches ) ) {
            $student = $matches[ 1 ];
            $assignment = $matches[ 2 ];
        }

	// Get assignment details
	$grade_type_query = 'select t.id, t.grade_type '
	  . 'from grade_types as t, assignments as a '
	  . "where a.id = $assignment "
	  . 'and t.id = a.grade_type';
	$grade_type_result = $db->query( $grade_type_query );
	$grade_type_row = $grade_type_result->fetch_assoc( );
	$grade_type = $grade_type_row[ 'grade_type' ];
	$grade_type_id = $grade_type_row[ 'id' ];

	$date_query = 'select section, due_date from assignments '
	  . "where id = $assignment";
	$date_result = $db->query( $date_query );
	$date_row = $date_result->fetch_assoc( );
	$date = date( 'n/j/Y', strtotime( $date_row[ 'due_date' ] ) );

	$sequence_query = 'select count( * ) as c from assignments '
	  . "where grade_type = $grade_type_id "
	  . "and section = {$date_row[ 'section' ]} "
	  . "and due_date <= \"{$date_row[ 'due_date' ]}\" "
	  . 'order by due_date';
	//	print $sequence_query;
	$sequence_result = $db->query( $sequence_query );
	$sequence_row = $sequence_result->fetch_assoc( );
	$sequence = $sequence_row[ 'c' ];
        
	$course_name_query = 'select c.dept, c.course, s.section '
	  . 'from courses as c, sections as s, assignments as a '
	  . 'where s.course = c.id '
	  . 'and a.section = s.id '
	  . "and s.id = {$date_row[ 'section' ]}";
	$course_name_result = $db->query( $course_name_query );
	$course_name_row = $course_name_result->fetch_assoc( );
	$course_name = $course_name_row[ 'dept' ] . ' ' . $course_name_row[ 'course' ]
	  . ' ' . $course_name_row[ 'section' ];

        // Get this grade event
        
        $event_query = 'select * from grade_events '
            . "where assignment = $assignment";
        $event_result = $db->query( $event_query );
        $event_row = $event_result->fetch_assoc( );
        $event = $event_row[ 'id' ];
        
        // See if a grade has been recorded already
        
        $grade_query = 'select * from grades '
            . "where grade_event = $event "
            . "and student = $student";
        $grade_result = $db->query( $grade_query );
        if( $grade_result->num_rows == 0 ) {
            
            // We have to insert a new grade
            $insert_query = 'insert into grades ( id, grade_event, student, grade ) '
                . "values( null, $event, $student, $update_value )";
            $insert_result = $db->query( $insert_query );
            if( $db->affected_rows == 1 ) {
                print $update_value;
            } else {
                print $_POST[ 'original_html' ];
            }
        } else {
            
            // Else, we have to change a current grade
            
            $grade_row = $grade_result->fetch_assoc( );
            $update_query = 'update grades '
                . "set grade = $update_value "
                . "where id = {$grade_row[ 'id' ]}";
            $update_result = $db->query( $update_query );
            if( $db->affected_rows == 1 ) {
                print $update_value;
            } else {
                print $_POST[ 'original_html' ];
            }
        }
        
?>
<script type="text/javascript">
$(document).ready(function(){
    var student = "<?php echo $student; ?>";
    var section = "<?php echo $event_row[ 'section' ]; ?>";
    
    $.post( 'assignment_statistics.php',
        {
	  event: "<?php echo $event; ?>",
          date: "<?php echo $date; ?>",
	  type: "<?php echo $grade_type; ?>",
	  sequence: "<?php echo $sequence; ?>",
	  course_name: "<?php echo $course_name; ?>"
        },
        function( data ) {
            $('div#stats').html(data);
            $.post( 'calculate_student_average.php',
                { section: section, student: student },
                function(data){
                    $('span#average').html(data);
                    $('td.average[student=' + student + ']' ).html(data);
                    $('#roster_table').trigger('update');
                }
            )
        }
    )
})
</script>
<?php
        
    } else {
        print $_POST[ 'original_html' ];
    }
}
?>
