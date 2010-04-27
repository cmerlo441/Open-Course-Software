<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    $comment = $db->real_escape_string( trim( $_POST[ 'comment' ] ) );
    $submission = $db->real_escape_string( $_POST[ 'submission' ] );
    
    $db->query( 'lock tables submission_comments' );
    $add_comment_query = 'insert into submission_comments '
        . '( `id`, `submission_id`, `who`, `when`, `comment` ) '
        . "values( null, $submission, {$_SESSION[ 'student' ]}, "
        . '"' . date( 'Y-m-d H:i:s' ) . '", '
        . "\"$comment\" )";
    $add_comment_result = $db->query( $add_comment_query );

    $comments_query = 'select * from submission_comments '
        . "where `submission_id` = $submission "
        . 'order by `when`';
    $db->query( 'unlock tables' );
    $comments_result = $db->query( $comments_query );
    if( $comments_result->num_rows == 0 ) {
        print "None.\n";
    } else {
        while( $comment_row = $comments_result->fetch_assoc( ) ) {
            $whose_comment = $comment_row[ 'who' ] == 0 ? 'prof' : 'student';
            print "<div class=\"{$whose_comment}_comment\" id=\"{$comment_row[ 'id' ]}\">";
            print "<div class=\"who\">By "
                . ( $whose_comment == 'prof' ? "Prof. {$prof[ 'last' ]}" : $_SESSION[ 'name' ] )
                . "</div>\n";
            print "<div class=\"when\">On "
                . date( 'l, F j \a\t g:i a', strtotime( $comment_row[ 'when' ] ) )
                . "</div>\n";
            print "<div class=\"comment\">"
                . wordwrap( nl2br( stripslashes( $comment_row[ 'comment' ] ) ) )
                . "</div>  <!-- div.{$whose_comment}_comment -->\n";
            print "</div>\n";
        }
    }
}
?>
