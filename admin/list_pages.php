<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    // Was a new page added?
    if( isset( $_POST[ 'new_title' ] ) and isset( $_POST[ 'new_contents' ] ) ) {
        $safe_title = $db->real_escape_string( trim( $_POST[ 'new_title' ] ) );
        $safe_contents = $db->real_escape_string( trim( $_POST[ 'new_contents' ] ) );
        $title_slug_words = explode( ' ', $safe_title );
        $title_slug = strtolower( $title_slug_words[ 0 ] );
        for( $i = 1; $i < sizeof( $title_slug_words ); $i++ ) {
            $word = strtolower( $title_slug_words[ $i ] );
            if( $word != 'a' && $word != 'an' && $word != 'the' ) {
                $title_slug .= '_' . $word;
            }
        }
        $title_slug = preg_replace( '/[^a-z0-9_]/', '', $title_slug );
        $insert_query = 'insert into pages( id, title, text, slug, last_modified ) '
            . "values( null, \"$safe_title\", \"$safe_contents\", \"$title_slug\", "
            . '"' . date( 'Y-m-d H:i:s' ) . '" )';
        $insert_result = $db->query( $insert_query );
    }
    
    // Was a page deleted?
    if( isset( $_POST[ 'delete_id' ] ) ) {
        $db->query( "delete from pages where id = {$_POST[ 'delete_id' ]}" );
    }
    
    // Was a page edited?
    if( isset( $_POST[ 'edit' ] ) ) {
        $safe_text = $db->real_escape_string( trim( $_POST[ 'text' ] ) );
        $update_query = "update pages set text = \"$safe_text\", "
            . 'last_modified = "' . date( 'Y-m-d H:i:s' ) . '" '
            . "where id = {$_POST[ 'edit' ]}";
        $update_result = $db->query( $update_query );
    }
    
    print "<div id=\"pages\">\n";
    
    $pages_query = 'select * from pages order by title, last_modified';
    $pages_result = $db->query( $pages_query );
    if( $pages_result->num_rows == 0 ) {
        print "There are no pages to display.\n";
    } else {
        while( $row = $pages_result->fetch_assoc( ) ) {
            print "<h3><a href='#'>" . stripslashes( $row[ 'title' ] ) . "</a></h3>\n";
            print "<div class=\"contents\" id=\"{$row[ 'id' ]}\">\n";
            print "<p class=\"title\" id=\"{$row[ 'id' ]}\">" . stripslashes( $row[ 'title' ] ) . "</p>\n";
            print "<textarea name=\"{$row[ 'id' ]}\" id=\"{$row[ 'id' ]}\">"
                . "{$row[ 'text' ]}</textarea>\n";
            
            print "<p><input type=\"submit\" class=\"edit\" id=\"{$row[ 'id' ]}\" value=\"Edit This Page\" /></p>\n";
            
            print "<p class=\"delete_link\">";
            print "<a href=\"javascript:void(0)\" class=\"delete_page\" "
                . "id=\"{$row[ 'id' ]}\">"
                . "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" width=\"16\" />"
                . "</a>\n";
            print "<a href=\"javascript:void(0)\" class=\"delete_page\" "
                . "id=\"{$row[ 'id' ]}\">Delete this page</a></p>\n";
            print "</div>\n";
        }
    }
    print "</div>  <!-- div#pages -->\n";
    
?>

<script type="text/javascript">
$(document).ready(function(){
    $("div#pages").accordion({
        active: false,
        autoHeight: false,
        collapsible: true        
    });

    $("div.contents textarea").markItUp(mySettings);
    
    $("a.delete_page").click(function(){
        var id = $(this).attr("id");
        $.post('list_pages.php',
            { delete_id: id },
            function(data){
                $("div#current_pages").html(data);
            }
        )
    })
    
    $("input.edit").click(function(){
        var id = $(this).attr("id");
        var text = $("textarea[id="+id+"]").val();
        
        $.post('list_pages.php',
            { edit: id, text: text },
            function(data){
                $("div#current_pages").html(data);
            }
        )
    })
    
})
</script>

<?php

} else {
    print $no_admin;
}
?>
