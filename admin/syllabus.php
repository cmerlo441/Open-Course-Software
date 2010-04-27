<?php

$title_stub = 'Syllabus';
require_once( '../_header.inc' );

/* 
 * This file is in the admin directory, so there may or may not be a particular
 * section
 */
 
print "<div id=\"syllabus_details\"></div>\n";

if( $_SESSION[ 'admin' ] == 1 ) {

  //OK, get the course details and print the syllabus
  $section = isset( $_GET[ 'section' ] ) ?
		    $db->real_escape_string( $_GET[ 'section' ] ) :
		    0;
  $course = isset( $_GET[ 'course' ] ) ?
		    $db->real_escape_string( $_GET[ 'course' ] ) :
		    0;

?>
<script type="text/javascript">
$(document).ready(function(){
    $.post( "<?php echo $docroot; ?>/syllabus_details.php",
        { section: "<?php echo $section; ?>",
          course: "<?php echo $course; ?>",
          admin: 1 },
        function( data ) {
            $("div#syllabus_details").html( data );
        }
    )
})
</script>
<?php

} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
require_once( '../_footer.inc' );

?>
