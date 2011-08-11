<?php 
$no_header = 1;
require_once ( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {

    if( isset( $_POST[ 'url' ] ) and isset( $_POST[ 'link_text' ] ) ) {
        $url = $db->real_escape_string( $_POST[ 'url' ] );
        $link_text = $db->real_escape_string( $_POST[ 'link_text' ] );
        $db->query( 'insert into links( id, url, link_text, created ) '."values( null, \"$url\", \"$link_text\", ".'"'.date( 'Y-m-d H:i:s' ).'" )' );
    }
    
    else if( isset( $_POST[ 'delete' ] ) ) {
        $delete = $db->real_escape_string( $_POST[ 'delete' ] );
        $delete_query = "delete from links where id = $delete";
        $delete_result = $db->query( $delete_query );
    }
    
    $links_query = 'select * from links order by link_text';
    $links_result = $db->query( $links_query );
    if( $links_result->num_rows == 0)
        print 'None.';
    else {
        print "<ul id=\"links\">\n";
        while( $link = $links_result->fetch_object() ) {
            print "<li id=\"$link->id\">";
            print "<a class=\"delete\" href=\"javascript:void(0)\" id=\"$link->id\">\n";
            print "<img src=\"$docroot/images/silk_icons/delete.png\" "
                . "width=\"16\" height=\"16\" title=\"Delete this link\" /></a>\n";
            print "\"$link->link_text\" "
                . "(<a href=\"$link->url\">$link->url</a>)</li>\n";
        }
        print "</ul>\n";
?>

<script type="text/javascript">

$(document).ready(function(){
    
    $('a.delete').click(function(){
        var id = $(this).attr('id');
        $.post('list_links.php',
            { delete : id },
            function(data){
                $('div#current_links').html(data);
            }
        );
    })
   
})

</script>

<?php
    }
}
?>