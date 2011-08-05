<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $courses_query = 'select id, dept, course, short_name as name '
        . 'from courses '
        . 'order by dept, course';
    $courses_result = $db->query( $courses_query );
    
    print "<table>\n";
    
    print "<tr>\n";
    print "<td>Course:</td>\n";
    print "<td><select id=\"course\">\n";
    print "<option id=\"0\">Choose a course</option>\n";
    while( $row = $courses_result->fetch_object( ) ) {
        print "<option id=\"$row->id\">$row->dept $row->course: $row->name</option>\n";
    }
    print "</select></td>\n";
    print "</tr>\n\n";
    
    print "<tr>\n";
    print "<td>Section:</td><td><input type=\"text\" id=\"section\" size=\"5\"></td>\n";
    print "</tr>\n\n";
    
    print "<tr>\n";
    print "<td>CRN:</td><td><input type=\"text\" id=\"crn\" size=\"8\"></td>\n";
    print "</tr>\n\n";
    
    print "<tr>\n";
    print "<td>When:</td><td>";
    print "<input type=\"radio\" name=\"when\" id=\"day\" checked><label style=\"padding: 0 1em 0 0.25em\" for=\"day\">Day</label>\n";
    print "<input type=\"radio\" name=\"when\" id=\"evening\"><label  style=\"padding: 0 1em 0 0.25em\"for=\"evening\">Evening</label>\n";
    
    print "</td>\n</tr>\n\n";
    
    print "</table>\n";
}

?>