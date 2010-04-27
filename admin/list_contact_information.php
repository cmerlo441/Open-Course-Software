<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    if( isset( $_POST[ 'type' ] ) and isset( $_POST[ 'description' ] )
        and isset( $_POST[ 'contact_info' ] ) and isset( $_POST[ 'sequence' ] ) ) {
        $type = $db->real_escape_string( trim( $_POST[ 'type' ] ) );
        $description = $db->real_escape_string( trim( $_POST[ 'description' ] ) );
        $contact_info = $db->real_escape_string( trim( $_POST[ 'contact_info' ] ) );
        $sequence = $db->real_escape_string( trim( $_POST[ 'sequence' ] ) );
        
        $insert_query = 'insert into contact_information '
            . '(id, type, description, contact_info, sequence ) '
            . "values( null, \"$type\", \"$description\", \"$contact_info\", \"$sequence\" )";
        $insert_result = $db->query( $insert_query );
    }
    
    $info_query = 'select * from contact_information order by sequence';
    $info_result = $db->query( $info_query );
    while( $row = $info_result->fetch_assoc( ) ) {
        print "<h3>{$row[ 'description' ]}</h3>\n";
        print "<table>\n";
        print "<tr id=\"type\">\n";
        print "<td>DB Key:</td>\n";
        print "<td><span id=\"type\">{$row[ 'type' ]}</span></td>\n";
        print "</tr>\n\n";
        
        print "<tr id=\"description\">\n";
        print "<td>Description:</td>\n";
        print "<td><span id=\"description\">{$row[ 'description' ]}</span></td>\n";
        print "</tr>\n\n";
        
        print "<tr id=\"contact_info\">\n";
        print "<td>Contact Information:</td>\n";
        print "<td><span id=\"contact_info\">{$row[ 'contact_info' ]}</span></td>\n";
        print "</tr>\n\n";
        
        print "<tr id=\"sequence\">\n";
        print "<td>Sequence:</td>\n";
        print "<td><span id=\"sequence\">{$row[ 'sequence' ]}</span></td>\n";
        print "</tr>\n\n";
        print "</table>\n";
    }
?>

<script type="text/javascript">
$(document).ready(function(){
    $('span#type').editInPlace({
        url: 'update_contact_information.php',
        params: 'ajax=yes&column=type',
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })

    $('span#description').editInPlace({
        url: 'update_contact_information.php',
        params: 'ajax=yes&column=description',
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })

    $('span#contact_info').editInPlace({
        url: 'update_contact_information.php',
        params: 'ajax=yes&column=contact_info',
		field_type: "textarea",
		textarea_rows: "5",
		textarea_cols: "40",
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })

    $('span#sequence').editInPlace({
        url: 'update_contact_information.php',
        params: 'ajax=yes&column=sequence',
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })

})
</script>

<?php
}
   
?>
