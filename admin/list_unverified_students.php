<?php

$no_header = 1;
require_once( "../_header.inc" );

if( $_SESSION[ 'admin' ] == 1 ) {

    if( isset( $_POST[ 'verify' ] ) ) {

        $id = $db->real_escape_string( $_POST[ 'verify' ] );
        
        // Verify student in the database
        
        $verify_query = 'update students set verified = 1 '
            . "where id = $id";
        $verify_result = $db->query( $verify_query );

        // Activate student in all relevant sections 

        $section_query = 'update student_x_section set active = 1 '
            . "where student = $id";
        $section_result = $db->query( $section_query );
        
        // Send verification e-mail
        $student_query = 'select first, middle, last, email '
            . 'from students '
            . "where id = $id";
        $student_result = $db->query( $student_query );
        $student = $student_result->fetch_assoc( );
        $name = name( $student );
        $message = wordwrap( "Hello, {$student[ 'first' ]}.  "
            . "Your account has been finalized, and you can now log in.  "
            . "Just visit $url and click on \"Log in now\".  Have a great "
            . "semester!" );

        $headers = "From: {$prof[ 'name' ]} <{$prof[ 'email' ]}>\n";

        mail( "$name <{$student[ 'email' ]}>",
	      'Your Account Has Been Finalized',
	      $message, $headers );

        // Remove any unfinished accounts with same Banner ID

        $banner_query = 'select banner from students '
            . "where id = $id";
        $banner_result = $db->query( $banner_query );
        $banner_row = $banner_result->fetch_assoc( );
        $banner = $banner_row[ 'banner' ];
        $others_query = 'select id from students '
            . "where banner = \"$banner\" and id != $id";
        $others_result = $db->query( $others_query );
        while( $other_id = $others_result->fetch_assoc( ) ) {
            $remove_query = 'delete from student_x_section '
                . "where student = {$other_id[ 'id' ]}";
            $remove_result = $db->query( $remove_query );
            $db->query( "delete from students where id = {$other_id[ 'id' ]}" );
        }

?>

<script type="text/javascript">
$(document).ready(function(){
    $.pnotify({
        pnotify_title: 'Student Added',
        pnotify_text: "Student <?php echo $name; ?> has been added to the database.",
        pnotify_shadow: true
    });
})
</script>

<?php

    }
    
    else if( isset( $_POST[ 'deny' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'deny' ] );
        
        // Get e-mail address from DB
        
        $email_query = 'select first, last, email from students '
            . "where id = $id";
        $email_result = $db->query( $email_query );
        $email_row = $email_result->fetch_assoc( );
        $email = $email_row[ 'email' ];
        $first = $email_row[ 'first' ];
		$name = name( $email_row );
        
        // Send denied e-mail
        
        $message = wordwrap( "Hello, $first.  I am sorry to inform you that "
                             . "your request for an account on my website has "
                             . "been denied.  This is usually due to selecting "
                             . "the wrong section, or trying to create a "
                             . "duplicate account.  Feel free to reply to this "
                             . "email if you need more information." );                             

        $headers = "From: {$prof[ 'name' ]} <{$prof[ 'email' ]}>\n";

        mail( "$name <{$email}>", 'Your Account Request Has Been Denied',
            $message, $headers );

        // Remove entries from student_x_section
        
        $x_query = 'delete from student_x_section '
            . "where student = $id";
        $x_result = $db->query( $x_query );
        
        // Remove student from students
        
        $student_query = 'delete from students '
            . "where id = $id";
        $student_result = $db->query( $student_query );
        

?>

<script type="text/javascript">
$(document).ready(function(){
    $.pnotify({
        pnotify_title: 'Student Denied',
        pnotify_text: "<?php echo $name; ?> was not added to the database.",
        pnotify_shadow: true
    });
})
</script>

<?php

    }

	$sections_query = 'select c.dept, c.course, s.section, s.id '
		. 'from courses as c, sections as s '
		. 'where s.course = c.id '
		. 'order by c.dept, c.course, s.section';
	$sections_result = $db->query( $sections_query );
	while( $section_row = $sections_result->fetch_object( ) ) {
    
		$student_query = 'select s.id, s.first, s.middle, s.last, s.email, s.banner, s.password '
			. 'from students as s, student_x_section as x '
			. 'where x.student = s.id '
			. "and x.section = $section_row->id "
			. 'and s.verified = 0 and password != "Fake Password" '
			. 'order by s.last, s.first, s.middle';
	
/*
	    $student_query = 'select * from students '
	        . 'where verified = 0 and password != "Fake Password" '
	        . 'order by last, first, middle';
 */
	    $student_result = $db->query( $student_query );
	    $count = $student_result->num_rows;
	    if( $count > 0 ) {
			print "<h2>$section_row->dept $section_row->course $section_row->section</h2>\n";

	        print "<ul class=\"unverified_students\">\n";
	        while( $row = $student_result->fetch_assoc( ) ) {
	            $name = ucwords( $row[ 'first' ] ) . ' ';
	            if( $row[ 'middle' ] != '' ) {
	                $name .= ucwords( $row[ 'middle' ] ) . ' ';
	            }
	            $name .= ucwords( $row[ 'last' ] );
	            print "<li id=\"{$row[ 'id' ]}\">\n";
	            
	            print "<a href=\"javascript:void(0)\" class=\"verify\" id=\"{$row[ 'id' ]}\">"
	                . "<img src=\"$docroot/images/silk_icons/accept.png\" height=\"16\" "
	                . "width=\"16\" title=\"Verify $name\" /></a>\n";
	            print "<a href=\"javascript:void(0)\" class=\"deny\" id=\"{$row[ 'id' ]}\">"
	                . "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" "
	                . "width=\"16\" title=\"Deny $name\" /></a>\n";
	            print "{$row[ 'banner' ]}: $name ";
	            
	            print "</li>\n";
	        }
	        print "</ul>\n";
	    }
    }
    
?>

<script type="text/javascript">
$(document).ready(function(){
    $('a.verify').click(function(){
        var id = $(this).attr('id');
        $.post( 'list_unverified_students.php',
            { verify: id },
            function(data) {
                $('div#list_unverified_students').html(data);
                $.post( 'count_unverified_students.php',
                    function(data){
                        if (data != '') {
                            $('div#unverified').html(data);
                        } else {
                            $('div#unverified').slideUp(500);
                        }
                    }
                )
            }
        )
    })

    $('a.deny').click(function(){
        var id = $(this).attr('id');
        $.post( 'list_unverified_students.php',
            { deny: id },
            function(data) {
                $('div#list_unverified_students').html(data);
                $.post( 'count_unverified_students.php',
                    function(data){
                        if (data != '') {
                            $('div#unverified').html(data);
                        } else {
                            $('div#unverified').slideUp(500);
                        }
                    }
                )
            }
        )
    })
})
</script>

<?php

}
   
?>
