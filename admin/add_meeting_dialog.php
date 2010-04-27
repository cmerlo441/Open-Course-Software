<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
$days = array( "Monday" => 1,
               "Tuesday" => 2,
               "Wednesday" => 3,
               "Thursday" => 4,
               "Friday" => 5,
               "Saturday" => 6,
               "Sunday" => 7 );
    
    $id = $db->real_escape_string( $_POST[ 'id' ] );
    $section_query = 'select c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . "where s.course = c.id "
        . "and s.id = $id";
    $section_result = $db->query( $section_query );
    $section_row = $section_result->fetch_assoc( );
    $section = "{$section_row[ 'dept' ]} {$section_row[ 'course' ]} "
        . $section_row[ 'section' ];
    print "<p><b>Add Meeting For $section</b></p>\n";
    print "<table>\n";
    print "<tr>\n";
    print "<td>Day:</td><td><select id=\"day\">";
    foreach( $days as $day=>$key ) {
        print "<option value=\"$key\">$day</option>\n";
    }
    print "</select></td>\n";
    print "</tr>\n\n";
    
    print "<tr>\n";
    print "<td>Start time:</td>"
        . "<td><input type=\"text\" id=\"start\" size=\"10\" /></td>\n";
    print "</tr>\n";
    
    print "<tr>\n";
    print "<td>End time:</td>"
        . "<td><input type=\"text\" id=\"end\" size=\"10\" /></td>\n";
    print "</tr>\n";
    
    print "<tr>\n";
    print "<td colspan=\"2\" align=\"center\"><span style=\"font-size: x-small\">"
        . "You may enter times in a format like \"11:00 am\"</span></td>\n";
    print "</tr>\n";
    
    print "<tr>\n";
    print "<td>Building:</td>"
        . "<td><input type=\"text\" id=\"building\" size=\"10\" /></td>\n";
    print "</tr>\n";
    
    print "<tr>\n";
    print "<td>Room:</td>"
        . "<td><input type=\"text\" id=\"room\" size=\"10\" /></td>\n";
    print "</tr>\n";
    
    print "</table>\n";
}

?>
