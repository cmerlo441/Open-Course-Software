<?php

$no_header = 1;
require_once( '_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
?>
<h2>Site Administrator</h2>
<div id="admin_top">
<p>You are logged in as <b><?php echo $prof[ 'name' ]; ?></b>.
<?php print_link( 'javascript:void(0)', 'Log out now.', 'logout', 'Log out now.' ); ?>
</p>
<p>
    <?php print_link( 'javascript:void(0)',
                      "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" width=\"16\" style=\"border: 0\" />",
                      'hide', 'Hide this pane.' ); ?>
    <?php print_link( 'javascript:void(0)', 'Hide this pane.', 'hide', 'Hide this pane.' ); ?>
</p>
</div>  <!-- div#admin_top -->

<div id="unverified"></div>

<div class="accordion" id="admin_tools">

<h3><a href="#">Current Sections</a></h3>
<div id="sections">
    <?php
    $sections_query = 'select c.dept, c.course, c.short_name, '
        . 's.id, s.section, s.banner '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . 'order by c.dept, c.course, s.section';
    $sections_result = $db->query( $sections_query );
    if( $sections_result->num_rows == 0 ) {
        print "<p>No current sections</p>\n";
    } else {
        print "<ul>\n";
        while( $row = $sections_result->fetch_assoc( ) ) {
            print "  <li><a href=\"javascript:void(0)\" class=\"section_tasks\" id=\"{$row[ 'id' ]}\">"
                . "{$row[ 'dept' ]} {$row[ 'course' ]} {$row[ 'section' ]}: "
                . "{$row[ 'short_name' ]} ({$row[ 'banner' ]})</a>\n";
            print "    <ul class=\"section_tasks\" id=\"{$row[ 'id' ]}\">\n";
            print "      <li>";
            print_link( "$admin/roster.php?section={$row[ 'id' ]}", 'Class Roster' );
            $roster_query = "select count( * ) as c from student_x_section "
                . "where section = \"{$row[ 'id' ]}\" and active = 1 and incomplete = 0";
            $roster_result = $db->query( $roster_query );
            $roster_row = $roster_result->fetch_assoc( );
            $roster_result->close();
            $roster_count = $roster_row[ 'c' ];
            print " ($roster_count student" . ( $roster_count == 1 ? '' : 's' ) . ')';
            print "</li>\n";
            
            print "      <li>";
            print_link( "$admin/syllabus.php?section={$row[ 'id' ]}", 'Course Syllabus' );
            print "</li>\n";
            
            print "      <li>";
            print_link( "$admin/manage_assignments.php?section={$row[ 'id' ]}", 'Assignments &amp; Grades' );
            print "</li>\n";
            
            $ref_query = 'select * from reference '
                . "where section = {$row[ 'id' ]}";
            $ref_result = $db->query( $ref_query );
            
            print "      <li>";
            print_link( "$admin/reference.php?section={$row[ 'id' ]}", 'Reference Materials' );
            print ' (' . $ref_result->num_rows . ')';
            print "</li>\n";

            print "      <li>";
            print_link( "$admin/attendance.php?section={$row[ 'id' ]}", 'Attendance' );
            print "</li>\n";
            
            print "      <li>";
            print_link( "$admin/sign_in_sheet.php?section={$row[ 'id' ]}", 'Print Sign-in Sheet' );
            print "</li>\n";
            
            print "      <li>";
            print_link( "$admin/make_csv.php?section={$row[ 'id' ]}&type=Excel", 'Export Grades to Excel' );
            print "</li>\n";

            print "</ul></li>\n";
        }
        print "</ul>\n";
    }
    
    ?>
    <p><?php print_link( "$admin/login_history.php", 'View Login History' ); ?></p>
    <p><?php print_link( "$admin/sections.php", 'Edit Current Sections' ); ?></p>
</div>  <!-- div#sections -->

<h3><a href="#">Course Information</a></h3>
<div id="courses">
<ul>
    <li><?php print_link( "$admin/courses.php", "Courses" ); ?></li>
    <li><?php print_link( "$admin/textbooks.php", 'Textbooks' ); ?> /
        <?php print_link( "$admin/authors.php", 'Authors' ); ?></li>
    <li><?php print_link( "$admin/letter_grades.php", 'Letter Grades' ); ?></li>
    <li><?php print_link( "$admin/grade_types.php", 'Grade Types' ); ?></li>
</ul>
</div>  <!-- div#courses -->

<h3><a href="#">E-Mail</a></h3>
<div id="e-mail">
<ul>
  <li><?php print_link( "$admin/email_class.php", 'Send E-Mail To A Class' ); ?></li>
  <li><?php print_link( "$admin/read_email.php", 'Read E-Mail From Students' ); ?></li>
</ul>
</div>  <!-- div#courses -->

<h3><a href="#">PHProf Configuration</a></h3>
<div id="semester">
<ul>
    <li><?php print_link( "$admin/prof_details.php", 'About You' ); ?></li>
    <li><?php print_link( "$admin/calendar.php", 'About This Semester' ); ?></li>
    <li><?php print_link( "$admin/contact_info.php", 'Edit Your Contact Information' ); ?></li>
    <li><?php print_link( "$admin/pages.php", 'Static Pages' ); ?></li>
    <li><?php print_link( "$admin/syllabus_editor.php", 'Syllabus Editor' ); ?></li>
    <li><?php print_link( "$admin/end_of_semester.php", 'End-of-Semester Clean Up' ); ?></li>
    <li><?php print_link( "$admin/phprof.php", 'PHProf Configuration Options' ); ?></li>
</ul>
</div>  <!-- div#semester -->

</div>  <!-- div.accordion -->

<script type="text/javascript">
$(document).ready(function(){
    
    $('div#unverified').hide();
    $.post( "<?php echo $admin; ?>/count_unverified_students.php",
        function(data) {
            if( data != '' ) {
                $('div#unverified').html(data).slideDown(500);
            }
        }
    )
    
    $("div.accordion div>ul").css("padding","0");
    $("div#admin_tools").accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });
    
    $("a.logout").click(function(){
    
        // Unset all the session variables
        $.ajax({
            type: "POST",
            url: "<?php echo $docroot; ?>/logout.php"
    	});
    
        $("div#admin").fadeOut(500, function(){
            $.ajax({
                type: "POST",
                url: "<?php echo $docroot; ?>/not_logged_in_tools.php",
                success: function( msg ) {
                    $("div#not_logged_in").html( msg ).fadeIn(500);
                }
            })
        });
        return false;
    });
    
    $("div#sections a.section_tasks").click(function(){
        var id = $(this).attr("id");
        $("div#sections ul.section_tasks[id=" + id + "]").slideToggle(1000);
    })
    
    $("div#admin_top a.hide").click(function(){
        $("div#show_left").slideDown()
            .html("<a href=\"javascript:void(0)\" id=\"show_left\">Unhide the admin panel</a>")
            .css("padding","0.25em");
        $("div#left").fadeOut( 250, function(){
            $("div#main").animate({ width: "100%" }, 250 );
            $("a[id=show_left]").click(function(){
                $("div#main").animate({ width: "63%" }, 250, function(){
                    $("div#left").fadeIn( 250, function(){
                        $("div#show_left").slideUp()
                            .css("padding","0");
                    });
                })
            })
        })
    })
    
})

</script>

<?php
} else {
    print $no_admin;
}

?>