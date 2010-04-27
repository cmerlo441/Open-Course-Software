<?php

$title_stub = 'Send Mail';
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    $section = $db->real_escape_string( $_GET[ 'section' ] );
    
    // Make sure this student is in this section
    $valid_query = 'select * from student_x_section '
        . "where student = {$_SESSION[ 'student' ]} "
        . "and section = $section";
    $valid_result = $db->query( $valid_query );
    if( $valid_result->num_rows == 0 ) {
        print 'You have chosen an invalid section.';
    } else {
        $student_name = $_SESSION[ 'first' ] . ' ';
        if( $_SESSION[ 'middle' ] != '' ) {
            $student_name .= $_SESSION[ 'middle' ] . ' ';
        }
        $student_name .= $_SESSION[ 'last' ];
    
        $section_query = 'select c.dept, c.course, s.section '
            . 'from courses as c, sections as s '
            . 'where s.course = c.id '
            . "and s.id = $section";
        $section_result = $db->query( $section_query );
        $section_row = $section_result->fetch_assoc( );
        $section_name = "{$section_row[ 'dept' ]} {$section_row[ 'course' ]} "
            . $section_row[ 'section' ];
    }
?>

<div id="mail_was_sent" class="success" style="border-bottom: 1px solid"></div>

<table>
    <tr id="from">
        <td>From:</td>
        <td><?php print $student_name . " &lt;" . $_SESSION[ 'email' ] . "&gt;"; ?></td>
    </tr>
    
    <tr id="reply-to">
        <td>Reply-to:</td>
        <td>
            <select id="replyto">
                <option value="external" selected><?php echo $_SESSION[ 'email' ]; ?></option>
                <option value="ncc"><?php echo strtolower( $_SESSION[ 'banner' ] ) . '@students.ncc.edu'; ?></option>
            </select>
        </td>
    
    <tr id="to">
        <td>To:</td>
        <td><?php print $prof[ 'name' ] . " &lt;" . $prof[ 'email' ] . "&gt;"; ?></td>
    </tr>
    
    <tr id="subject">
        <td>Subject:</td>
        <td><?php echo $section_name . ':'; ?>
        <input type="text" id="subject" /></td>
    </tr>
    
    <tr id="message">
        <td>Message:</td>
        <td><textarea id="message" cols="40" rows="5"></textarea></td>
    </tr>
    
    <tr id="button">
        <td colspan="2" align="center">
            <input type="submit" id="send" value="Send Mail" />
        </td>
    </tr>
</table>

<script type="text/javascript">
$(document).ready(function(){
    $('div#mail_was_sent').hide();
    
    $('input#subject').focus();
    
    $('input#send').click(function(){
        var student = "<?php echo $_SESSION[ 'student' ]; ?>";
        var section = "<?php echo $section; ?>";
        var section_name = "<?php echo $section_name; ?>";
        var replyto = $('select#replyto').val();
        var subject = $('input#subject').val();
        var message = $('textarea#message').val();
        
        $.post('send_student_mail.php',
            {
                student: student,
                section: section,
                section_name: section_name,
                replyto: replyto,
                subject: subject,
                message: message
            },
            function(data){
                $('input#subject').val('');
                $('textarea#message').val('');
                $('div#mail_was_sent').html(data).slideDown('1000');
            }
        )
    })
})
</script>

<?php
} else {
    print $no_student;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
require_once( '../_footer.inc' );

?>
