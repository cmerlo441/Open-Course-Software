<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $student_id = $db->real_escape_string( $_POST[ 'student' ] );
    $section    = $db->real_escape_string( $_POST[ 'section' ] );

    $student_query = 'select s.id, s.first, s.middle, s.last, s.email, x.active '
        . 'from students as s, student_x_section as x '
        . "where s.id = $student_id "
        . 'and x.student = s.id '
        . "and x.section = $section";
    $student_result = $db->query( $student_query );
    $student_row = $student_result->fetch_assoc( );
    
    $student_name = $student_row[ 'first' ] . ' ';
    if( $student_row[ 'middle' ] != '' ) {
        $student_name .= $student_row[ 'middle' ] . ' ';
    }
    $student_name .= $student_row[ 'last' ];
    
    print "<h2>$student_name</h2>\n";
    
    print "<div id=\"student_details\">\n";
    
    print "<h3><a href=\"#\">E-Mail</a></h3>\n";
    print "<div id=\"email\">\n";

    print "<div id=\"address\">{$student_row[ 'first' ]}'s email address is "
        . $student_row[ 'email' ] . ".</div>\n";
    
    print "<div class=\"dialog\" id=\"send_email_dialog\" title=\"Send E-Mail\">\n";
    print "</div>  <!-- div#send_email_dialog -->\n";

    print "<div class=\"dialog\" id=\"sent_email_dialog\" "
	. "title=\"E-Mail You Sent To $student_name\">\n";
    print 'foo';
    print "</div>  <!-- div#sent_email_dialog -->\n";
    
    print "<div class=\"dialog\" id=\"received_email_dialog\" "
	. "title=\"E-Mail $student_name Sent You\">\n";
    print 'foo';
    print "</div>  <!-- div#received_email_dialog -->\n";
    
    print "<ul>\n";
    print "<li><a href=\"javascript:void(0)\" class=\"send_email\" id=\"$student_id\">Send {$student_row[ 'first' ]} an e-mail</a></li>\n";

    $sent_mail_query = 'select m.id '
	. 'from mail_to_students as m, student_x_section as x '
	. "where x.student = $student_id "
	. "and x.section = $section "
	. 'and m.student_x_section = x.id';
    $sent_mail_result = $db->query( $sent_mail_query );
    if( $sent_mail_result->num_rows > 0 ) {
	print "<li><a href=\"javascript:void(0)\" class=\"sent_email\" "
	    . "id=\"$student_id\">Read e-mail you have sent to "
	    . "{$student_row[ 'first' ]}</a></li>\n";
    } else {
	print "<li>You have not sent any e-mail to {$student_row[ 'first' ]}."
	    . "</li>\n";
    }

    $received_mail_query = 'select m.id '
	. 'from mail_from_students as m, student_x_section as x '
	. "where x.student = $student_id "
	. "and x.section = $section "
	. 'and m.student_x_section = x.id';
    $received_mail_result = $db->query( $received_mail_query );
    if( $received_mail_result->num_rows > 0 ) {
	print "<li><a href=\"javascript:void(0)\" class=\"received_email\" "
	    . "id=\"$student_id\">Read e-mail {$student_row[ 'first' ]} has "
	    . "sent you</a></li>\n";
    } else {
	print "<li>{$student_row[ 'first' ]} has not sent you any e-mail.</li>\n";
    }
    
    print "</ul>\n";
    print "</div>  <!-- div#email -->\n";
    
    print "<h3><a href=\"#\">Grades</a></h3>\n";
    print "<div id=\"grades\">\n";
    print "<div id=\"average\">Average: <span id=\"average\"></span></div>\n";
    print "<div id=\"grade_list\"></div>\n";
    print "</div>  <!-- div#grades -->\n";
    
    print "<h3><a href=\"#\">Attendance</a></h3>\n";
    print "<div id=\"attendance\">\n";
    $present_query = 'select count( a.id ) as p '
        . 'from attendance as a, attendance_types as t '
        . "where a.student = $student_id "
        . "and a.section = $section "
        . 'and a.presence = t.id '
        . 'and t.type = "present"';
    $present_result = $db->query( $present_query );
    $present_row = $present_result->fetch_assoc( );
    $present = $present_row[ 'p' ];
    
    $absent_query = 'select count( a.id ) as a '
        . 'from attendance as a, attendance_types as t '
        . "where a.student = $student_id "
        . "and a.section = $section "
        . 'and a.presence = t.id '
        . 'and t.type = "absent"';
    $absent_result = $db->query( $absent_query );
    $absent_row = $absent_result->fetch_assoc( );
    $absent = $absent_row[ 'a' ];
    
    $excused_query = 'select count( a.id ) as e '
        . 'from attendance as a, attendance_types as t '
        . "where a.student = $student_id "
        . "and a.section = $section "
        . 'and a.presence = t.id '
        . 'and t.type = "excused"';
    $excused_result = $db->query( $excused_query );
    $excused_row = $excused_result->fetch_assoc( );
    $excused = $excused_row[ 'e' ];

    print "<div id=\"attendance_summary\">\n";
    print "{$student_row[ 'first' ]} has attended $present class meeting"
        . ( $present == 1 ? '' : 's' ) . ".<br />\n";
    print "<span id=\"absent\">{$student_row[ 'first' ]} has been absent from $absent meeting"
        . ( $absent == 1 ? '' : 's' ) . ".</span><br />\n";
    print "{$student_row[ 'first' ]} has $excused excused absence"
        . ( $excused == 1 ? '' : 's' ) . ".\n";
    print "</div>\n";
    
    $dates_query = 'select start, end from semester';
    $dates_result = $db->query( $dates_query );
    $dates_row = $dates_result->fetch_assoc( );
    $month = date( 'n', strtotime( $dates_row[ 'start' ] ) );
    $end = date( 'n', strtotime( $dates_row[ 'end' ] ) );
    if( date( 'Y-m-d' ) < $dates_row[ 'end' ] ) {
        $end = date( 'n' );
    }
    
    while( $month <= $end ) {
        $attendance = array( );
        $attendance_query = 'select a.date, t.type '
            . 'from attendance as a, attendance_types as t '
            . "where a.student = $student_id "
            . "and a.section = $section "
            . "and a.date >= \""
            . date( 'Y-m-d',
                    strtotime( date( 'Y' ) . '-' . $month . '-01' ) )
            . '" '
            . 'and a.date <= "'
            . date( 'Y-m-t', strtotime( date( 'Y' ) . '-' . $month . '-01' ) )
            . '" '
            . 'and a.presence = t.id '
            . 'order by a.date';
        $attendance_result = $db->query( $attendance_query );
        while( $attendance_row = $attendance_result->fetch_assoc( ) ) {
            $attendance[ date( 'j', strtotime( $attendance_row[ 'date' ] ) ) ] =
                $attendance_row[ 'type' ];
        }
        
        $calendar = new Calendar( $month, date( 'Y' ), $docroot, $db, $attendance );
        $calendar->disp( );
        
        $month++;
    }
    
    print "</div>  <!-- div#attendance -->\n";
    
    print "<h3><a href=\"#\">Login History</a></h3>\n";
    print "<div id=\"login_history\">\n";
    $login_query = 'select l.datetime, l.address, l.browser, s.first, s.last '
        . 'from logins as l, students as s '
        . 'where l.student = s.id '
        . "and s.id = $student_id";
    $login_result = $db->query( $login_query );

