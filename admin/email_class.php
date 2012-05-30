<?php

$title_stub = 'Send E-Mail To A Class';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
?>

<table>
    <tr>
        <td>From:</td>
        <td><?php echo $prof[ 'name' ] . ' &lt;' . $prof[ 'email' ] . '&gt;' ?></td>
    </tr>

    <tr>
        <td>To:</td>
        <td>
<?php
    $section_query = 'select c.dept, c.course, s.id, s.section '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . 'order by c.dept, c.course, s.section';
    $section_result = $db->query( $section_query );
    while( $section_row = $section_result->fetch_assoc( ) ) {
        print "<input type=\"checkbox\" id=\"{$section_row[ 'id' ]}\" ";
        if( $section_result->num_rows == 1 )
            print 'checked="checked" ';
        print "/> " . $section_row[ 'dept' ] . ' ' . $section_row[ 'course' ] . ' '
            . $section_row[ 'section' ] . "<br />\n";
    }
?>
        </td>
    </tr>
    
    <tr>
        <td>Subject:</td>
        <td><input type="text" id="subject" /></td>
    </tr>
    
    <tr>
        <td>Message:</td>
        <td><textarea id="message" cols="40" rows="5"></textarea></td>
    </tr>
    
    <tr>
        <td colspan="2" align="center">
            <input type="submit" id="send" value="Send Mail" />
        </td>
    </tr>
</table>

<div class="dialog" id="section_error" title="Choose A Section"></div>

<script type="text/javascript">
$(document).ready(function(){
    $('input#send').click(function(){
        var sections = '';
        var subject = $('input#subject').val();
        var message = $('textarea#message').val();

        if ($(":checked").size() == 0) {
            $.post('section_error_dialog.php', function(data){
                $('div#section_error').html(data).dialog({
                    autoOpen: true,
                    hide: 'puff',
                    modal: true,
                    buttons: {
                        'OK': function(){
                            $(this).dialog('destroy');
                        }
                    }
                })
            })
        }
        else {
            $(':checked').each(function(){
                var section = $(this).attr('id');
                sections += section + ',';
            })
            $.post('send_class_email.php', {
                sections: sections,
                subject: subject,
                message: message
            }, function(data){
                $.pnotify({
                    pnotify_title: 'Your Mail Was Sent',
                    pnotify_text: data,
                    pnotify_shadow: true
                });
                $('input:checkbox').removeAttr('checked');
                $('input#subject').val('');
                $('textarea#message').val('');
            })
        }
    })
})
</script>

<?php
} else {
    print $no_admin;
}

?>