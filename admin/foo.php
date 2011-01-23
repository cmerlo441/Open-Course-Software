<?php

$title_stub = 'Foo';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    print "<a id=\"tweet\" href=\"javascript:void(0)\">Tweet!</a>\n";

?>

<script type="text/javascript">
$(document).ready(function(){
    $('a#tweet').click(function(){
        $.post('tweet.php',
            {
                update_string: 'This is a test.  You can ignore this tweet.'
            },
            function( data ) {
                //alert( data );
            })
    })
})
	       
</script>

<?php
} else {
    print $no_admin;
}
?>