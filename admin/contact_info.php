<?php

$title_stub = 'Edit Contact Information';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $info_query = 'select contact_info, last_updated from contact_info';
    $info_result = $db->query( $info_query );
    $info = 'Click here to edit your contact information.  Click outside the box when you\'re done.';
    if( $info_result->num_rows == 1 ) {
        $info_row = $info_result->fetch_object( );
        $info = nl2br( $info_row->contact_info );
        $last_update = date( 'l, F j, Y \a\t g:i a', strtotime( $info_row->last_updated ) );
    }
    
    print "<p>Enter or edit your contact information below.  You may want to use HTML."
        . "  <span id=\"last_update\">";
    if( isset( $last_update ) ) {
        print "You last updated your contact information on "
            . "$last_update.";
    }
    print "</span></p>\n";
    print "<div style=\"border: 1px solid white; padding: 1em;\">"
        . "<span class=\"editInPlace\" id=\"info\">$info</span></div>\n";
        
?>

<script type="text/javascript">
    
$(document).ready(function(){
    $("span#info").editInPlace({
        url: "update_contact_information.php",
        params: "ajax=yes",
        field_type: "textarea",
        textarea_rows: "5",
        textarea_cols: "60",
        saving_image: "<?php echo $docroot ?>/images/ajax-loader.gif"
    });
})
    
</script>

<?php

} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include ( "$fileroot/_footer.inc" );

?>