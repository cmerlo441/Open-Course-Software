<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    // Was an author added to a textbook?
    if( isset( $_POST[ 'textbook' ] ) and isset( $_POST[ 'new_author' ] ) ) {
        $last_sequence_query = 'select sequence from textbook_x_author '
            . "where textbook = {$_POST[ 'textbook' ]} "
            . 'order by sequence desc';
        $last_sequence_result = $db->query( $last_sequence_query );
        if( $last_sequence_result->num_rows == 0 ) {
            $sequence = 1;
        } else {
            $row = $last_sequence_result->fetch_assoc( );
            $sequence = $row[ 'sequence' ] + 1;
        }
        $insert_query = 'insert into textbook_x_author( id, textbook, author, sequence ) '
            . "values( null, {$_POST[ 'textbook' ]}, {$_POST[ 'new_author' ]}, $sequence )";
        $insert_result = $db->query( $insert_query );
    }
    
    // Was an author removed from a textbook?
    if( isset( $_POST[ 'remove_author' ] ) ) {
        $remove_query = 'delete from textbook_x_author '
            . "where id = {$_POST[ 'remove_author' ]}";
        $remove_result = $db->query( $remove_query );
    }
    
    $all_authors_query = 'select id, first, middle, last from authors order by last, first, middle';
    $all_authors_result = $db->query( $all_authors_query );
    $all_authors = array( );
    $count = 0;
    while( $row = $all_authors_result->fetch_assoc( ) ) {
        foreach( explode( ' ', 'id first middle last' ) as $field ) {
            $all_authors[ $count ][ $field ] = $row[ $field ];
        }
        $count++;
    }
    
    // Was an author moved?
    if( isset( $_POST[ 'move' ] ) ) {
        $moving_author_query = 'select * from textbook_x_author '
            . "where id = {$_POST[ 'author' ]}";
        $moving_author_result = $db->query( $moving_author_query );
        $moving_author_row = $moving_author_result->fetch_assoc( );
        $m_sequence = $moving_author_row[ 'sequence' ];
        
        $displaced_author_query = 'select * from textbook_x_author '
            . "where textbook = {$moving_author_row[ 'textbook' ]} "
            . 'and sequence = '
            . ( $_POST[ 'move' ] == 'up' ? $m_sequence - 1 : $m_sequence + 1 );
        $displaced_author_result = $db->query( $displaced_author_query );
        $displaced_author_row = $displaced_author_result->fetch_assoc( );
        $d_sequence = $displaced_author_row[ 'sequence' ];
        
        $sequence_query_1 = 'update textbook_x_author '
            . "set sequence = $d_sequence where id = {$moving_author_row[ 'id' ]}";
        $sequence_query_2 = 'update textbook_x_author '
            . "set sequence = $m_sequence where id = {$displaced_author_row[ 'id' ]}";
        $db->query( $sequence_query_1 );
        $db->query( $sequence_query_2 );
    }
    
    // Was a new textbook created?
    if( isset( $_POST[ 'title' ] ) ) {
        $title = $db->real_escape_string( trim( $_POST[ 'title' ] ) );
        $subtitle = $db->real_escape_string( trim( $_POST[ 'subtitle' ] ) );
        $isbn = $db->real_escape_string( trim( $_POST[ 'isbn' ] ) );
        $new_textbook_query = 'insert into textbooks( id, title, subtitle, edition, year, isbn, publisher ) '
            . "values( null, \"$title\", \"$subtitle\", {$_POST[ 'edition' ]}, {$_POST[ 'year' ]}, "
            . "\"$isbn\", {$_POST[ 'publisher' ]} )";
        $db->query( $new_textbook_query );
    }
    
    // Was a textbook deleted?
    if( isset( $_POST[ 'remove_textbook' ] ) ) {
        $db->query( "delete from textbook_x_author where textbook = {$_POST[ 'remove_textbook' ]}" );
        $db->query( "delete from textbooks where id = {$_POST[ 'remove_textbook' ]}" );
    }
    
    $textbook_query = 'select t.id, t.title, t.subtitle, t.edition, t.year, t.isbn, '
        . 'p.name, p.url '
        . 'from textbooks as t, publishers as p '
        . 'where t.publisher = p.id '
        . 'order by t.title, t.edition, t.year';
    $textbook_result = $db->query( $textbook_query );
    print "<div id=\"textbooks\">\n";
    
    // Was something edited in place?
    if( isset( $_POST[ 'column' ] ) ) {
	    if( trim( $_POST[ 'update_value' ] ) != trim( $_POST[ 'original_html' ] ) ) {
    		$update_query = "update textbooks set {$_POST[ 'column' ]} = \""
    			. htmlentities( trim( $_POST[ 'update_value' ] ) )
    			. "\" where id = \"{$_POST[ 'element_id' ]}\"";
    		$update_result = $db->query( $update_query );
        }
    }
    
    // Are there any textbooks?
    if( $textbook_result->num_rows == 0 ) {
        print "No textbooks.";
    } else {
        $accordion = 0;
        while( $textbook_row = $textbook_result->fetch_assoc( ) ) {
            print "<h3><a href=\"#\">{$textbook_row[ 'title' ]} ("
                . number_suffix( $textbook_row[ 'edition' ] ) . " edition)</a></h3>\n";
            print "<div class=\"textbook\" id=\"{$textbook_row[ 'id' ]}\" accordion=\"$accordion\">\n";
            print "<a href=\"javascript:void(0)\" class=\"delete_textbook\" accordion=\"$accordion\" "
                . "id=\"{$textbook_row[ 'id' ]}\" title=\"Delete {$textbook_row[ 'title' ]}\">"
                . "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" width=\"16\" /></a>\n";
            print "<span class=\"title\" id=\"{$textbook_row[ 'id' ]}\">{$textbook_row[ 'title' ]}</span>";
            if( $textbook_row[ 'subtitle' ] != '' ) {
                print ": <span class=\"subtitle\" id=\"{$textbook_row[ 'id' ]}\">{$textbook_row[ 'subtitle' ]}</span>";
            }
            print ".\n";
            print "<span class=\"edition\" id=\"{$textbook_row[ 'id' ]}\">" . number_suffix( $textbook_row[ 'edition' ] ) . "</span> edition,\n";
            print "<span class=\"year\" id=\"{$textbook_row[ 'id' ]}\">{$textbook_row[ 'year' ]}</span>.\n";
            print "ISBN <span class=\"isbn\" id=\"{$textbook_row[ 'id' ]}\">{$textbook_row[ 'isbn' ]}</span>.\n";
            print "<div class=\"authors\" id=\"{$textbook_row[ 'id' ]}\">Authors:\n";
            $authors_query = 'select a.id as author_id, a.first, a.middle, '
                . 'a.last, a.email, a.url, '
                . 'x.id as x_id, x.sequence '
                . 'from authors as a, textbook_x_author as x '
                . 'where x.author = a.id '
                . "and x.textbook = {$textbook_row[ 'id' ]} "
                . 'order by x.sequence, a.last, a.first, a.middle';
            $authors_result = $db->query( $authors_query );

            print "<ul id=\"{$textbook_row[ 'id' ]}\">\n";

            if( $authors_result->num_rows == 0 ) {
                print "None.";
            } else {
                $used_authors = array( );
                $count = 1;
                while( $authors_row = $authors_result->fetch_assoc( ) ) {
                    print "<li class=\"author\" id=\"{$authors_row[ 'x_id' ]}\">"
                    . "<a href=\"javascript:void(0)\" class=\"remove_author\" accordion=\"$accordion\" "
                    . "id=\"{$authors_row[ 'x_id' ]}\" title=\"Remove {$authors_row[ 'last' ]} from {$textbook_row[ 'title' ]}\">"
                    . "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" width=\"16\" /></a>\n";
                    print "{$authors_row[ 'first' ]} ";
                    if( $authors_row[ 'middle' ] != '' ) {
                        print $authors_row[ 'middle' ] . ' ';
                    }
                    print "{$authors_row[ 'last' ]}\n";
    
                    if( $count > 1 ) {
                        print "<a href=\"javascript:void(0)\" class=\"move_up\" "
                            . "id=\"{$authors_row[ 'x_id' ]}\" accordion=\"$accordion\" "
                            . "title=\"Move {$authors_row[ 'first' ]} ";
                        if( $authors_row[ 'middle' ] != '' ) {
                            print $authors_row[ 'middle' ] . ' ';
                        }
                        print "{$authors_row[ 'last' ]} up\">"
                            . "<img src=\"$docroot/images/silk_icons/arrow_up.png\" "
                            . "height=\"16\" width=\"16\" /></a>";
                    }
                    if( $count < $authors_result->num_rows ) {
                        print "<a href=\"javascript:void(0)\" class=\"move_down\" "
                            . "id=\"{$authors_row[ 'x_id' ]}\" accordion=\"$accordion\" "
                            . "title=\"Move {$authors_row[ 'first' ]} ";
                        if( $authors_row[ 'middle' ] != '' ) {
                            print $authors_row[ 'middle' ] . ' ';
                        }
                        print "{$authors_row[ 'last' ]} down\">"
                            . "<img src=\"$docroot/images/silk_icons/arrow_down.png\" "
                            . "height=\"16\" width=\"16\" /></a>";
                    }
    
                    print "</li>\n";
                    $used_authors[ ] = $authors_row[ 'author_id' ];
                    $count++;
                }
                $authors_result->close( );
                
            }
            print "<li><img src=\"$docroot/images/silk_icons/add.png\" height=\"16\" width=\"16\" />\n";
            print "<select class=\"new_author\" id=\"{$textbook_row[ 'id' ]}\" "
                . "textbook=\"{$textbook_row[ 'id' ]}\" accordion=\"$accordion\">\n";
            print "<option value=\"0\">Add an author</option>\n";
            foreach( $all_authors as $author ) {
                if( array_search( $author[ 'id' ], $used_authors ) === FALSE ) {
                    print "<option value=\"{$author[ 'id' ]}\">{$author[ 'first' ]} ";
                    if( $author[ 'middle' ] != '' ) {
                        print $author[ 'middle' ] . ' ';
                    }
                    print "{$author[ 'last' ]}</option>\n";
                }
            }
            print "</select>\n";
            print "</ul>\n";
            print "</div> <!-- div.authors#{$textbook_row[ 'id' ]} -->\n";
            
            print "</div>  <!-- div.textbook#{$textbook_row[ 'id' ]} -->\n";
            $accordion++;
        }
    } // if there were textbooks
    print "</div>  <!-- div#textbooks -->\n";
