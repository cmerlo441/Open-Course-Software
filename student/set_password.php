<?php

$title_stub = 'Create Password';
require_once( '../_header.inc' );

?>

<div id="set_password">
</div>  <!-- div#set_password -->

<script type="text/javascript">
$(document).ready(function(){
    $.post('set_password_display.php',
        { code: "<?php echo $_GET[ 'code' ]; ?>" },
        function(data) {
            $('div#set_password').html(data);
        }
    )
})
</script>
<?php

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>