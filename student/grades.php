<?php

$title_stub = 'Grades';
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    $sections_query = 'select s.id, c.id as course_id, c.dept, c.course, s.section '
        . 'from courses as c, sections as s, student_x_section as x '
        . "where x.student = {$_SESSION[ 'student' ]} "
        . 'and x.section = ' . $db->real_escape_string( $_GET[ 'section' ] ) . ' '
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
    
    // Determine grade types and build accordion
    
    print "<div id=\"grades\">\n";
    
    $grade_type_query = 'select t.id, t.grade_type, t.plural, '
        . 'w.course, w.grade_weight '
        . 'from grade_types as t, grade_weights as w '
        . 'where w.grade_type = t.id '
        . "and w.course = {$section_row[ 'course_id' ]} "
	. 'and w.grade_weight > 0 '
        . 'order by w.grade_weight desc';
    $grade_type_result = $db->query( $grade_type_query );
    while( $grade_type = $grade_type_result->fetch_assoc( ) ) {
        print "<h3><a href=\"#\">{$grade_type[ 'plural' ]} "
            . "({$grade_type[ 'grade_weight' ]}%)</a></h3>\n";
        print "<div class=\"specific_grades\" "
            . "name=\"{$grade_type[ 'grade_type' ]}\" id=\"{$grade_type[ 'id' ]}\">\n";
        print "<img src=\"$docroot/images/ajax-loader.gif\" height=\"16\" width=\"16\" />\n";
        print "</div>  <!-- div.specific_grades -->\n";
    }
    
    print "</div>  <!-- div#grades -->\n";
    
    $passing_color_query = 'select v from ocsw where k = "passing_green"';
    $passing_color_result = $db->query( $passing_color_query );
    $passing_color_row = $passing_color_result->fetch_assoc( );
    $passing_color = $passing_color_row[ 'v' ];

    $failing_color_query = 'select v from ocsw where k = "failing_red"';
    $failing_color_result = $db->query( $failing_color_query );
    $failing_color_row = $failing_color_result->fetch_assoc( );
    $failing_color = $failing_color_row[ 'v' ];

    print "<div id=\"class_average\">Class Average: <span id=\"class_average_span\"></span></div>\n";
?>

<script type="text/javascript">
$(document).ready(function(){
    var section_name = " :: <?php echo $section; ?>";
    var section_id = "<?php echo $section_row[ 'id' ]; ?>";
    var student_id = "<?php echo $_SESSION[ 'student' ]; ?>";

    var passing_color = "<?php echo $passing_color; ?>";
    var failing_color = "<?php echo $failing_color; ?>";

    $('h1').html( $('h1').html( ) + section_name );
    $(document).attr('title', $(document).attr('title') + section_name );
    
    $('div#grades').accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });
    
    $( 'div.specific_grades' ).each(function(){
        var grade_type = $(this).attr( 'id' );
        var grade_name = $(this).attr( 'name' );
        
        $.post( 'specific_grades.php',
            { grade_type: grade_type, section: section_id, grade_name: grade_name },
            function( data ) {
                $('div.specific_grades[id=' + grade_type + ']' ).html(data);
            }
        )
    })

    $.post( 'calculate_class_average.php',
        { section: section_id },
        function( data ) {
            $('span#class_average_span').html( data );
            if( data >= 60 && passing_color == 1 ) {
                $('span#class_average_span').addClass( 'passing' );
            } else if( data < 60 && failing_color == 1 ) {
                $('span#class_average_span').addClass( 'failing' );
            }
        }
    )
})
</script>

<?php
} else {
    print $no_student;
}
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );   
?>
