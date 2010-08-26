<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $new_type        =
	trim( $db->real_escape_string( $_POST[ 'new_type' ] ) );
    $new_type_plural = 
	trim( $db->real_escape_string( $_POST[ 'new_type_plural' ] ) );
    
    if( $new_type != '' ) {
        $insert_query = 'insert into grade_types ( id, grade_type, plural ) '
            . "values ( null, \"$new_type\", \"$new_type_plural\" )";
        $insert_result = $db->query( $insert_query );
        $error = ( $db->affected_rows == 0 );
    }
    
    if( isset( $_POST[ 'id_to_remove' ] ) ) {
	$id_to_remove = 
	    trim( $db->real_escape_string( $_POST[ 'id_to_remove' ] ) );
        $delete_query = 'delete from grade_types '
            . "where id = $id_to_remove";
        $delete_result = $db->query( $delete_query );
    }
    
    $types_query = 'select * from grade_types order by grade_type';
    $types_result = $db->query( $types_query );
    if( $types_result->num_rows > 0 ) {
        print "<ul id=\"grade_types\">\n";
        while( $row = $types_result->fetch_assoc( ) ) {
            print "<li class=\"grade_type\" id=\"{$row[ 'id' ]}\">";
            print "<a href=\"javascript:void(0)\" class=\"remove_grade_type\" "
                . "id=\"{$row[ 'id' ]}\" title=\"Remove {$row[ 'grade_type' ]}\">"
                . "<img src=\"$docroot/images/silk_icons/cancel.png\" "
                . "height=\"16\" width=\"16\" /></a> ";
            print "{$row[ 'grade_type' ]} ({$row[ 'plural' ]})\n";
            print "</li>\n";
        }
        print "</ul>\n";
    } else {
        print "<p>None.</p>\n";
    }
?>

<script type="text/javascript">
$(document).ready(function(){
    $("a.remove_grade_type").click(function(){
        var id = $(this).attr("id");
        $.post( 'grade_types_list.php',
            { id_to_remove: id },
            function(data){
                $("div#current_grade_types").html(data);
            }
        )
    })
})
</script>

<?php
}
   
?>
