<?php

$title_stub = 'Read E-Mail';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    print "<h2>E-Mail From Students</h2>\n";
    $from_query = 'select c.dept, c.course, sections.section, '
        . 's.first, s.middle, s.last, m.subject, m.id, m.message, m.sent_time '
        . 'from courses as c, sections, students as s, mail_from_students as m, '
        . 'student_x_section as x '
        . 'where x.student = s.id '
        . 'and x.section = sections.id '
        . 'and sections.course = c.id '
        . 'and m.student_x_section = x.id';
    $from_result = $db->query( $from_query );
    if( $from_result->num_rows > 0 ) {
        print "<table class=\"tablesorter\">\n";
        print "  <thead>\n";
        print "    <tr>\n";
        print "      <th>Student</th>\n";
        print "      <th>Section</th>\n";
        print "      <th>Subject</th>\n";
        print "      <th>Time</th>\n";
        print "    </tr>\n";
        print "  </thead>\n\n";
        
        print "  <tbody>\n";
        while( $from_row = $from_result->fetch_assoc( ) ) {
            print "    <tr id=\"{$from_row[ 'id' ]}\">\n";
            print "      <td>{$from_row[ 'last' ]}, {$from_row[ 'first' ]}";
            if( $from_row[ 'middle' ] != '' ) {
                print ' ' . $from_row[ 'middle' ];
            }
            print "</td>\n";
            
            print "      <td>{$from_row[ 'dept' ]} {$from_row[ 'course' ]} "
                . "{$from_row[ 'section' ]}</td>\n";
            
            print "      <td>{$from_row[ 'subject' ]}</td>\n";
            
            print "      <td>" . date( 'n/d g:i a', strtotime( $from_row[ 'sent_time' ] ) )
                . "</td>\n";
            print "  </tr>\n";
        }
        print "  </tbody>\n";
        print "</table>\n";
    } else {
        print 'None.';
    }
    
    print "<div id=\"details\"></div>\n";
?>

<script type="text/javascript">
$(document).ready(function(){
    $('table.tablesorter').tablesorter( {
        sortList: [ [ 1, 0 ], [ 0, 0 ] ],
        widgets: [ 'ocsw', 'clickable_rows' ]
    } );
    
    $('table.tablesorter tr').click(function(){
        var id = $(this).attr('id');
        $.post('read_mail_from_student.php',
            { id: id },
            function(data){
                $('div#details').html(data).slideDown('1000');
            }
        )
    })
})
</script>

<?php

} else {
    print $no_admin;
}

?>