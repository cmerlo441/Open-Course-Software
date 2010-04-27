<?php

$title_stub = 'Letter Grades';
require_once( '../_header.inc' );

if( isset( $_SESSION[ 'admin' ] ) ) {
    $grades_query = 'select * from letter_grades '
        . 'order by grade desc';
    $grades_result = $db->query( $grades_query );
    print "<table class=\"tablesorter\">\n";
    print "<thead>\n";
    print "  <tr>\n";
    print "    <th>Letter Grade</th>\n";
    print "    <th>Numerical Grade</th>\n";
    print "    <th>Actions</th>\n";
    print "  </tr>\n";
    print "</thead>\n\n";
    
    print "<tbody>\n";
    while( $grade_row = $grades_result->fetch_assoc( ) ) {
        print "  <tr class=\"letter_grade\" id=\"{$grade_row[ 'id' ]}\">";
        print "    <td class=\"letter\">{$grade_row[ 'letter' ]}</td>\n";
        print "    <td class=\"grade\">{$grade_row[ 'grade' ]}</td>\n";
        print "    <td class=\"actions\">Edit | ";
        print "<a href=\"javascript:void(0)\" class=\"delete_letter_grade\" id=\"{$grade_row[ 'id' ]}\" >"
            . "<img src=\"$docroot/images/cancel_16.png\" height=\"16\" width=\"16\" title=\"Delete This Letter Grade\" /></a></td>\n";
        print "  </tr>\n";
    }
    print "</tbody>\n</table>\n";
    
    print "<a href=\"javascript:void(0)\" class=\"add_letter_grade\">"
        . "<img src=\"$docroot/images/add_16.png\" height=\"16\" width=\"16\" title=\"Add New Letter Grade\" /></a> ";
    print "<a href=\"javascript:void(0)\" class=\"add_letter_grade\">Add New Letter Grade</a>\n";

?>
<script type="text/javascript">
   $(document).ready(function(){
       $("table.tablesorter").tablesorter( { sortList: [ [1,1] ] } );
     });
</script>
<?php

} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
