<?php

$title_stub = 'Project Submission';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $student = $db->real_escape_string( $_GET[ 'student' ] );
    $assignment = $db->real_escape_string( $_GET [ 'assignment' ] );
    
    $student_query = 'select first, middle, last from students '
        . "where id = $student";
    $student_result = $db->query( $student_query );
    $student_row = $student_result->fetch_assoc( );
    $name = name( $student_row );
    
    $assignment_query = 'select a.grade_type, a.posted_date, a.due_date, a.title, '
        . 'c.dept, c.course, s.section, s.id '
        . 'from courses as c, sections as s, assignments as a '
        . 'where s.course = c.id '
        . 'and a.section = s.id '
        . "and a.id = $assignment";
    $assignment_result = $db->query( $assignment_query );
    $assignment_row = $assignment_result->fetch_assoc( );
    $section = $assignment_row[ 'dept' ] . ' ' . $assignment_row[ 'course' ] . ' '
        . $assignment_row[ 'section' ];
    
    print "<h2>$name, $section</h2>\n";
    
    $sequence_query = 'select * from assignments '
        . "where section = {$assignment_row[ 'id' ]} "
        . "and grade_type = {$assignment_row[ 'grade_type' ]} "
        . "and due_date <= \"{$assignment_row[ 'due_date' ]}\"";
    $sequence_result = $db->query( $sequence_query );
    $sequence = $sequence_result->num_rows;
    
    print "<h3>Project #$sequence: {$assignment_row[ 'title' ]}</h3>\n";
    
    print "<div class=\"student_uploads\" id=\"$student\">\n";
    $uploads_query = 'select * from assignment_uploads '
        . "where student = $student "
        . "and assignment = $assignment "
        . "order by filename";
    $uploads_result = $db->query( $uploads_query );
    while( $upload = $uploads_result->fetch_assoc( ) ) {
        print '<h3><a href="#">' . $upload[ 'filename' ] . ': '
            . date( 'l, n/j g:i a', strtotime( $upload[ 'datetime' ] ) );
        if( $upload[ 'datetime' ] > $assignment_row[ 'due_date' ] ) {
            $diff = strtotime( $upload[ 'datetime' ] ) - strtotime( $assignment_row[ 'due_date' ] );
            $seconds_in_a_day = 60 * 60 * 24;
            $days_late = ceil( $diff / $seconds_in_a_day );
            print ", <span class=\"late\">$days_late day"
                . ( $days_late == 1 ? '' : 's' ) . " late</span>\n";
        }
        print "</a></h3>\n";
        print "<div class=\"upload\" id=\"{$upload[ 'id' ]}\">\n";
        if( substr( $upload[ 'filename' ], -5, 5 ) == '.java' ) {
            print "<pre class=\"brush:java\">";
        } else if( substr( $upload[ 'filename' ], -3, 3 ) == '.js' ) {
            print "<pre class=\"brush:js\">";
        } else if( substr( $upload[ 'filename' ], -4, 4 ) == '.php' ) {
            print "<pre class=\"brush:php\">";
        } else if( substr( $upload[ 'filename' ], -2, 2 ) == '.c' ) {
            print "<pre class=\"brush:c\">";
        } else if( substr( $upload[ 'filename' ], -4, 4 ) == '.cpp' ) {
            print "<pre class=\"brush:cpp\">";
        } else {
            print "<pre class=\"brush:plain\">";
        }
        print htmlentities( $upload[ 'file' ] ) . "</pre>\n";
        print "</div>  <!-- div.upload#{$upload[ 'id' ]} -->\n";

    }
    print "</div>  <!-- div.student_uploads#{$student[ 'student' ]} -->\n";
    
    // See if a grade has been posted

    $grade_query = 'select g.grade '
        . 'from grades as g, grade_events as e '
        . "where grade_event = e.id "
        . "and e.assignment = $assignment "
        . "and student = $student";
    $grade_result = $db->query( $grade_query );
    if( $grade_result->num_rows == 1 ) {
        $grade_row = $grade_result->fetch_assoc( );
        $grade = $grade_row[ 'grade' ];
        $sum += $grade;
    }
    
    print "Grade: "
        . "<span class=\"grade\" id=\"$student\" size=\"4\" "
        . "id=\"$assignment\">$grade</span>\n";

?>

<script type="text/javascript">
$(document).ready(function(){
    $('div.student_uploads').accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });

    $('span.grade').editInPlace({
        url: 'update_grade.php',
        default_text: '(No grade recorded yet)',
        params: "ajax=yes&assignment_id=<?php echo $assignment; ?>",
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })
    
})
</script>

<?php

}

?>