<?php

$title_stub = 'Projects';
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    
    $type_id_query = 'select id from grade_types '
        . "where grade_type like \"project\"";
    $type_id_result = $db->query( $type_id_query );
    $type_id_row = $type_id_result->fetch_assoc( );
    $type_id = $type_id_row[ 'id' ];
    
    $section_id = $db->real_escape_string( $_GET[ 'section' ] );
    $sections_query = 'select s.id, c.id as course_id, c.dept, c.course, s.section '
        . 'from courses as c, sections as s, student_x_section as x '
        . "where x.student = {$_SESSION[ 'student' ]} "
        . "and x.section = $section_id "
        . 'and x.section = s.id '
        . 'and s.course = c.id '
        . 'and ( x.status = ( select id from student_statuses where status = "Grade" ) '
        . 'or x.status = ( select id from student_statuses where status = "Audit" ) '
        . 'or x.status = ( select id from student_statuses where status = "INC" ) ) '
        . 'order by c.dept, c.course';
    $sections_result = $db->query( $sections_query );
    $section_row = $sections_result->fetch_assoc( );
    $section = $section_row[ 'dept' ] . ' ' . $section_row[ 'course' ]
          . ' ' . $section_row[ 'section' ];
    
    $sequence = 1;
    
    print "<h2>Past Assignments</h2>\n";
    
    $past_query = 'select id, grade_type, posted_date, due_date, title, description '
        . 'from assignments '
        . "where grade_type = $type_id "
        . "and section = $section_id "
        . 'and due_date < "' . date( 'Y-m-d H:i:s' ) . '" '
        . 'order by due_date';
    $past_result = $db->query( $past_query );
    if( $past_result->num_rows == 0 ) {
        print 'None.';
    } else {
        print "<ul>\n";
        while( $project = $past_result->fetch_assoc( ) ) {
            print '<li>';
            print_link( "$docroot/student/project.php?section=$section_id&project=$sequence",
                "Project #$sequence: {$project[ 'title' ]}" );
            print "  (Due " . date( 'l, F j, Y', strtotime( $project[ 'due_date' ] ) )
                . ")</li>\n";
            $sequence++;
        }
        print "</ul>\n";
    }

    print "<h2>Future Assignments</h2>\n";
    
    $future_query = 'select id, grade_type, posted_date, due_date, title, description '
        . 'from assignments '
        . "where grade_type = $type_id "
        . "and section = $section_id "
        . 'and due_date >= "' . date( 'Y-m-d H:i:s' ) . '" '
        . 'order by due_date';
    $future_result = $db->query( $future_query );
    if( $future_result->num_rows == 0 ) {
        print 'None.';
    } else {
        print "<ul>\n";
        while( $project = $future_result->fetch_assoc( ) ) {
            print '<li>';
            print_link( "$docroot/student/project.php?section=$section_id&project=$sequence",
                "Project #$sequence: {$project[ 'title' ]}" );
            print "  (Due " . date( 'l, F j, Y', strtotime( $project[ 'due_date' ] ) )
                . ")</li>\n";
            $sequence++;
        }
        print "</ul>\n";
    }
    
?>

<script type="text/javascript">
$(document).ready(function(){
    var section_name = " <?php echo $type; ?> :: <?php echo $section; ?>";
    var section_id = "<?php echo $section_row[ 'id' ]; ?>";
    var student_id = "<?php echo $_SESSION[ 'student' ]; ?>";
    
    var passing_color = "<?php echo $passing_color; ?>";
    var failing_color = "<?php echo $failing_color; ?>";

    $('h1').html( $('h1').html( ) + section_name );
    $(document).attr('title', $(document).attr('title') + section_name );
    
    $('div.assignments').accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });
    
    $('div#future div.submission').editInPlace({
        url: 'submit_homework.php',
        params: 'ajax=yes',
        field_type: "textarea",
		textarea_rows: "5",
		textarea_cols: "40",
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })

    $('input.submit_comment').click(function(){
        var id = $(this).attr('id');
        var comment = $('textarea.comment[id=' + id + ']').val();
        $.post( 'add_comment.php',
            { comment: comment, submission: id },
            function(data){
                $('div#current_comments').html(data);
                $('textarea.comment[id=' + id + ']').val('');
            }
        )
    })

})
</script>

<?php
        
}
   
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
