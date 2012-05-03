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
    
    print "<div data-role=\"header\" data-inset=\"true\">\n";
    print "<h1>$section_name Reference Materials</h1>\n";
    print "</div>\n";
    $reference_query = 'select * from reference '
        . "where section = $section "
        . 'order by available desc, uploaded, filename';
    $reference_result = $db->query( $reference_query );

    if( $reference_result->num_rows == 0 ) {
        print 'You have not uploaded any reference materials for this section.';
    } else {
        print "<ul data-role=\"listview\" data-theme='c' data-inset=\"true\">\n";
        print "<li data-role=\"list-divider\">Available Reference Materials</li>\n";
        $first_unavail = 0;
        while( $r = $reference_result->fetch_object( ) ) {
            if( $first_unavail == 0 and $r->available == 0 ) {
                $first_unavail = 1;
                print "  <li data-role=\"list-divider\">Hidden Reference Materials</li>\n";
            }
            print "  <li><a href=\"reference.php?id=$r->id\">\n"
                . "    <p><strong>$r->filename</strong>\n"
                . "    <span class=\"ui-li-count\">" . number_format( $r->size / 1024, 0 ) . " KB</p>";
            print "  </a></li>\n\n";
        }
        print "</ul>\n";
    }
    
} else {
    print 'You are not authorized to view this page.';
}

require_once( '../_footer.inc' );
?>