?>

<table class="tablesorter" id="logins">
    <thead>
        <tr>
            <th>Time</th>
            <th>IP Address</th>
            <th>Browser</th>
            <th>OS</th>
        </tr>
    </thead>
    
    <tbody>
<?php
    if( $login_result->num_rows == 0 ) {
        print "<tr class=\"no_logins\"><td colspan=\"4\">No logins</td></tr>\n";
    } else {
        while( $login = $login_result->fetch_assoc( ) ) {
            print "        <tr>\n";
            print "            <td>" . date( 'm/d H:i', strtotime( $login[ 'datetime' ] ) ) . "</td>\n";
            print "            <td>{$login[ 'address' ]}</td>\n";
            print "            <td>" . browser( $login[ 'browser' ] ) . "</td>\n";
            print "            <td>" . os( $login[ 'browser' ] ) . "</td>\n";
            print "        </tr>\n";
        }
    }
?>
    </tbody>
</table>

<p><?php print_link( "$admin/page_views.php?student={$student_row[ 'id' ]}",
		  "<p>View {$student_row[ 'first' ]}'s page view history" ); ?>.</p>


</div>  <!-- div#login_history -->
</div>  <!-- div#student_details -->

<script type="text/javascript">
$(document).ready(function(){
    var student = "<?php echo $student_id; ?>";
    var student_name = "<?php echo $student_name; ?>";
    var section = "<?php echo $section; ?>";
    
    $('#logins').tablesorter({ sortList: [ [0,0] ], widgets: [ 'ocsw' ] });
    
    $('div#middle div#content table#logins tr.no_logins > td').css('background-color', '#444');
    $('div#middle div#content table#logins tr.no_logins > td').css('text-align', 'center');

    $('a.send_email').click(function(){
        
        var student_address = "<?php echo $student_name . ' <' . $student_row[ 'email' ] . '>'; ?>";
        var prof_address = "<?php echo $prof[ 'name' ] . ' <' . $prof[ 'email' ] . '>'; ?>";
        
        $.post( 'send_email_form.php',
            { student: student, section: section },
            function( data ) {
                $('div#send_email_dialog').html(data).dialog({
                    autoOpen: true,
                    hide: 'puff',
                    modal: true,
                    width: 500,
                    buttons: {
                        'Send': function(){
			    var subject = $('input#subject').val();
			    var message = $('textarea#message').val();
                            $.post( 'send_email.php',
                                {
                                    student_id: student,
                                    section: section,
                                    to: student_address,
                                    from: prof_address,
                                    subject: subject,
                                    message: message
				},
                                function( data ) {
				    var title;
				    var text;
				    var type;
				    if( data == "1" ) {
					title = 'E-Mail Sent';
					text = 'Your e-mail message "'
					    + message
					    + '" has been sent to '
					    + '<?php echo $student_name; ?>.';
					type = 'normal';
				    } else {
					title = 'Problem Sending E-Mail';
					text = 'Your e-mail message was not sent.';
					type = 'error';
				    }
				    $.pnotify({
				        pnotify_title: title,
					pnotify_text: text,
					pnotify_shadow: true,
					pnotify_type: type
				    })
				}
                            );
                            $('div#send_email_dialog').dialog('destroy');
                        },
                        'Cancel': function(){
                            $('div#send_email_dialog').dialog('destroy');
                        }
                    }
                })
            }
        )
    })

	$('a.sent_email').click(function(){
            $.post( 'list_email_to_student.php',
	    { student: student, section: section },
            function( data ) {
                $('div#sent_email_dialog').html(data).dialog({
		    title: "E-Mail You Sent to " + student_name,
		    autoOpen: true,
		    hide: 'puff',
		    modal: true,
		    position: 'center',
		    width: 700,
		    buttons: {
		        'OK': function(){
			    $('div#sent_email_dialog').dialog('destroy');
			}
		    }
		})
	    }
	    )
	})

	$('a.received_email').click(function(){
        $.post( 'list_email_from_student.php',
	    { student: student, section: section },
            function( data ) {
                $('div#received_email_dialog').html(data).dialog({
		    title: "E-Mail " + student_name + " Sent You",
		    autoOpen: true,
		    hide: 'puff',
		    modal: true,
		    position: 'center',
		    width: 700,
		    buttons: {
		        'OK': function(){
			    $('div#received_email_dialog').dialog('destroy');
			}
		    }
		})
	    }
	)
    })
    
    $.post( 'calculate_student_average.php',
        { section: section, student: student },
        function(data){
            $('span#average').html(data);
        }
    )
    
    $.post( 'student_grades.php',
        { section: section, student: student },
        function( data ) {
            $('div#grade_list').html(data);
        }
    )

    $('div#student_details').accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });

})
</script>

<?php
    
} else {
    print $no_admin;
}
?>