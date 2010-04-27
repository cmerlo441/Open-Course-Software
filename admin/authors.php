<?php

$title_stub = 'Authors';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    print "<div id=\"current_authors\"></div>\n";
    
?>

<h3>Add A New Author</h3>
<div id="add_new_author">
<table>
    <tr>
        <td>First Name:</td>
        <td><input type="text" id="first" /></td>
    </tr>
    <tr>
        <td>Middle Name:</td>
        <td><input type="text" id="middle_name" /></td>
    </tr>
    <tr>
        <td>Last Name:</td>
        <td><input type="text" id="last" /></td>
    </tr>
    <tr>
        <td>E-Mail Address:</td>
        <td><input type="text" id="email" /></td>
    </tr>
    <tr>
        <td>Website:</td>
        <td><input type="text" id="url" /></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><input type="submit" id="new_author" value="Add This Author" /></td>
    </tr>
</table>
</div>  <!-- div#add_new_author -->

<script type="text/javascript">
$(document).ready(function(){
    $.post( 'list_authors.php',
        function(data){
            $("div#current_authors").html(data);
        }
    )
    
    $("div#add_new_author input#new_author").click(function(){
        var first = $("input#first").val();
        var middle = $("input#middle_name").val();
        var last = $("input#last").val();
        var email = $("input#email").val();
        var url = $("input#url").val();
        
        $.post('list_authors.php',
            { first: first, middle: middle, last: last, email: email, url: url },
            function(data){
                $("div#current_authors").html(data);
                $("input#first").val("");
                $("input#middle_name").val("");
                $("input#last").val("");
                $("input#email").val("");
                $("input#url").val("");
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
