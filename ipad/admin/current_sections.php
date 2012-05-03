<?php

require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $sections_query = 'select s.id, c.dept, c.course, s.section '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . 'order by c.dept, c.course, s.section';
    $sections_result = $db->query( $sections_query );
    
    while( $section = $sections_result->fetch_object( ) ) {
?>

        <div data-role="collapsible" data-collapsed="true" id="<?php echo $section->id; ?>">
        <h1><?php echo "$section->dept $section->course $section->section"; ?></h1>
    
        <ul data-role="listview" data-inset="true">
        <li class="link"><a href="roster.php?section=<?php echo $section->id; ?>">Class Roster</a></li>
        <li class="link"><a href="assignments.php?section=<?php echo $section->id; ?>">Assignments &amp; Grades</a></li>
        <li class="link"><a href="reference_materials.php?section=<?php echo $section->id; ?>">Reference Materials</a></li>
        <li class="link"><a href="attendance.php?section=<?php echo $section->id; ?>">Attendance</a></li>

        </ul>
        
        </div>  <!-- collapsible -->
        
<?php
    }

} else {
    print 'You are not authorized to view this page.';
}

require_once( '../_footer.inc' );
?>
