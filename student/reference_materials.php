<?php

$title_stub = 'Reference Materials';
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {

    $section = $db->real_escape_string( $_GET[ 'section' ] );
    
    $course_query = 'select c.id as course_id, c.dept, c.course, '
        . 's.section, s.id as section_id '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = $section";
    $course_result = $db->query( $course_query );
    $course_row = $course_result->fetch_assoc( );
    $course_name = $course_row[ 'dept' ] . ' ' . $course_row[ 'course' ] . ' '
        . $course_row[ 'section' ];
?>
    
<div id="ref_list"></div>  <!-- div#ref_list -->

<script type="text/javascript">
$(document).ready(function(){
    var course = " :: <?php echo $course_name; ?>";
    var section = "<?php echo $course_row[ 'section_id' ]; ?>";
    
    $('h1').html( $('h1').html( ) + course );
    $(document).attr('title', $(document).attr('title') + course );
    
    $.post( 'list_reference_materials.php',
        { section: section },
        function( data ) {
            $('div#ref_list').html(data);
        }
    )

    setInterval( function(){
        $.post( 'list_reference_materials.php',
            { section: section },
            function( data ) {
                $('div#ref_list').html(data);
            }
        )
    }, 2500 );

})
</script>

<?php
} else {
    print $no_student;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>