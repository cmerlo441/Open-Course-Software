<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    if( isset( $_POST[ 'delete' ] ) ) {
	$id = $db->real_escape_string( $_POST[ 'delete' ] );
	$db->query( "delete from letter_grades where id = \"$id\"" );
    } else {

	$letter = $db->real_escape_string( $_POST[ 'letter' ] );
	$number = $db->real_escape_string( $_POST[ 'number' ] );
	$query = 'insert into letter_grades( id, letter, grade ) '
	    . "values( null, \"$letter\", \"$number\" )";
	$result = $db->query( $query );
    }

    // Reprint the table
    $grades_query = 'select * from letter_grades '
	. 'order by grade desc';
    $grades_result = $db->query( $grades_query );
    print "<table class=\"tablesorter\" id=\"letter_grades\">\n";
    print "<thead>\n";
    print "  <tr>\n";
    print "    <th>Letter Grade</th>\n";
    print "    <th>Numerical Grade</th>\n";
    print "    <th>Actions</th>\n";
    print "  </tr>\n";
    print "</thead>\n\n";
    
    print "<tbody>\n";
    while( $grade_row = $grades_result->fetch_assoc( ) ) {
        print "  <tr class=\"letter_grade\" id=\"{$grade_row[ 'id' ]}\">\n";
        print "    <td class=\"letter\">{$grade_row[ 'letter' ]}</td>\n";
        print "    <td class=\"grade\">{$grade_row[ 'grade' ]}</td>\n";
        print "    <td class=\"actions\">Edit | ";
        print "<a href=\"javascript:void(0)\" class=\"delete_letter_grade\" id=\"{$grade_row[ 'id' ]}\" >"
            . "<img src=\"$docroot/images/cancel_16.png\" height=\"16\" width=\"16\" title=\"Delete This Letter Grade\" /></a></td>\n";
        print "  </tr>\n";
    }
    print "</tbody>\n</table>\n";
}
?>
