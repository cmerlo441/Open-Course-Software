<?php

$title_stub = 'Contact Information Editor';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
?>
<h2>Current Contact Information</h2>
<div id="current_contact_info"></div>
    
<h2>Add New Information</h2>
<div id="add_contact_info">
    <table>
        <tr>
            <td>DB Key:</td>
            <td><input type="text" id="type" /></td>
        </tr>

        <tr>
            <td>Description:</td>
            <td><input type="text" id="description" /></td>
        </tr>

        <tr>
            <td>Contact Information:</td>
            <td><textarea id="contact_info" rows="5" cols="40"></textarea></td>
        </tr>

        <tr>
            <td>Sequence:</td>
            <td><input type="text" id="sequence" /></td>
        </tr>
        
        <tr>
            <td colspan="2" align="center">
                <input type="submit" value="Add This Information" id="add" />
            </td>
        </tr>
    </table>
</div>  <!-- div#add_contact_info -->
    
<script type="text/javascript">
$(document).ready(function(){
    $.post( 'list_contact_information.php',
        function(data) {
            $('div#current_contact_info').html(data);
        }
    )
    
    $('input:submit#add').click(function(){
        var type = $('input#type').val();
        var description = $('input#description').val();
        var contact_info = $('textarea#contact_info').val();
        var sequence = $('input#sequence').val();
        
        $.post( 'list_contact_information.php',
            {
                type: type,
                description: description,
                contact_info: contact_info,
                sequence: sequence
            },
            function(data){
                $('div#current_contact_info').html(data);
                $('input#type').val('');
                $('input#description').val('');
                $('textarea#contact_info').val('');
                $('input#sequence').val('');
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
include( "$fileroot/_footer.inc" );

?>
