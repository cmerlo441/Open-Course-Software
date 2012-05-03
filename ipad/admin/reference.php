<?php

require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $id = $db->real_escape_string( $_GET[ 'id' ] );

    $ref_query = 'select * '
        . 'from reference '
        . "where id = $id";
    $ref_result = $db->query( $ref_query );
    if( $ref_result->num_rows == 1 ) {
        $ref = $ref_result->fetch_object( );
        
        $section_query = 'select c.dept, c.course, s.section '
            . 'from courses as c, sections as s '
            . "where s.course = c.id "
            . "and s.id = $ref->section";
        $section_result = $db->query( $section_query );
        $section = $section_result->fetch_object( );
        
        print "    <div data-role=\"header\" data-inset=\"true\">\n";
        print "        <h1>$ref->filename<br />\n";
        print "        ($section->dept $section->course $section->section)</h1>\n";
        print "    </div>\n\n";
        
        print "    <ul data-role=\"listview\" data-inset=\"true\">\n";
 
        print "    <li data-role=\"list-divider\">Availability</li>\n";
        print "    <li>";
        print "        <div data-role=\"fieldcontain\">\n";
        print "            <fieldset data-role=\"controlgroup\" data-type=\"horizontal\">\n";
        print "                <input type=\"radio\" name=\"available\" id=\"available\" value=\"avaiable\" ";
        if( $ref->available ) {
            print 'checked="checked" ';
        }
        print " />\n";
        print "                <label for=\"available\">Available</label>\n";
        
        print "                <input type=\"radio\" name=\"available\" id=\"hidden\" value=\"hidden\" ";
        if( ! $ref->available ) {
            print 'checked="checked" ';
        }
        print " />\n";
        print "                <label for=\"hidden\">Hidden</label>\n";
        print "            </fieldset>\n";
        print "        </div>\n";
        print "    </li>\n";
        
        print "    <li data-role=\"list-divider\">File Details</li>\n";
        print "    <li>$ref->size bytes</li>\n";
        print "    <li>Uploaded " . date( 'D, M j g:i a', strtotime( $ref->uploaded ) ) . "</li>\n";
        
        $downloads_query = 'select d.student, d.datetime, s.first, s.last '
            . 'from reference_downloads as d, students as s '
            . "where reference = $ref->id "
            . 'and d.student = s.id '
            . 'order by d.datetime';
        $downloads_result = $db->query( $downloads_query );
        print "    <li data-role=\"list-divider\">Student Downloads<span class=\"ui-li-count\">$downloads_result->num_rows</span></li>\n";
        while( $download = $downloads_result->fetch_object( ) ) {
            print "    <li><strong>" . ucwords("$download->first $download->last") . "</strong>\n";
            print "        <p class=\"ui-li-aside\">" . date( 'D, M j g:i a', strtotime( $download->datetime ) ) . "</p>\n";
            print "    </li>\n";
        }
        
        print "    </ul>\n";
        
        
        
    } else {
        print 'No such material exists.';
    }
    
} else {
    print 'You are not authorized to view this page.';
}

require_once( '../_footer.inc' );
?>
