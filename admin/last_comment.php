<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
	
	$query = 'select sc.when, sc.comment, '
		. 'c.dept, c.course, s.section, '
		. 'st.first, st.last, '
		. 'a.id as assignment_id, a.title, a.due_date '
		
		. 'from submission_comments as sc, '
		. 'assignment_submissions as subs, '
		. 'assignments as a, '
		. 'students as st, '
		. 'courses as c, sections as s '
		
		. 'where sc.submission_id = subs.id '
		. 'and a.id = subs.assignment '

		. 'and a.section = s.id '
                . 'and s.course = c.id '
		
		. 'and sc.who = st.id '
		
		. 'order by sc.when desc limit 1';
	
	$result = $db->query( $query );

	if( $result->num_rows >= 1 ) {

	    $comment = $result->fetch_object( );
	    //	print_r( $comment );

	    print "<div class=\"dialog\" id=\"comment_text\">"
		. nl2br( $comment->comment ) . "</div>\n";

	    print "<p style=\"text-align: center\">"
		. "<span style=\"font-weight: bold\">Latest comment</span> "
		. "(<a href=\"javascript:void(0)\" id=\"hide_latest_comment\">"
		. "Hide this)</p>\n";

	    print "<div class=\"student_comment\" id=\"$comment->comment_id\">";
	    print "<div class=\"who\">By $comment->first $comment->last ($comment->dept $comment->course $comment->section)</div>\n";
	    print "<div class=\"on\">On <a href=\"$admin/assignment.php?assignment=$comment->assignment_id\">"
		. "$comment->title</a> (Due "
		. date( 'M. jS', strtotime( $comment->due_date ) )
		. ")</div>\n";

	    print "<div class=\"when\">On "
		. date( 'l, F j \a\t g:i a', strtotime( $comment->when ) )
		. "</div>\n";
	    /*
	     print "<div class=\"comment\">"
	     . wordwrap( nl2br( stripslashes( $comment->comment ) ) )
	     . "</div>\n";
	    */

	    print "</div>\n";

	    print "<a href=\"javascript:void(0)\" id=\"read_comment\">Read this comment</a> | ";
	    print_link( "$admin/comments.php", 'See all comments' );
?>

<script type="text/javascript">
$(document).ready(function(){
    $('a#hide_latest_comment').click(function(){
       $('div#last_comment').fadeOut(500);
    })

    $('a#read_comment').click(function(){
        $('div#comment_text').dialog({
            modal: true,
            width: 500,
            title: 'Comment from <?php echo "$comment->first $comment->last"; ?>',
            buttons: {
                'OK': function(){
                    $('div#comment_text').dialog('destroy');
                }
            }
        });
    })
})
</script>

<?php
    }
}
?>
