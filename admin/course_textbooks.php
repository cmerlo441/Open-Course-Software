<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    if( isset( $_POST[ 'remove' ] ) ) {
        $remove_query = 'delete from course_x_textbook '
            . "where course = " . $db->real_escape_string( $_POST[ 'course' ] )
            . " and textbook = " . $db->real_escape_string( $_POST[ 'remove' ] );
        $remove_result = $db->query( $remove_query );
    }
    
    // Add a textbook to the course
    
    else if( isset( $_POST[ 'textbook' ] ) ) {
        $textbook_query = 'insert into course_x_textbook( id, course, textbook, required ) '
            . "values( null, \""
            . $db->real_escape_string( $_POST[ 'course' ] ) . "\", "
            . "\"" . $db->real_escape_string( $_POST[ 'textbook' ] ) . "\", 1 )";
        $textbook_result = $db->query( $textbook_query );
    }
    
    $textbook_query = 'select t.id, t.title, t.edition, t.year, t.isbn, '
        . 'p.name, p.url '
        . 'from textbooks as t, course_x_textbook as x, publishers as p '
        . 'where x.course = ' . $db->real_escape_string( $_POST[ 'course' ] )
        . ' and x.textbook = t.id '
        . 'and t.publisher = p.id '
        . 'order by t.title, t.year, t.edition ';
    $textbook_result = $db->query( $textbook_query );
    if( $textbook_result->num_rows == 0 ) {
        print "None.<br />\n";
    } else {
        print "<ul>";
        while( $row = $textbook_result->fetch_assoc( ) ) {
            print "<li>";
            
            print "<a class=\"remove\" id=\"{$row[ 'id' ]}\" "
                . "href=\"javascript:void(0)\">"
                . "<img src=\"$docroot/images/silk_icons/cancel.png\" height=\"16\" "
                . "width=\"16\" title=\"Remove {$row[ 'title' ]}\" /></a>\n";
            
            print "<b>{$row[ 'title' ]}</b>.  "
                . number_suffix( $row[ 'edition' ] ) . ' edition, '
                . "{$row[ 'year' ]}.  ";

            $authors_query = 'select a.id, a.first, a.middle, a.last, a.email, '
                . 'a.url, x.sequence '
                . 'from authors as a, textbook_x_author as x '
                . "where x.textbook = {$row[ 'id' ]} "
                . 'and x.author = a.id '
                . 'order by x.sequence';
            $authors_result = $db->query( $authors_query );

            if( $authors_result->num_rows == 1 ) {
                $author = $authors_result->fetch_assoc( );
                print "{$author[ 'last' ]}, {$author[ 'first' ]}";
                if( $author[ 'middle' ] != '' ) {
                    print ' ' . $author[ 'middle' ];
                }
                print ".";
            } else if( $authors_result->num_rows == 2 or
                       $authors_result->num_rows > 3 ) {
                $a1 = $authors_result->fetch_assoc( );
                $a2 = $authors_result->fetch_assoc( );
                print "{$a1[ 'last' ]}, {$a1[ 'first' ]}";
                if( $a1[ 'middle' ] != '' ) {
                    print ' ' . $a1[ 'middle' ];
                }
                print " &amp; {$a2[ 'first' ]}";
                if( $a2[ 'middle' ] != '' ) {
                    print ' ' . $a2[ 'middle' ];
                }
                print $a2[ 'last' ];
                if( $authors_result->num_rows > 3 ) {
                    print ' et al.';
                }
            } else if( $authors_result->num_rows == 3 ) {
                $a1 = $authors_result->fetch_assoc( );
                $a2 = $authors_result->fetch_assoc( );
                $a3 = $authors_result->fetch_assoc( );
                print "{$a1[ 'last' ]}, {$a1[ 'first' ]}";
                if( $a1[ 'middle' ] != '' ) {
                    print ' ' . $a1[ 'middle' ];
                }
                print ", {$a2[ 'first' ]}";
                if( $a2[ 'middle' ] != '' ) {
                    print ' ' . $a2[ 'middle' ];
                }
                print ' ' . $a2[ 'last' ];
                print ", &amp; {$a3[ 'first' ]}";
                if( $a3[ 'middle' ] != '' ) {
                    print ' ' . $a3[ 'middle' ];
                }
                print ' ' . $a3[ 'last' ];
            }

            print "</li>\n";
        }
        print "</ul>\n";
    }

    print "<select id=\"new_textbook\">\n";
    print "<option id=\"0\">Add a textbook</option>\n";    
    $all_textbooks_query = 'select * from textbooks '
        . 'order by title, year, edition ';
    $all_textbooks_result = $db->query( $all_textbooks_query );
    while( $book = $all_textbooks_result->fetch_assoc( ) ) {
        print "<option value=\"{$book[ 'id' ]}\">{$book[ 'title' ]}, "
            . "{$book[ 'year' ]} (";
        $authors_query = 'select a.last, x.sequence '
            . 'from authors as a, textbook_x_author as x '
            . "where x.textbook = {$book[ 'id' ]} "
            . 'and x.author = a.id '
            . 'order by x.sequence';
        $authors_result = $db->query( $authors_query );
        if( $authors_result->num_rows == 1 ) {
            $author = $authors_result->fetch_assoc( );
            print $author[ 'last' ] . ')';
        } else if( $authors_result->num_rows == 2 or
                   $authors_result->num_rows > 3 ) {
            $a1 = $authors_result->fetch_assoc( );
            $a2 = $authors_result->fetch_assoc( );
            print "{$a1[ 'last' ]} &amp; {$a2[ 'last' ]}";
            if( $authors_result->num_rows > 3 ) {
                print ' et al.';
            }
            print ')';
        } else if( $authors_result->num_rows == 3 ) {
            $a1 = $authors_result->fetch_assoc( );
            $a2 = $authors_result->fetch_assoc( );
            $a3 = $authors_result->fetch_assoc( );
            print "{$a1[ 'last' ]}, {$a2[ 'last' ]}, &amp; {$a3[ 'last' ]})";
        }
        print "</option>\n";
    }
    print "</select>\n";
?>
<script type="text/javascript">
$(document).ready(function(){
    $("a.remove").click(function(){
        var textbook = $(this).attr("id");
        var course = "<?php echo $_POST[ 'course' ]; ?>";
        $.post( 'course_textbooks.php',
            { course: course, remove: textbook },
            function(data){
                $("div#textbooks").html(data);
            }
        )
    })

    $("select#new_textbook").change(function(){
        var textbook = $(this).val();
        var course = "<?php echo $_POST[ 'course' ]; ?>";
        $.post( 'course_textbooks.php',
            { course: course, textbook: textbook },
            function(data){
                $("div#textbooks").html(data);
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
