<?php
   
$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    $update_value = $db->real_escape_string( trim( $_POST[ 'update_value' ] ) );
    $original_html = $db->real_escape_string( trim( $_POST[ 'original_html' ] ) );
    
	if( $update_value != $original_html ) {
	    $rows_query = 'select * from contact_info';
        $rows_result = $db->query( $rows_query );
        if( $rows_result->num_rows == 1 ) {
    		$update_query = "update contact_info set contact_info = \"$update_value\", "
                . "last_updated = \"" . date( 'Y-m-d H:i:s' ) . "\"";
		    $update_result = $db->query( $update_query );
        } else {
            $insert_query = 'insert into contact_info ( contact_info, last_updated ) '
                . "values( \"$update_value\", \"" . date( 'Y-m-d H:i:s' ) . "\" )";
            $insert_result = $db->query( $insert_query );
        }
		if( $db->affected_rows == 1 ) {
			$new_data_query = "select contact_info, last_updated from contact_info";
			$new_data_result = $db->query( $new_data_query );
            $new_data_row = $new_data_result->fetch_object( );
            $new_data_result->close( );
			print $new_data_row->contact_info;
            $date = date( 'l, F j, Y \a\t g:i a', strtotime( $new_data_row->last_updated ) );
?>

<script type="text/javascript">
$(document).ready(function(){
    $('span#last_update').html("You last updated your contact information on " +
        "<?php echo $date; ?>." );
})  
</script>

<?php
		} else {
			print $original_html;
		}
	} else {
		print $original_html;
	}
}

?>
