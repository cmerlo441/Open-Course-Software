<?php 

$title_stub = 'Class Sections';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    // Show current sections
    
    print "<h2>Current Sections</h2>\n";
    print "<div id=\"current_sections\"></div>\n";
    
    // Code to add a new section
    
?>

<script type="text/javascript">

$(document).ready(function(){

    $.post( 'beta_list_sections.php', function(data){
        $('div#current_sections').html(data);
    })
	
})

</script>

<?php
}

?>