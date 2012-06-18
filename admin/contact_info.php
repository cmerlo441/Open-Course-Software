<?php

$title_stub = 'Edit Contact Information';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    print "<p>Enter or edit your contact information below.  You may want to use HTML.  "
        . "<span id=\"last_update\">";

    $info_query = 'select contact_info, last_updated from contact_info';
    $info_result = $db->query( $info_query );
    $info = 'Click here to edit your contact information.  Click outside the box when you\'re done.';
    if( $info_result->num_rows == 1 ) {
        $info_row = $info_result->fetch_object( );
        $info = $info_row->contact_info;
        $last_update = date( 'l, F j, Y \a\t g:i a', strtotime( $info_row->last_updated ) );
        print "You last updated your contact information on $last_update.";
    }
    print "</span></p>\n\n";
    
    print "<div id=\"edit\"><textarea id=\"contact_info\" rows=\"10\" cols=\"60\">$info</textarea><br />\n";
    print "<div style=\"padding: 0.5em; text-align: center;\">\n";
    print "<input type=\"submit\" disabled=\"disabled\" value=\"Save Changes\" id=\"save\">\n";
    print "<input type=\"submit\" disabled=\"disabled\" value=\"Cancel Changes\" id=\"cancel\">\n";
    print "</div></div>\n";
        
?>

<script type="text/javascript">
    
$(document).ready(function(){
    $('textarea#contact_info').markItUp(mySettings)
        .keyup( function( ) {
            $('input#save').removeAttr( 'disabled' );
            $('input#cancel').removeAttr( 'disabled' );
        });
    
    $('input#save').click(function(){
        var info = $('textarea#contact_info').val();
        $.post('update_contact_information.php',
            { update_value: info, original_html: '' },
            function(data){
                $('input#save').attr('disabled','disabled');
                $('input#cancel').attr('disabled','disabled');
            }
        )
    })
})
    
</script>

<?php

} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include ( "$fileroot/_footer.inc" );

?>