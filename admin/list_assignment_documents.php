<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    if( isset( $_POST[ 'id_to_delete' ] ) ) {
	$id = $db->real_escape_string( $_POST[ 'id_to_delete' ] );
        $delete_query = 'delete from assignment_documents '
            . "where id = $id";
        $delete_result = $db->query( $delete_query );
    }

    $assignment = $db->real_escape_string( $_POST[ 'assignment' ] );
    
    $files_query = 'select * from assignment_documents '
        . "where assignment = \"$assignment\" "
        . 'order by name';
    $files_result = $db->query( $files_query );
    if( $files_result->num_rows == 0 ) {
        print "None.";
    } else {
        print "<ul>\n";
        while( $file = $files_result->fetch_assoc( ) ) {
            print "<li><a href=\"javascript:void(0)\" class=\"delete\" id=\"{$file[ 'id' ]}\">"
                . "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" "
                . "width=\"16\" title=\"Remove {$file[ 'name' ]}\" /></a>\n";
            print "<a href=\"javascript:void(0)\" class=\"download\" id=\"{$file[ 'id' ]}\">"
                . "{$file[ 'name' ]} ({$file[ 'size' ]} bytes)</a></li>\n";
        }
        print "</ul>\n";
    }
?>
<script type="text/javascript">
$(document).ready(function(){
    $('a.delete').click(function(){
        var id = $(this).attr('id');
        $.post( 'list_assignment_documents.php',
            { id_to_delete: id, assignment: "<?php echo $assignment; ?>" },
            function( data ) {
                $('div#existing_files').html(data);
            }
        )
    })
    
    $('a.download').click(function(){
        var id = $(this).attr('id');
        window.location = "<?php echo $docroot; ?>/download_assignment_document.php?id=" + id;
    })
})
</script>
<?php
}
   
?>
