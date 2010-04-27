<?php

$no_header = 1;
require_once( './_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    
    $sections_query = 'select s.id, c.id as course_id, c.dept, c.course, s.section '
        . 'from courses as c, sections as s, student_x_section as x '
        . "where x.student = {$_SESSION[ 'student' ]} "
        . 'and x.section = s.id '
        . 'and s.course = c.id '
        . 'and x.active = 1 '
        . 'order by c.dept, c.course';
    $sections_result = $db->query( $sections_query );
?>

<h2><?php echo $_SESSION[ 'name' ]; ?></h2>
<div id="student_top">
<p>You will be automatically logged out at
<?php print date( 'g:i a', mktime( date( 'H' ), date( 'i' ) + 10 ) ); ?>.  
<?php print_link( 'javascript:void(0)', 'Log out now.', 'logout', 'Log out now.' ); ?>
</p>
<p>
    <?php /* print_link( 'javascript:void(0)',
                      "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" width=\"16\" style=\"border: 0\" />",
                      'hide', 'Hide this pane.' ); */ ?>
    <?php /* print_link( 'javascript:void(0)', 'Hide this pane.', 'hide', 'Hide this pane.' ); */ ?>
</p>
</div>  <!-- div#student_top -->

<div class="accordion">

<?php
    while( $section_row = $sections_result->fetch_assoc( ) ) {
?>
    <h3><a href="#"><?php print $section_row[ 'dept' ] . ' '
        . $section_row[ 'course' ] . ' ' . $section_row[ 'section' ]; ?></a></h3>
    <div class="section" id="<?php echo $section_row[ 'id' ];?>">
        <ul>
            <li id='syllabus'>
                <?php print_link( "$student/syllabus.php"
                                  . "?section={$section_row[ 'id' ]}",
                                  'Syllabus' ); ?>
            </li>
            <li id='grades'>
                <?php print_link( "$student/grades.php"
                                  . "?section={$section_row[ 'id' ]}",
                                  'Grades' ); ?>
            </li>
            <li id='attendance'>
                <?php print_link( "$student/attendance.php"
                                  . "?section={$section_row[ 'id' ]}",
                                  'Attendance' ); ?>
            </li>
            <li id='homework'>
                <?php print_link( "$student/homework.php"
                                  . "?section={$section_row[ 'id' ]}",
                                  'Homework' ); ?>
            </li>

            </li>
            <li id='reference'>
                <?php print_link( "$student/reference_materials.php"
                                  . "?section={$section_row[ 'id' ]}",
                                  'Reference Materials' ); ?>
            </li>

<?php
        foreach( explode( ' ', 'project lab' ) as $grade_type ) {
            $grade_type_query = 'select id, grade_type, plural from grade_types '
                . "where grade_type like \"%$grade_type%\"";
            $grade_type_result = $db->query( $grade_type_query );
            $grade_type_row = $grade_type_result->fetch_assoc( );
            $grade_type = $grade_type_row[ 'id' ];

            $grade_query = 'select id from grade_weights '
                . "where course = {$section_row[ 'course_id' ]} "
                . "and grade_type = $grade_type ";
            $grade_result = $db->query( $grade_query );
            if( $grade_result->num_rows > 0 ) {
                print strtolower( "<li id=\"{$grade_type_row[ 'plural' ]}\">" );
                print_link( 
                    strtolower(
                        str_replace( ' ', '_',
                                     "$student/{$grade_type_row[ 'plural' ]}.php" ) )
                    . "?section={$section_row[ 'id' ]}",
                    $grade_type_row[ 'plural' ] );
                print "</li>\n";
            }
        }
?>

            <li id='send_mail'>
                <?php print_link( "$student/send_mail.php"
                                  . "?section={$section_row[ 'id' ]}",
                                  "Send Prof. {$prof[ 'last' ]} E-Mail" ); ?>
            </li>

        </ul>
    </div>  <!-- div.section#<?php print $section_row[ 'section' ]; ?> -->
<?php
    }
?>

</div>  <!-- div.accordion -->

<script type="text/javascript">
$(document).ready(function(){
    $(".accordion div>ul").css("padding","0");
    $(".accordion").accordion({
        active: 0,
        autoHeight: false,
        collapsible: true
    });
        
    $("a.logout").click(function(){
    
        // Unset all the session variables
        $.ajax({
            type: "POST",
            url: "<?php echo $docroot; ?>/logout.php"
    	});
    
        $("div#student").fadeOut(500, function(){
            $.ajax({
                type: "POST",
                url: "<?php echo $docroot; ?>/not_logged_in_tools.php",
                success: function( msg ) {
                    $("div#not_logged_in").html( msg ).fadeIn(500);
                }
            })
        });
        return false;
    });
    
})
</script>
    
<?php
} else {
    print $no_student;
}

?>
