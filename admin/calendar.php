<?php 
$title_stub = 'Calendar';
require_once ( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    // Prepopulate text fields with something useful, so we don't have to search
    // from December 1969
    $start = $end = date( 'l, F j, Y' );
    
    $semester_query = 'select * from semester';
    $semester_result = $db->query( $semester_query );
    if( $semester_result->num_rows == 1 ) {
        $semester_row = $semester_result->fetch_assoc();
        $semester_result->close();
        
        if( $semester_row[ 'name' ] != '')
            $semester = $semester_row[ 'name' ];
        if( $semester_row[ 'start' ] != '0000-00-00')
            $start = date( 'l, F j, Y', strtotime( $semester_row[ 'start' ] ) );
        if( $semester_row[ 'end' ] != '0000-00-00')
            $end = date( 'l, F j, Y', strtotime( $semester_row[ 'end' ] ) );
    }
    
    $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    
?>
<h2>
    This Semester
</h2>
<p>
    Semester name: <span class="editInPlace" id="semester_name"><?php echo $semester; ?></span>
</p>
<p>
    Semester start date: <input size="30" type="text" class="date" id="semester_start" value="<?php echo $start; ?>" />
</p>
<p>
    Semester end date: <input size="30" type="text" class="date" id="semester_end" value="<?php echo $end; ?>" />
</p>
<h2>
    Holidays
</h2>
<div id="holidays">
</div>
<h3>
    Add A Holiday
</h3>
<div id="new_holiday">
    <table>
        <tr>
        <td>
            Date:
        </td>
        <td>
            <input type="text" id="holiday_date" />
        </td>
        <tr>
            <td>
                Holiday:
            </td>
            <td>
                <input type="text" id="description" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="holiday_day">Are Day Classes Canceled?</label>
            </td>
            <td>
                <input type="checkbox" id="holiday_day" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="holiday_evening">Are Evening Classes Canceled?</label>
            </td>
            <td>
                <input type="checkbox" id="holiday_evening" />
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center">
                <input type="submit" id="new_holiday" value="Add This Holiday" />
            </td>
        </tr>
    </table>
</div><!-- div#new_holiday -->
<h2>
    Rescheduled Days
</h2>
<div id="rescheduled_days">
</div>
<h3>
    Add A Rescheduled Day
</h3>
<div id="new_rescheduled_day">
    <table>
        <tr>
        <td>
            Date:
        </td>
        <td>
            <input type="text" id="resched_date" />
        </td>
        <tr>
            <td>
                Which Day Do Classes Follow?
            </td>
            <td>
                <select id="follow" />
                <?php 
                for( $i = 0; $i < 7; $i++ ) {
                    print "<option value=\"$i\">{$days[ $i ]}</option>\n";
                }
                print "</select>\n";
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <label for="resched_day">Are Day Classes Rescheduled?</label>
            </td>
            <td>
                <input type="checkbox" id="resched_day" />
            </td>
        </tr>
        <tr>
            <td>
                <label for="resched_evening">Are Evening Classes Rescheduled?</label>
            </td>
            <td>
                <input type="checkbox" id="resched_evening" />
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center">
                <input type="submit" id="new_rescheduled_day" value="Add This Rescheduled Day" />
            </td>
        </tr>
    </table>
</div><!-- div#new_rescheduled_day -->
<script type="text/javascript">
    $(document).ready(function() {
        $(document).attr('title', $(document).attr('title') +
        ' :: <?php echo $semester; ?>');
        
        $.post('holidays.php', function(data) {
            $("div#holidays").html(data);
        })
        
        $.post('rescheduled_days.php', function(data) {
            $("div#rescheduled_days").html(data);
        })
        
        $("input.date").datepicker({
            dateFormat: 'DD, MM d, yy',
            onSelect: function(date) {
                var id = $(this).attr('id');
                
                $.ajax({
                    type: 'POST',
                    url: "<?php echo $admin ?>/update_calendar.php",
                    data: "column=" + id + "&date=" + date,
                    dataType: 'text',
                    success: function(msg) {
                        // alert( msg );
                    }
                    
                })
            }
        })
        
        $("span#semester_name").editInPlace({
            url: "update_calendar.php",
            params: "ajax=yes",
            saving_image: "<?php echo $docroot ?>/images/ajax-loader.gif"
        });
        
        $("input#holiday_date").datepicker({
            dateFormat: 'DD, MM d, yy'
        })
        
        $("input#resched_date").datepicker({
            dateFormat: 'DD, MM d, yy'
        })
        
        $('input[id=new_holiday]').click(function() {
            var date = $('input#holiday_date').val();
            var description = $('input#description').val();
            var day = $('input#holiday_day').attr('checked') == "checked" ? 1 : 0;
            var evening = $('input#holiday_evening').attr('checked') == "checked" ? 1 : 0;
            $.post('holidays.php', {
                date: date,
                description: description,
                day: day,
                evening: evening
            }, function(data) {
                $('div#holidays').html(data);
            })
        })
        
        $('input#new_rescheduled_day').click(function() {
            var date = $('input#resched_date').val();
            var follow = $('select#follow').val();
            var day = $('input#resched_day').attr('checked') == "checked" ? 1 : 0;
            var evening = $('input#resched_evening').attr('checked') == "checked" ? 1 : 0;
            $.post('rescheduled_days.php', {
                date: date,
                follow: follow,
                day: day,
                evening: evening
            }, function(data) {
                $('div#rescheduled_days').html(data);
            })
        })
        
    });
</script>
<?php 
} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include ( "$fileroot/_footer.inc" );

?>
