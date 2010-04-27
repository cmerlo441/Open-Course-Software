<?php

$title_stub = 'Calendar';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    $semester_query = 'select * from semester';
    $semester_result = $db->query( $semester_query );
    $semester_row = $semester_result->fetch_assoc( );
    $semester_result->close( );
    $semester = $semester_row[ 'name' ];
    $start = date( 'l, F j, Y', strtotime( $semester_row[ 'start' ] ) );
    $end = date( 'l, F j, Y', strtotime( $semester_row[ 'end' ] ) );
    
?>
<h2>This Semester</h2>
    
<p>Semester name:  <span class="editInPlace" id="semester_name"><?php echo $semester; ?></span></p>
<p>Semester start date:  <input size="30" type="text" class="date"
    id="semester_start" value="<?php echo $start; ?>" />
<p>Semester end date:  <input size="30" type="text" class="date"
    id="semester_end" value="<?php echo $end; ?>" />

<h2>Holidays</h2>
<div id="holidays">
</div>

<h3>Add A Holiday</h3>
<div id="new_holiday">
<table>
    <tr>
        <td>Date:</td>
        <td><input type="text" id="date" /></td>
    <tr>
        <td>Holiday:</td>
        <td><input type="text" id="description" /></td>
    </tr>
    <tr>
        <td>Day Classes Canceled?</td>
        <td><input type="checkbox" id="day" /></td>
    </tr>
    <tr>
        <td>Evening Classes Canceled?</td>
        <td><input type="checkbox" id="evening" /></td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center">
            <input type="submit" id="new_holiday" value="Add This Holiday" />
        </td>
    </tr>
</table>
</div>  <!-- div#new_holiday -->

<script type="text/javascript">
$(document).ready(function(){
    $(document).attr('title', $(document).attr('title') + ' :: <?php echo $semester; ?>');
    
    $.post( 'holidays.php',
        function(data){
            $("div#holidays").html(data);
        }
    )
    
    $("input.date").datepicker({ dateFormat: 'DD, MM d, yy', onSelect: function(date){
            var id = $(this).attr('id');
            
            $.ajax({
                type: 'POST',
                url: "<?php echo $admin ?>/update_calendar.php",
                data: "column=" + id + "&date=" + date,
                dataType: 'text',
                success: function( msg ) {
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

    $("input#date").datepicker({
        dateFormat: 'DD, MM d, yy'
    })
    
    $('input[id=new_holiday]').click(function(){
        var date = $('input#date').val();
        var description = $('input#description').val();
        var day = $('input#day').attr('checked') == true ? 1 : 0;
        var evening = $('input#evening').attr('checked') == true ? 1 : 0;
        $.post('holidays.php',
            { date: date, description: description, day: day, evening: evening },
            function(data){
                $('div#holidays').html(data);
            }
        )
    })
    
});
</script>

<?php

} else {
    print $no_admin;
}
   
$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>