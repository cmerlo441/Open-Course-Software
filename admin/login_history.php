<?php

$title_stub = 'Login History';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $login_query = 'select l.id as login_id, l.datetime, l.address, l.browser, s.id as student_id, s.first, s.last, x.section '
        . 'from logins as l, students as s, student_x_section as x '
        . 'where l.student = s.id '
        . 'and x.student = s.id '
        . 'order by l.datetime desc '
        . 'limit 50';
    $login_result = $db->query( $login_query );
    
    $sections_query = 'select c.dept, c.course, s.section, s.id '
        . "from courses as c, sections as s "
        . 'where s.course = c.id '
        . 'order by c.dept, c.course, s.section';
    $sections_result = $db->query( $sections_query );
    print "<p style=\"text-align: center\">\n";
    while( $section_row = $sections_result->fetch_assoc( ) ) {
        print "<a href=\"javascript:void(0)\" class=\"hide\" id=\"{$section_row[ 'id' ]}\">"
            . "{$section_row[ 'dept' ]} {$section_row[ 'course' ]} {$section_row[ 'section' ]} students</a>\n";
        print "<img src=\"$docroot/images/silk_icons/accept.png\" height=\"16\" width=\"16\" "
            . "class=\"on\" id=\"{$section_row[ 'id' ]}\" />\n";
        print "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" width=\"16\" "
            . "class=\"off\" id=\"{$section_row[ 'id' ]}\" style=\"display:none\" />\n";
        print "<br />\n";
    }
    print "</p>\n";

?>

<table class="tablesorter" id="logins">
    <thead>
        <tr>
            <th>Student</th>
            <th>Time</th>
            <th>IP Address</th>
            <th>Browser</th>
            <th>OS</th>
        </tr>
    </thead>
    
    <tbody>
<?php
    while( $login = $login_result->fetch_object( ) ) {
        print "        <tr class=\"$login->section\" id=\"$login->login_id\">\n";
        print '            <td>' . ucwords( "$login->last, $login->first" ) . "</td>\n";
        print "            <td>" . date( 'm/d H:i', strtotime( $login->datetime ) ) . "</td>\n";
        print "            <td>$login->address</td>\n";
        print "            <td>" . browser( $login->browser ) . "</td>\n";
        print "            <td>" . os( $login->browser ) . "</td>\n";
        print "        </tr>\n";
    }
?>
    </tbody>
</table>

<script type="text/javascript">
$(document).ready(function(){
    $('table#logins').tablesorter({ sortList: [ [1,1], [0,0] ], widgets: [ 'ocsw' ] });
    
    $('a.hide').click(function(){
        var id = $(this).attr('id');
        $('table#logins tr.' + id ).each(function(){
            $(this).toggle();
        })
        $('img.on[id=' + id + ']').toggle();
        $('img.off[id=' + id + ']').toggle();
        $('table#logins').trigger('update');
        return false;
    })
    
    $(window).scroll(function(){
        var last_row = $('table#logins tr:last').attr('id');
        if( $(window).scrollTop() == $(document).height() - $(window).height() && last_row > 1 ) {
            $.post('login_history_data.php',
                { start: last_row },
                function(data){
                    $('table#logins tbody').append(data);
                    $('table#logins').trigger('update');
                    $('table#logins tbody td').css('background-color',$('table#logins tbody tr:first td').css('background-color'));
                }
            )
        };
        return false;
    })
    
})
</script>

<?php
} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>