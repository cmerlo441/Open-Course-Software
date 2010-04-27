<?php

$title_stub = 'Grade Types';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
?>

<h3>Existing Grade Types</h3>
<div id="current_grade_types"></div>
    
<div id="new_grade_type">
<h3>Add a New Grade Type</h3>
Singular name: <input type="text" id="new_type" />
Plural name: <input type="text" id="new_type_plural" />
<input type="submit" id="submit" value="Add" />
</div>
    

<script type="text/javascript">

$(document).ready(function(){
    $.post( 'grade_types_list.php',
        function(data){
            $("div#current_grade_types").html(data);
        }
    )
    
    $("div#new_grade_type input:submit").click(function(){
        var new_type = $("input#new_type").val();
        var new_type_plural = $("input#new_type_plural").val();
        $.post( 'grade_types_list.php',
            { new_type: new_type, new_type_plural: new_type_plural },
            function(data){
                $("div#current_grade_types").html(data);
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
