<?php

$title_stub = 'Class Roster';
require_once( '../_header.inc' );

function getOrdinal($number){
    // get first digit
    $digit = abs($number) % 10;
    $ext = 'th';
    $ext = ((abs($number) %100 < 21 && abs($number) %100 > 4)
	    ? 'th'
	    : (($digit < 4) ? ($digit < 3) ? ($digit < 2) ? ($digit < 1)
	       ? 'th' : 'st' : 'nd' : 'rd' : 'th'));
    return "{$number}{$ext}";
}

if( $_SESSION[ 'admin' ] == 1 ) {

    $statuses = array();
    $statuses_query = 'select id, status '
        . 'from student_statuses '
        . 'where status != "'
        . ( $credits == 0 ? 'UW and status != NA' : 'UU' ) . '" '
        . 'order by id';
    $statuses_result = $db->query( $statuses_query );
    while( $status_row = $statuses_result->fetch_object( ) ) {
        $statuses[ $status_row->id ] = $status_row->status;
    }

    $section = $db->real_escape_string( $_GET[ 'section' ] );

    $section_query = 'select c.id as course_id, c.dept, c.course, s.section, s.id, c.long_name '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = $section";
    $section_result = $db->query( $section_query );
    $section_row = $section_result->fetch_assoc( );
    $course_id = $section_row[ 'course_id' ];
    $section_result->close( );

    $roster_query = 'select s.id, s.first, s.middle, s.last, s.banner, '
        . 'x.section, x.status, status.status as status_string '
        . 'from students as s, student_x_section as x, student_statuses as status '
        . 'where x.student = s.id '
        . 'and x.status = status.id '
        . 'and s.verified = 1 '
        . "and x.section = $section";
    $roster_result = $db->query( $roster_query );

    $course = $section_row[ 'dept' ] . ' ' . $section_row[ 'course' ] . ' '
        . $section_row[ 'section' ] . ': ' . $section_row[ 'long_name' ];
        
    print "<h3>$course</h3>\n";
    
    if( $roster_result->num_rows == 0 ) {
        print "<p>No students.";
    } else {
        print "<table class=\"tablesorter\" id=\"roster_table\">\n";
        print "<thead>\n";
        print "<tr>\n";
        print "  <th>First</th>\n";
        print "  <th>Last</th>\n";
        print "  <th>MyNCC ID</th>\n";
        print "  <th>Status</th>\n";
        print "  <th>Average</th>\n";
        print "  <th>Absences</th>\n";
        print "  <th>Logins</th>\n";
        print "</tr>\n";
        print "</thead>\n\n";

        print "<tbody>\n";
        while( $row = $roster_result->fetch_assoc( ) ) {
            print "<tr id=\"{$row[ 'id' ]}\" class=\"";
            if( $statuses[ $row[ 'status' ] ] == 'Grade' or
                $statuses[ $row[ 'status' ] ] == 'Audit' or
                $statuses[ $row[ 'status' ] ] == 'INC' ) {
                print 'active';
            } else {
                print 'not_active';
            }
            print "\">\n";
            
            print "  <td student=\"{$row[ 'id' ]}\" class=\"first\">"
                . ucwords( $row[ 'first' ] ) . "</td>\n";
            print "  <td student=\"{$row[ 'id' ]}\" class=\"last\">"
                . ucwords( $row[ 'last' ] ) . "</td>\n";
            print "  <td student=\"{$row[ 'id' ]}\" class=\"banner\">"
                . "{$row[ 'banner' ]}</td>\n";
            print "  <td student=\"{$row[ 'id' ]}\" class=\"status\">\n";
            print "    <select class=\"status\" student=\"{$row[ 'id' ]}\">\n";
            foreach( $statuses as $key=>$value ) {
                print "      <option value=\"$key\"";
                if( $row[ 'status' ] == $key ) { print ' selected'; }
                print ">" . ( $value == 'Grade' ? 'Normal' : $value ) . "</option>\n";
            }
            print "    </select></td>\n";
            print "  <td student=\"{$row[ 'id' ]}\" class=\"average\"></td>\n";

        	$absences_query = 'select a.id '
                . 'from attendance as a, attendance_types as t '
                . "where a.student = {$row[ 'id' ]} "
                . "and a.section = {$row[ 'section' ]} "
                . "and a.presence = t.id "
                . "and t.type like \"a%\"";
            $absences_result = $db->query( $absences_query );
            $absences = $absences_result->num_rows;

            print "  <td student=\"{$row[ 'id' ]}\" class=\"absences\">$absences</td>\n";

            $logins_query = 'select id from logins '
                . "where student = {$row[ 'id' ]}";
            $logins_result = $db->query( $logins_query );
            $logins = $logins_result->num_rows;
            print "  <td student=\"{$row[ 'id' ]}\" class=\"logins\">$logins</td>\n";

            print "</tr>\n\n";
        }
        $roster_result->close( );
        print "</tbody>\n";
        print "</table>\n\n";

        print "<div id=\"recent_absences\">\n";
        print "<h2>Recent Absences</h2>\n";
        $recent_meeting_query = 'select date from attendance '
            . "where section = {$section_row[ 'id' ]} "
            . 'order by date desc limit 1';
        $recent_meeting_result = $db->query( $recent_meeting_query );
        if( $recent_meeting_result->num_rows == 0 ) {
            print 'You haven\'t entered any attendance.';
                } else {
            $recent_meeting_row = $recent_meeting_result->fetch_object( );
            $date = $recent_meeting_row->date;
            print "<h3>" . date( 'l, F j, Y', strtotime( $date ) ) . "</h3>\n";
            $recent_absences_query = 'select s.first, s.last, s.id '
                . 'from students as s, attendance as a, attendance_types as t '
                . 'where a.student = s.id '
                . "and a.section = {$section_row[ 'id' ]} "
                . "and a.date = \"$date\" "
                . 'and a.presence = t.id '
                . 'and t.type = "absent" '
                . 'order by s.last, s.first';
            $recent_absences_result = $db->query( $recent_absences_query );
            if( $recent_absences_result->num_rows == 0 ) {
                print 'Perfect attendance.';
            } else {
                print "<ul>\n";
                while( $row = $recent_absences_result->fetch_assoc( ) ) {
                print '<ul>' . lastfirst( $row );
                $how_many_query = 'select a.id '
                    . 'from attendance as a, attendance_types as t '
                    . "where a.student = {$row[ 'id' ]} "
                    . "and a.section = {$section_row[ 'id' ]} "
                    . "and a.presence = t.id "
                    . 'and t.type = "absent"';
                $how_many_result = $db->query( $how_many_query );
                $how_many = $how_many_result->num_rows;
                print " (" . getOrdinal( $how_many ) . " absence)</ul>\n";
            }
            print "</ul>\n";
        }
    }
    print "</div>  <!-- div#recent_absences -->\n";

    print "<div id=\"student_data\"></div>\n";
}

?>

<script type="text/javascript">
$(document).ready(function(){
    
    var course  = "<?php echo $course_id; ?>";
    var section = "<?php echo $section; ?>";

    // add parser through the tablesorter addParser method
    $.tablesorter.addParser({
        // set a unique id
        id: 'status',
        is: function(s) {
            // return false so this parser is not auto detected
            return false;
        },
        format: function(s) {
            // format your data for normalization
            var m = null;
            if( m = /value="([0-9])" selected/.exec(s.toLowerCase()) ) {
                var v = m[1];
                return v;
            }
        },
        // set type, either numeric or text
        type: 'numeric'
    });

    $(document).attr('title', $(document).attr('title') + ' :: <?php echo $course; ?>');
    
    $("#roster_table").tablesorter({
        sortList: [ [3,0], [1,0], [0,0] ],
        widgets: [ 'ocsw', 'clickable_rows' ],
        headers: {
            3: { sorter: 'status' }
        }
    });
    
    $('#roster_table td.average').each(function(){
        var student = $(this).attr("student");
        var section = "<?php echo $section; ?>";
        
        $.post( 'calculate_student_average.php',
            { section: section, student: student },
            function(data){
                $('#roster_table td.average[student=' + student + ']').html(data);
                $('#roster_table').trigger('update');
            }
        )
    });
    
    $("#roster_table tr").click(function(){
        var id = $(this).attr("id");
        var section = "<?php echo $section; ?>";

        $.post( 'student.php',
            { student: id, section: section },
            function(data){
                $("div#student_data").html(data).fadeIn(1000);
            }
        )
    });

    /*
     * This code will have to be replaced with code that handles a change to a
     * <select>, and then calls the replacement to toggle_student_active_status.php
     */ 


    $('select.status').change(function(){
        var student = $(this).attr('student');
        var status_id = $(this).val();
        var status_string = $(this).find('option:selected').text();
        
        var tr = $(this).parent().parent();
        
        var first_name = $(tr).find('td.first').text();
        var last_name = $(tr).find('td.last').text();
        var name = first_name + ' ' + last_name;
        
        $.post('change_student_status.php',
            { student: student, section: section, status: status_id },
            function(data){
                if( data == 1 ) {
                    
                    if( status_string == 'Normal' || status_string == 'Audit' || status_string == 'INC' ) {
                        $(tr).removeClass('not_active').addClass('active');
                    } else {
                        $(tr).removeClass('active').addClass('not_active');
                    }
                    
                    $.pnotify({
                        pnotify_title: 'Status Changed',
                        pnotify_text: 'You have changed ' + name + "'s status to " + status_string + '.',
                        pnotify_shadow: true
                    });
                    $.post( 'calculate_student_average.php',
                        { section: section, student: student },
                        function(data){
                            $('#roster_table td.average[student=' + student + ']').html(data);
                            $('#roster_table').tablesorter({
                                sortList: [ [3,0], [1,0], [0,0] ],
                                widgets: [ 'ocsw', 'clickable_rows' ],
                                headers: {
                                    3: { sorter: 'status' }
                                }
                            })
                        }
                    )
                } else {
                    $.pnotify({
                        pnotify_title: 'Error',
                        pnotify_text: name + "'s status was not changed to " + status_string + '.',
                        pnotify_type: 'error',
                        pnotify_shadow: true
                    })
                }
            }
        )
        
    });
    
    /*
    $("a.active").click(function(){
        var id = $(this).attr("id");
        var section = "<?php echo $section; ?>";
        
        $.post( 'toggle_student_active_status.php',
            { id: id, section: section },
            function(data){
                $("td.active[student=" + id + "]").html(data);
                if( $("tr[id=" + id + "]").hasClass("active") ) {
                    $("tr[id=" + id + "]").removeClass("active");
                    $("tr[id=" + id + "]").addClass("not_active");
                } else {
                    $("tr[id=" + id + "]").removeClass("not_active");
                    $("tr[id=" + id + "]").addClass("active");                    
                }
                $("#roster_table").tablesorter({
                    sortList: [ [4,0], [2,0], [0,0], [1,0] ],
                    widgets: [ 'ocsw', 'clickable_rows' ],
                    headers: {
                        4: { sorter: 'active' }
                    }
                });
            }
        )
    
    });
    */
});
</script>

<?php

} else {
  print $no_admin;
}
   
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