?>
<script type="text/javascript">

$(document).ready(function(){
    $("div#textbooks").accordion({
        active: <?php echo isset( $_POST[ 'accordion' ] ) ? $_POST[ 'accordion' ] : 'false'; ?>,
        autoHeight: false,
        collapsible: true
    });
    
    $("select.new_author").change(function(){
        var accordion = $(this).attr("accordion");
        var textbook = $(this).attr("id");
        var author = $(this).attr("value");
        $.post('list_textbooks.php',
            { accordion: accordion, textbook: textbook, new_author: author },
            function(data){
                $("div#current_textbooks").html(data);
            }
        )
    })
    
    $("a.remove_author").click(function(){
        var accordion = $(this).attr("accordion");
        var x = $(this).attr("id");
        $.post( 'list_textbooks.php',
        { accordion: accordion, remove_author: x },
        function(data){
            $("div#current_textbooks").html(data);
        })
    })
    
    $("a.move_up").click(function(){
        var accordion = $(this).attr("accordion");
        var author = $(this).attr("id");
        $.post('list_textbooks.php',
        { accordion: accordion, move: "up", author: author },
        function(data){
            $("div#current_textbooks").html(data);
        })
    })
    
    $("a.move_down").click(function(){
        var accordion = $(this).attr("accordion");
        var author = $(this).attr("id");
        $.post('list_textbooks.php',
            { accordion: accordion, move: "down", author: author },
            function(data){
                $("div#current_textbooks").html(data);
        })
    })
    
    $("a.delete_textbook").click(function(){
        var id = $(this).attr("id");
        $.post( 'list_textbooks.php',
            { remove_textbook: id },
            function(data){
                $("div#current_textbooks").html(data);
        })
    })
    
    $("span.title").editInPlace({
		url: "update_textbook.php",
		params: "ajax=yes&column=title",
		saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
	});
    
    $("span.subtitle").editInPlace({
		url: "update_textbook.php",
		params: "ajax=yes&column=subtitle",
		saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
	});
    
    $("span.edition").editInPlace({
        url: 'update_textbook.php',
        params: 'ajax=yes&column=edition',
        field_type: 'select',
        select_options: '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20'
    });
    
    $("span.year").editInPlace({
		url: "update_textbook.php",
		params: "ajax=yes&column=year",
		saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
	});
    
    $("span.isbn").editInPlace({
		url: "update_textbook.php",
		params: "ajax=yes&column=isbn",
		saving_image: "<?php echo $docroot; ?>/images/ajax-loader.gif"
	});
    
})

</script>
<?php

} else {
    print $no_admin;
}
   
?>
