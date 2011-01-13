<?php

$title_stub = 'End-Of-Semester Clean Up';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
?>
<p>This script will delete all of the following from the database:</p>
<ul>
    <li>Assignments, including:
        <ul>
            <li>Homework</li>
            <li>Lab Assignments</li>
            <li>Projects</li>
            <li>All documents you uploaded with respect to these assignments</li>
        </ul>
    </li>
    <li>Grades</li>
    <li>Holidays and rescheduled days</li>
    <li>Login and page view history</li>
    <li>Sent and received e-mail</li>
    <li>Current sections</li>
    <li>All student information</li>
</ul>

<p><b>Make sure you know what you&apos;re doing</b> before you enable this script.</p>

<table>
    <tr>
        <td valign="top"><input type="checkbox" id="confirm" /></td>
            <td>I know what I&apos;m doing, and I want to delete all the
            information from the previous semester.</td>
    </tr>
</table>
<p style="text-align: center"><input type="submit" id="clean" /></p>

<div class="dialog" id="cleanup_dialog" title="Cleanup Results">
<img src="<?php echo $docroot; ?>/images/ajax-loader.gif" alt="Please wait" />
</div>

<script type="text/javascript">
$(document).ready(function(){
    $('input#clean').click(function(){
        var checked = ( $('input#confirm').attr('checked') == true );
        if( checked ) {
            $.post( 'end_of_semester_cleanup_script.php',
                { verified: 1 },
                function(data){
                    $('#cleanup_dialog').html(data).dialog({
                        autoOpen: true,
                        hide: 'puff',
                        modal: true,
                        width: '450px',
                        buttons: {
                            'OK': function(){
                                $(this).dialog('destroy');
                            }
                        }
                    })
                }
            )
        } else {
            $('#cleanup_dialog').html('Nothing cleaned up.  '
                + 'You did not check the checkbox.').dialog({
                autoOpen: true,
                hide: 'puff',
                modal: true,
                buttons: {
                    'OK': function(){
                        $(this).dialog('destroy');
                    }
                }
            })
        }
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
