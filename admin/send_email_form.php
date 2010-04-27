<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $student_query = 'select * from students '
        . 'where id = ' . $db->real_escape_string( $_POST[ 'student' ] );
    $student_result = $db->query( $student_query );
    $student = $student_result->fetch_assoc( );
    $student_name = $student[ 'first' ] . ' ';
    if( $student[ 'middle' ] != '' ) {
        $student_name .= $student[ 'middle' ] . ' ';
    }
    $student_name .= $student[ 'last' ];
    
    $section_query = 'select c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = {$_POST[ 'section' ]}";
    $section_result = $db->query( $section_query );
    $section_row = $section_result->fetch_assoc( );
    $section = $section_row[ 'dept' ] . ' ' . $section_row[ 'course' ]
        . ' ' . $section_row[ 'section' ];
    
    ?>
    
<table>
    <tr>
        <td>To:</td>
        <td id="to"><?php echo $student_name; ?> &lt;<?php echo $student[ 'email' ]; ?>&gt;</td>
    </tr>
    
    <tr>
        <td>From:</td>
        <td id="from"><?php echo $prof[ 'name' ]; ?> &lt;<?php echo $prof[ 'email' ]; ?>&gt;</td>
    </tr>
    
    <tr>
        <td>Subject:</td>
        <td><input type="text" id="subject" size="40" value = "<?php echo $section; ?>: "/></td>
    </tr>
    
    <tr>
        <td>Message:</td>
        <td><textarea id="message" rows="10" cols="40"></textarea></td>
    </tr>
</table>
    
    <?php
}
   
?>
