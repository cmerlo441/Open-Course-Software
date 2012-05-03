<?php

require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $section = $db->real_escape_string( $_GET[ 'section' ] );

    $section_query = 'select c.dept, c.course, s.section '
    	. 'from courses as c, sections as s '
    	. 'where s.course = c.id '
    	. "and s.id = $section";
    $section_result = $db->query( $section_query );
    $section_row = $section_result->fetch_object( );
    $section_name = "$section_row->dept $section_row->course $section_row->section";
    
    $letter = '';
    $inactive = 0;
    
    print "<div data-role=\"header\" data-inset=\"true\">\n";
    print "<h1>$section_name Roster</h1>\n";
    print "</div>\n";

    $roster_query = 'select s.id, s.first, s.last, s.banner, x.active '
        . 'from students as s, student_x_section as x '
        . "where x.section = $section "
        . 'and x.student = s.id '
        . 'and s.verified = 1 '
        . 'order by x.active desc, s.last, s.first';
    $roster_result = $db->query( $roster_query );

    if( $roster_result->num_rows > 0 ) {
        print "<ul data-role=\"listview\" data-inset=\"true\">\n";
        while( $student = $roster_result->fetch_object( ) ){
            if( $student->active == 0 ) {
                if( $inactive == 0 ) {
                    print "<li data-role=\"list-divider\">Inactive Students</li>\n";
                    $inactive = 1;
                }
            } else if( ucfirst( substr( $student->last, 0, 1 ) ) != $letter ) {
                $letter = ucfirst( substr( $student->last, 0, 1 ) );
                print "<li data-role=\"list-divider\">$letter</li>\n";
                
            }
            print "<li id=\"$student->id\"";
            if( $student->active != 1 ) {
                print ' data-theme="a"';
            }
            print "><a href=\"student.php?student=$student->id&amp;section=$section\">"
                . ucwords( $student->first ) . ' ' . ucwords( $student->last )
                . "<p class=\"average ui-li-aside\" id=\"$student->id\">Average</p>"
                . "</a></li>\n";
        }
        print "</ul>\n";
    }

?>

<script type="text/javascript">

$('#the_page').live('pageinit',function(){
    
    var section = "<?php echo $section; ?>";

    $('p.average').each(function(){
        var student = $(this).attr('id');

        $.post( "<?php echo $main_site; ?>/admin/calculate_student_average.php",
            { section: section, student: student },
            function(data){
                $('p.average[id=' + student + ']').html(data);
            }
        )
    })

})

</script>

<?php

} else {
    print 'You are not authorized to view this page.';
}

require_once( '../_footer.inc' );
?>
