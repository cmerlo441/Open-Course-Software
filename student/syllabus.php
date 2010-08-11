<?php

$title_stub = 'Syllabus';
require_once( '../_header.inc' );

/* 
 * This file is in the student directory, so we're assuming that a student
 * is logged in, and asking for a syllabus in his/her own class
 */
 
print "<div id=\"syllabus_details\"></div>\n";

if( $_SESSION[ 'student' ] > 0 and isset( $_GET[ 'section' ] ) ) {

    $section = $db->real_escape_string( $_GET[ 'section' ] );

    $right_section_query = 'select * from student_x_section '
        . "where student = " . $db->real_escape_string( $_SESSION[ 'student' ] )
        . " and section = $section";
    $right_section_result = $db->query( $right_section_query );
    if( $right_section_result->num_rows == 1 ) {

        //OK, get the course details and print the syllabus

?>
<script type="text/javascript">
$(document).ready(function(){
    $.post( "<?php echo $docroot; ?>/syllabus_details.php",
        { section: "<?php echo $section; ?>",
          student: 1 },
        function( data ) {
            $("div#syllabus_details").html( data );
        }
    )
})
</script>
<?php
        
    }

} else {
    print $no_student;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
require_once( '../_footer.inc' );

?>
