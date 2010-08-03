<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $student = $db->real_escape_string( $_POST[ 'student' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );

    $student_query = 'select first, middle, last from students '
	. "where id = $student";
    $student_result = $db->query( $student_query );
    $student_row = $student_result->fetch_assoc( );
    $first = $student_row[ 'first' ];
    $middle = $student_row[ 'middle' ];
    $last = $student_row[ 'last' ];

    $student_mail_query = 'select m.id, m.subject, m.message, m.sent_time as datetime '
	. 'from mail_from_students as m, student_x_section as x '
	. "where x.student = $student "
	. "and x.section = $section "
	. "and x.id = m.student_x_section "
	. 'order by datetime desc, subject';
    $student_mail_result = $db->query( $student_mail_query );

    if( $student_mail_result->num_rows == 0 ) {
	print "You have not received any e-mail from $first.";
    } else {
	print "<div id=\"body\">\n";
	print "<ul>\n";
	while( $mail_row = $student_mail_result->fetch_assoc( ) ) {
	    $subject = $mail_row[ 'subject' ];
	    if( substr( trim( $subject ), -1 ) == ':' ) {
		$subject .= ' (No Subject)';
	    }
	    print "<li><a href=\"javascript:void(0)\" "
		. "class=\"open_student_message\" id=\"{$mail_row[ 'id' ]}\">"
		. "<b>$subject</b></a> ";
	    print date( 'D, M jS g:i a',
			strtotime( $mail_row[ 'datetime' ] ) );
	    print "</li>\n";
	}
	print "</ul>\n";
	print "</div>  <!-- div#body -->\n";
    }

?>

<script type="text/javascript">
$(document).ready(function(){

    var student = "<?php echo $student; ?>";
    var section = "<?php echo $section; ?>";

    $('a.open_student_message').click(function(){

        var id = $(this).attr('id');

        $.post('display_email_from_student.php',
	    { student: student, section: section, id: id },
	    function(data){
	        $('div#body').html( data );
		$('div#received_email_dialog').dialog( 'option', 'position', 'center' );
	    }
	)
    })
})
</script>

<?php
 }

?>
