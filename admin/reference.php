<?php

$title_stub = 'Reference Materials';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $section = $db->real_escape_string( $_GET[ 'section' ] );

    print "<div class=\"dialog\" id=\"info\"></div>\n";
    
    // What class is this?
    
    $course_query = 'select c.id as course_id, c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = $section";
    $course_result = $db->query( $course_query );
    $course_row = $course_result->fetch_assoc( );
    $course_name = $course_row[ 'dept' ] . ' ' . $course_row[ 'course' ] . ' '
        . $course_row[ 'section' ];
    
    print "<h2>Upload New Reference Material</h2>\n";
    print "<div id=\"file_upload\">\n";
    print "Upload a file:  <div id=\"fileUpload\"></div>\n";
    print "</div>  <!-- div#file_upload -->\n";
    
    print "<h2>Current Reference Materials</h2>\n";
    print "<div id=\"current\"></div>\n";
    
?>

<script type="text/javascript">
$(document).ready(function(){
    var course = " :: <?php echo $course_name; ?>";
    
    $('h1').html( $('h1').html( ) + course );
    $(document).attr('title', $(document).attr('title') + course );
    
    $.post( 'list_reference_materials.php',
        { section: "<?php echo $section; ?>" },
        function( data ) {
            $('div#current').html(data);
        }
    )

    $('#fileUpload').uploadify({
        'uploader': '../uploadify/uploadify.swf',
        'script': './reference_upload.php',
        'cancelImg': '../uploadify/cancel.png',
        'auto': 'true',
        'multi': true,
        'folder': './uploads',
        'buttonText': 'Browse',
        'wmode': 'transparent',
        'sizeLimit': '10000000',
        'scriptData': {'section': "<?php echo $section; ?>"},
        'fileDataName': 'file',
        'onComplete': function(a,b,c,d,e){
            $.post( 'list_reference_materials.php',
                { section: "<?php echo $section; ?>" },
                function( data ) {
                    $('div#current').html(data);
                }
            )
        },
        'onError': function( a, b, c, d ){
            if( d.info == 404 )
                alert( 'Can not find upload script' );
            else
                alert( 'error ' + d.type + ": " + d.info );
        }
    })
})
</script>

<?php
} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>