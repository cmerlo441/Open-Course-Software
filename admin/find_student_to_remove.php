<?php 

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    if( isset( $_POST[ 'remove' ] ) ) {
        $id = $db->real_escape_string( $_POST[ 'remove' ] );
        $x_query = 'select x.id '
            . 'from students as s, student_x_section as x '
            . 'where s.id = x.student '
            . "and s.banner = \"$id\"";
        $x_result = $db->query( $x_query );
        while( $row = $x_result->fetch_object( ) ) {
            $db->query( "delete from student_x_section where id = $row->id" );
        }
        $db->query( "delete from students where banner = \"$id\"" );
        print 'OK';
    }
    
    $id = $db->real_escape_string( $_POST[ 'id' ] );
    $student_query = 'select id, first, last, email, password from students '
        . "where banner = \"$id\"";
    $student_result = $db->query( $student_query );
    if( $student_result->num_rows == 0 ) {
        print 'Student not found.  Please try again.';
    } else {
        $student = $student_result->fetch_object( );
        print "<p>$student->first $student->last &lt;$student->email&gt;</p>";
        
        $sections_query = 'select s.id, c.dept, c.course, s.section '
            . 'from courses as c, sections as s, student_x_section as x '
            . 'where s.course = c.id '
            . 'and x.section = s.id '
            . "and x.student = $student->id "
            . 'order by dept, course';
        $sections_result = $db->query( $sections_query );
        while( $section = $sections_result->fetch_object( ) ) {
            $sections[ $section->id ][ 'course' ] =
                "$section->dept $section->course $section->section";
        }

        print "<p>";            
        foreach( $sections as $section ) {
            print "{$section[ 'course' ]}<br />\n";
        }
        print "</p>\n";
        
        /*
         * See whether this student has set a password.  If so, that means that
         * e-mail is working, and the student can log in, and so the problem
         * is likely something else, and might even be located between
         * the chair and the keyboard.
         */
        
        if( $student->password != 'Fake Password' ) {
            print "<div style=\"font-weight: bold; font-size: 1.5em;\">Password Has Been Set</div>\n";
            print "<p>Be aware that this student has already created a password, "
                . "which means that the e-mail address he or she provided is "
                . "completely functional.  You may not want to delete this "
                . "student's information.</p>\n";
        }
        
        print "<p><input type=\"submit\" id=\"remove\" "
            . "value=\"Remove $student->first $student->last\" /></p>\n";

    }
?>

<script type="text/javascript">
    
$(document).ready(function(){
    $('input#remove').click(function(){
        var id = "<?php echo $id; ?>";
        
        $.post('find_student_to_remove.php',
            { remove: id },
            function(data){
                $('input#banner').val('');
                $('div#student_to_remove').fadeOut();
            }
        )
    })
})
    
</script>

<?php

}

?>