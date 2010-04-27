<?php

$title_stub = 'Static Pages';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
?>

<p>Use this area to create and maintain pages whose contents will change
infrequently, and to which you want links to appear on the front page of your
web site.</p>

<p>You may want to <a href="javascript:void(0)" class="hide">hide the left pane</a>
of the page to use these editing tools.</p>

<h3>Current Pages</h3>

<div id="current_pages">
</div>  <!-- div#current_pages -->

<h3>Create A New Page</h3>
<div id="create_page">
    <p>Title: <input type="text" id="new_title" size="30" /></p>
    <p><textarea id="new_contents"></textarea></p>
    <p><input type="submit" id="submit" value="Add This Page" /></p>
</div>  <!-- div#create_page -->

<script type="text/javascript">
$(document).ready(function(){
    $.post('list_pages.php',
        function(data){
            $("div#current_pages").html(data);
        }
    )
    
    $("textarea#new_contents").markItUp(mySettings);
    
    $("input#submit").click(function(){
        var new_title = $("input#new_title").val();
        var new_contents = $("textarea#new_contents").val();
        $.post( 'list_pages.php',
            { new_title: new_title, new_contents: new_contents },
            function(data){
                $("div#current_pages").html(data);
                $("input#new_title").val("");
                $("textarea#new_contents").val("");
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
require_once( "$fileroot/_footer.inc" );

?>
