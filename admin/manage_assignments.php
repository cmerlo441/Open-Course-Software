<?php

$title_stub = 'Manage Assignments';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $course_query = 'select c.dept, c.course, c.id as course_id, '
        . 's.id as section_id, s.section '
        . 'from courses as c, sections as s '
        . 'where s.course = c.id '
        . "and s.id = \"{$_GET[ 'section' ]}\"";
    $course_result = $db->query( $course_query );
    $course_row = $course_result->fetch_assoc( );
    
    $section_name = "{$course_row[ 'dept' ]} {$course_row[ 'course' ]} "
        . "{$course_row[ 'section' ]}";
    $section_id = $course_row[ 'section_id' ];
    $course_id = $course_row[ 'course_id' ];
    
    $assignment_types_query = 'select t.id, t.grade_type, t.plural '
        . 'from grade_types as t, grade_weights as w '
        . "where w.course = \"{$course_id}\" "
        . 'and w.grade_type = t.id '
        . 'order by t.grade_type';
    $assignment_types_result = $db->query( $assignment_types_query );
    $assignment_types = array( );
    while( $row = $assignment_types_result->fetch_assoc( ) ) {
        $assignment_types[ $row[ 'id' ] ][ 'singular' ] = $row[ 'grade_type' ];
        $assignment_types[ $row[ 'id' ] ][ 'plural'] = $row[ 'plural' ];
    }
    print "<h2>Existing Assignments</h2>\n";
    print "<div class=\"accordion\" id=\"all_assignments\">\n";
    foreach( $assignment_types as $id=>$types ) {
        print "<h3><a href='#'>{$types[ 'plural' ]}</a></h3>\n";
        print "<div class=\"assignments\" id=\"$id\">";
        print "<img src=\"$docroot/images/ajax-loader.gif\" />";
        print "</div>  <!-- div.assignments#$id -->\n";
    }
    print "</div>  <!-- div#all_assignments -->\n";
    
?>

<h2>Create a New Assignment</h2>
<table>
    <tr>
        <td>Type of assignment:</td>
        <td><select id="type">
            <option value="0">Choose an assignment type</option>
            <?php
    foreach( $assignment_types as $id=>$types ) {
        print "<option value=\"$id\">{$types[ 'singular' ]}</option>\n";
    }
    print "</select></div>  <!-- div#type -->\n";
            ?>
        </select></td>
    </tr>
    
    <tr>
        <td>Assignment title (optional):</td>
        <td><input type="text" id="title" /></td>
    </tr>
    
    <tr>
        <td>Due date:</td>
        <td><input type="text" id="due_date" size="40"/></td>
    </tr>
    
    <tr>
        <td>Due time:</td>
        <td><input type="text" id="due_time" size="40" /></td>
    </tr>
    
    <tr>
        <td>Description:</td>
        <td><textarea cols="40" rows="10" id="description"></textarea></td>
    </tr>
    
    <tr>
        <td colspan="2" align="center">
            <input type="submit" id="submit" value="Create Assignment" />
        </td>
    </tr>
</table>

</div>  <!-- div#new_assignment -->

<script type="text/javascript">
$(document).ready(function(){
    document.title = document.title + " :: <?php echo $section_name; ?>";
    $("h1").html( $("h1").html() + " for <?php echo $section_name; ?>" );
    
    $("div#all_assignments").accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    })

    $("div.assignments").each(function(){
        var section = "<?php echo $section_id; ?>";
        var grade_type = $(this).attr('id');
        $.post('section_assignments.php',
            { section: section, grade_type: grade_type },
            function(data){
                $("div.assignments[id="+ grade_type +"]").html(data);
            }
        )
    })

    $("input#due_date").datepicker( {dateFormat: 'yy-mm-dd'} ).change(function(){
        var assignment_type = $('#type').val();
        var section = "<?php echo $_GET[ 'section' ]; ?>";
        var date = $('#due_date').val();
        
        $.post( './calculate_due_time.php',
            { assignment_type: assignment_type, section: section, date: date },
            function( data ) {
                $('input#due_time').val( data );
            }
        )
    })
    
    $('input#submit').click(function(){
        var assignment_type = $('#type').val();
        var title = $('input#title').val();
        var section = "<?php echo $_GET[ 'section' ]; ?>";
        var due_date = $('#due_date').val();
        var due_time = $('#due_time').val();
        var description = $('textarea#description').val();
        
        $.post( './new_assignment.php',
            {
                assignment_type: assignment_type,
                title: title,
                section: section,
                due_date: due_date,
                due_time: due_time,
                description: description
            },
            function( ) {
                $("div.assignments").each(function(){
                var section = "<?php echo $section_id; ?>";
                var grade_type = $(this).attr('id');
                $.post('section_assignments.php',
                    { section: section, grade_type: grade_type },
                    function(data){
                        $("div.assignments[id="+ grade_type +"]").html(data);
                        $('select#type').val('0');
                        $('input:text').val('');
                        $('textarea#description').val('');
                    })
                })
            }
        )
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
