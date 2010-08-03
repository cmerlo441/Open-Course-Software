<?php

$title_stub = 'Reference Materials';
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    
    $section = $db->real_escape_string( $_GET[ 'section' ] );
    
    $course_query = 'select c.id as course_id, c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = $section";
    $course_result = $db->query( $course_query );
    $course_row = $course_result->fetch_assoc( );
    $course_name = $course_row[ 'dept' ] . ' ' . $course_row[ 'course' ] . ' '
        . $course_row[ 'section' ];
    
    $ref_query = 'select * from reference '
        . "where section = $section "
        . "and available = 1 "
        . 'order by filename';
    $ref_result = $db->query( $ref_query );
    
    if( $ref_result->num_rows == 0 ) {
        print 'None.';
    } else {
  
?>

<table id="reference" class="tablesorter">
    <thead>
        <tr>
            <th>Filename</th>
            <th>Size</th>
            <th>Upload Date</th>
        </tr>
    </thead>
    
    <tbody>
<?
        while( $ref = $ref_result->fetch_assoc( ) ) {
            print "        <tr id=\"{$ref[ 'id' ]}\">\n";
            print "            <td>{$ref[ 'filename' ]}</td>\n";
            print "            <td>{$ref[ 'size' ]} Bytes</td>\n";
            print "            <td>"
                . date( 'F j, Y g:i A', strtotime( $ref[ 'uploaded' ] ) ) . "</td>\n";
            print "        </tr>\n";
        }
?>
    </tbody>
</table>

<?
    }
?>

<script type="text/javascript">
$(document).ready(function(){
    var course = " :: <?php echo $course_name; ?>";
    
    $('h1').html( $('h1').html( ) + course );
    $(document).attr('title', $(document).attr('title') + course );
    $('#reference').tablesorter({
        headers: { 1: { sorter: 'digit' },
                   2: { sorter: 'date' } },
        sortList: [ [2,1], [0,0] ],
        widgets: [ 'ocsw', 'clickable_rows' ]
    });
    
    $('#reference tr').click(function(){
        var id = $(this).attr('id');
        
        window.open("../download_reference_material.php?id=" + id );
        return false;
    })
})
</script>

<?php
} else {
    print $no_student;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>