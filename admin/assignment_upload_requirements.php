<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $a = $db->real_escape_string( $_POST[ 'assignment' ] );
    
    if( isset( $_POST[ 'filename' ] ) ) {
        $filename = $db->real_escape_string( $_POST[ 'filename' ] );
        $insert_query = 'insert into assignment_upload_requirements '
            . '( id, assignment, filename ) values '
            . "( null, $a, \"$filename\" )";
        $insert_result = $db->query( $insert_query );
    }
    
    else if( isset( $_POST[ 'remove' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'remove' ] );
        $remove_query = 'delete from assignment_upload_requirements '
            . "where assignment = $a and id = $id";
        $remove_result = $db->query( $remove_query );
    }
    
    $requirements_query = 'select * from assignment_upload_requirements '
        . "where assignment = $a "
        . 'order by filename';
    $requirements_result = $db->query( $requirements_query );
    if( $requirements_result->num_rows == 0 ) {
        print 'None.';
    } else {
        print "<ul id=\"required_uploads\">\n";
        while( $row = $requirements_result->fetch_assoc( ) ) {
            print "<li>";
            print "<a href=\"javascript:void(0)\" class=\"remove_req\" "
                . "id=\"{$row[ 'id' ]}\">";
            print "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" "
                . "width=\"16\" title=\"Remove requirement for {$row[ 'filename' ]}\" /></a>\n";
            print "{$row[ 'filename' ]}</li>\n";
        }
        print "</ul>\n";
    }
    
    print "<p>Add an upload requirement: <input type=\"text\" id=\"filename\" />"
        . "<input type=\"submit\" id=\"add\" value=\"Add\" /></p>\n";
?>

<script type="text/javascript">
$(document).ready(function(){
    var assignment = "<?php echo $_POST[ 'assignment' ]; ?>";
    
    $('input#add').click(function(){
        var filename = $('input#filename').val();
        
        if( filename != '' ) {
            $.post( 'assignment_upload_requirements.php',
                { assignment: assignment, filename: filename },
                function( data ) {
                    $('div#upload_requirements').html(data);
                }
            )
        }
    })
    
    $('a.remove_req').click(function(){
        var id = $(this).attr('id');
        $.post( 'assignment_upload_requirements.php',
            { assignment: assignment, remove: id },
            function(data){
                $('div#upload_requirements').html(data);
            }
        )
    })
})
</script>

<?php
}
   
?>
