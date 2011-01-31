<?php

$title_stub = 'Important Links';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

?>

<p>Use this page to add important links for your students from your
home page.</p>

<h2>Current Links</h2>
<div id="current_links"></div>

<h2>Add A Link</h2>
<div id="new_link">
  URL: <input type="text" id="url" /><br />
  Link Text: <input type="text" id="link_text" /><br />
  <input type="submit" id="submit" value="Add This Link" />
</div>

<script type="text/javascript">

$(document).ready(function(){
    $.post('list_links.php',
	function(data){
	    $('div#current_links').html(data);
	})
    
    $('input#submit').click(function(){
        var url = $('input#url').val();
        var link_text = $('input#link_text').val();
        $.post('list_links.php',
            { url: url, link_text: link_text },
            function(data){
                $('div#current_links').html(data);
                $('input:text').val('');
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
include ( "$fileroot/_footer.inc" );

?>