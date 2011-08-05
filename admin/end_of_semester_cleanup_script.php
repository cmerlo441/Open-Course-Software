<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $tables = 'assignments,'
	. 'assignment_documents,'
	. 'assignment_submissions,'
    . 'assignment_uploads,'
	. 'assignment_upload_requirements,'
	. 'attendance,'
	. 'curves,'
	. 'drop_lowest,'
    . 'grades,'
	. 'grade_events,'
	. 'holidays,'
	. 'last_login_growled,'
	. 'logins,'
	. 'mail_from_students,'
	. 'mail_to_classes,'
    . 'mail_to_students,'
	. 'office_hours,'
	. 'page_views,'
	. 'password_reset_request,'
	. 'reference,'
	. 'reference_downloads,'
	. 'rescheduled_days,'
	. 'sections,'
	. 'section_meetings,'
	// . 'shoutbox,'
    . 'students,'
	. 'student_x_section,'
	. 'student_x_verification,'
	. 'submission_comments';
    foreach( explode( ',', $tables ) as $table ) {
        print "Cleaning out table $table... ";
        $db->query( "truncate $table" );
        print "Done.<br />\n";
    }
    print "<p>Cleanup done.</p>\n";
}

?>
