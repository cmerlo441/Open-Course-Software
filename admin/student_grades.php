<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $student = $db->real_escape_string( $_POST[ 'student' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );

    // What kind of grades does this class have?
    $grade_types_query = 'select t.id, t.grade_type, t.plural, w.grade_weight '
        . 'from grade_types as t, sections as s, grade_weights as w '
        . 'where w.course = s.course '
        . 'and w.grade_type = t.id '
        . "and s.id = $section "
        . 'order by w.grade_weight desc, t.grade_type';
    $grade_types_result = $db->query( $grade_types_query );
    
    print "<div id=\"student_grades\">\n";
    
    while( $grade_types_row = $grade_types_result->fetch_assoc( ) ) {
        
        // Now, for each type, see if anything has been assigned
        $grade_events_query = 'select * from grade_events '
            . "where section = $section "
            . "and grade_type = {$grade_types_row[ 'id' ]} "
            . 'order by date';
        $grade_events_result = $db->query( $grade_events_query );
        if( $grade_events_result->num_rows > 0 ) {
            print "<p style=\"text-align: center\"><b>";
            if( $grade_events_result->num_rows == 1 ) {
                print $grade_types_row[ 'grade_type' ];
            } else {
                print $grade_types_row[ 'plural' ];
            }
            print "</b> ({$grade_types_row[ 'grade_weight' ]}% of total)</p>\n";

            $sequence = 1;
            $event = array( );
            while( $event_row = $grade_events_result->fetch_assoc( ) ) {
                $event[ $sequence ][ 'date' ] = $event_row[ 'date' ];
                $event[ $sequence ][ 'id' ] = $event_row[ 'id' ];
                $event[ $sequence ][ 'assignment' ] = $event_row[ 'assignment' ];
                $grade_query = 'select grade from grades '
                    . "where grade_event = {$event_row[ 'id' ]} "
                    . "and student = $student";
                $grade_result = $db->query( $grade_query );
                if( $grade_result->num_rows == 1 ) {
                    $grade_row = $grade_result->fetch_assoc( );
                    $event[ $sequence ][ 'grade' ] = $grade_row[ 'grade' ];
                }
                
                /* Else, maybe they've been graded, and this student just
                 * didn't hand it in
                 */
                
                else {
                    $grade_check_query = 'select grade from grades '
                        . "where grade_event = {$event_row[ 'id' ]} "
                        . "and student != $student";
                    $grade_check_result = $db->query( $grade_check_query );
                    if( $grade_check_result->num_rows > 0 ) {
                        $event[ $sequence ][ 'grade' ] = 'No Grade';
                    }
                }
                
                $sequence++;
            }

            print "<table id=\"admin_grades\">\n";
            print "  <tr id=\"dates\">\n";
            foreach( $event as $sequence=>$data ) {
                print "    <td>" . date( 'n/j', strtotime( $data[ 'date' ] ) ) . "</td>\n";
            }
            print "  </tr>\n";
            print "  <tr id=\"grades\">\n";
            foreach( $event as $sequence=>$data ) {

	      /* To make these editable:
	       * $data[ 'id' ] is the event ID
	       * When you click on the grade itself, you need to also know
	       * the student ID (which is in $student).
	       *
	       * This code is in assignment.php:
	       * $('span.grade').editInPlace({
	       *     url: 'update_grade.php',
	       *     default_text: '(No grade recorded yet)',
	       *     params: "ajax=yes&assignment_id=<?php echo $assignment; ?>",
	       *     saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
	       * })
	       *
	       * So just get the assignment ID from the event, which looks like
	       * it's in $data[ 'assignment' ], and post it
	       */
    
                print "    <td><span class=\"grade\" "
                    . "id=\"$student:{$data[ 'assignment' ]}\">"
                    . "{$data[ 'grade' ]}</span></td>\n";
            }
            print "</tr>\n";
            print "</table>\n\n";
        }
    }
    
    print "</div>  <!-- div#student_grades -->\n";
?>

<script type="text/javascript">
$(document).ready(function(){
    $('span.grade').editInPlace({
        url: 'update_grade.php',
        default_text: '(No grade recorded yet)',
        params: "ajax=yes",
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })
})
</script>

<?php
    
} else {
    print $no_admin;
}
   
?>
