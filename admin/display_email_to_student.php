<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $student = $db->real_escape_string( $_POST[ 'student' ] );
    $section = $db->real_escape_string( $_POST[ 'section' ] );
    $id = $db->real_escape_string( $_POST[ 'id' ] );

    $student_query = 'select first, middle, last, email from students '
	. "where id = $student";
    $student_result = $db->query( $student_query );
    $student_row = $student_result->fetch_assoc( );
    $student_name = name( $student_row );
    $student_email = $student_row[ 'email' ];

    $email_query = 'select * from mail_to_students '
	. "where id = $id";
    $email_result = $db->query( $email_query );
    $email = $email_result->fetch_assoc( );

    print "From: {$prof[ 'name' ]} &lt;{$prof[ 'email' ]}&gt;<br />\n";
    print "To: $student_name &lt;{$student_email}&gt;<br />\n";
    print "Date: " . date( 'l, F j, Y g:i a', strtotime( $email[ 'datetime' ] ) ) . "<br />\n";
    print "Subject: {$email[ 'subject' ]}<br /><br />\n\n";

    print nl2br( $email[ 'message' ] );

?>

<script type="text/javascript">
$(document).ready(function(){

    var student = "<?php echo $student; ?>";
    var section = "<?php echo $section; ?>";

    $('div#sent_email_dialog').dialog( 'option', 'buttons', {
        'OK': function(){
	    $('div#sent_email_dialog').dialog('destroy');
	},
	"<-- Go Back To List": function(){
	    $.post('list_email_to_student.php',
	        { student: student, section: section },
                function( data ) {
		    $('div#sent_email_dialog').html(data).dialog( 'option', 'buttons', {
		        'OK': function(){
	                    $('div#sent_email_dialog').dialog('destroy');
	                }
		    }).dialog( 'option', 'position', 'center' );
	        }
	    )
	}
    })
})
</script>

<?php

}

?>
