<?php

$title_stub = 'Unverified Students';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

?>

<div id="list_unverified_students">
</div>  <!-- div#list_unverified_students -->

<script type="text/javascript">
$(document).ready(function(){
    $.post( 'list_unverified_students.php',
        function(data){
            $('div#list_unverified_students').html(data);
        }
    )
})
</script>

<?php

} else {
    print $no_admin;
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>
