<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    // Was a new author added?
    
    if( isset( $_POST[ 'first' ] ) ) {
        $insert_query = 'insert into authors( id, first, middle, last, email, url ) '
            . 'values ( null, ';
        foreach( explode( ' ', 'first middle last email url' ) as $field ) {
            $safe[ $field ] = $db->real_escape_string( $_POST[ $field ] );
            if( $field == 'first' or $field == 'middle' or $field == 'last' ) {
                $safe[ $field ] = ucwords( $safe[ $field ] );
            }
            $insert_query .= "\"{$safe[ $field ]}\"";
            if( $field != 'url' ) {
                $insert_query .= ', ';
            }
        }
        $insert_query .= ' )';
         $db->query( $insert_query );
        //print "<pre>$insert_query;</pre>\n";
    }
    
    // Was an author deleted?
    if( isset( $_POST[ 'delete_author' ] ) ) {
	$id = $db->real_escape_string( $_POST[ 'delete_author' ] );
        $db->query( "delete from authors where id = \"$id\"" );
    }
    
    $authors_query = 'select * from authors order by last, first, middle';
    $authors_result = $db->query( $authors_query );
    
    print "<div id=\"authors\">\n";
    
    if( $authors_result->num_rows == 0 ) {
        print 'None.';
    } else {
        $accordion = 0;
        while( $author_row = $authors_result->fetch_assoc( ) ) {
            $id = $author_row[ 'id' ];
            $name = $author_row[ 'first' ] . ' '
                . ( $author_row[ 'middle' ] != '' ? $author_row[ 'middle' ] . ' ' : '' )
                . $author_row[ 'last' ];
            print "<h3><a href='#'>$name</a></h3>\n";
            
            print "<div class=\"author\" id=\"$id\"\n";
            print "Name: <span class=\"first\" id=\"$id\">{$author_row[ 'first' ]}</span>\n";
            print "<span class=\"middle\" id=\"$id\">{$author_row[ 'middle' ]}</span>\n";
            print "<span class=\"last\" id=\"$id\">{$author_row[ 'last' ]}</span>.<br />\n";
            print "E-mail Address: <span class=\"email\" id=\"$id\">{$author_row[ 'email' ]}</span>.<br />\n";
            print "Website: <span class=\"url\" id=\"$id\">{$author_row[ 'url' ]}</span>.<br /><br />\n\n";
            
            print "<div style=\"text-align: center;\">\n";
            print "<a href=\"javascript:void(0)\" id=\"$id\" class=\"delete_author\" "
                . "title=\"Remove $name\">"
                . "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" width=\"16\" /></a>\n";
            print "<a href=\"javascript:void(0)\" id=\"$id\" class=\"delete_author\" "
                . "title=\"Remove $name\">Remove $name from the database</a>.\n";
            print "</div>\n";
            
            print "</div>  <!-- div.author#$id -->\n\n";
            $accordion++;
        }
    }
    
    print "</div>  <!-- div#authors -->\n\n";

?>
<script type="text/javascript">
$(document).ready(function(){
    $("div#authors").accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    });

    $("span.first").editInPlace({
        url: 'update_author.php',
        default_text: '(Click here to add first name)',
        params: 'ajax=yes&column=first',
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })
    
    $("span.middle").editInPlace({
        url: 'update_author.php',
        default_text: '(Click here to add middle name)',
        params: 'ajax=yes&column=middle',
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })
    
    $("span.last").editInPlace({
        url: 'update_author.php',
        default_text: '(Click here to add last name)',
        params: 'ajax=yes&column=last',
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })
    
    $("span.email").editInPlace({
        url: 'update_author.php',
        default_text: '(Click here to add e-mail address)',
        params: 'ajax=yes&column=email',
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })
    
    $("span.url").editInPlace({
        url: 'update_author.php',
        default_text: '(Click here to add website)',
        params: 'ajax=yes&column=url',
        saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
    })
    
    $("a.delete_author").click(function(){
        var id = $(this).attr("id");
        $.post( 'list_authors.php',
            { delete_author: id },
            function(data){
                $("div#current_authors").html(data);
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
