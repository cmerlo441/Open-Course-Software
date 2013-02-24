<?php

$title_stub = 'Student Comments';
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    $comments_query = 'select c.id as comment_id, c.submission_id as sub_id, '
        . 'c.when, c.comment, '
        
        . 'a.id as assignment_id, a.due_date as due_date, a.title as title, '
        
        . 'gt.grade_type as type, '
        
        . 'co.dept, co.course, s.section, '
        
        . 'st.first, st.last '
        
        . 'from assignments as a, submission_comments as c, assignment_submissions as subs, '
        . 'courses as co, sections as s, students as st, grade_types as gt '
        
        . 'where c.who = st.id '
        
        . 'and a.grade_type = gt.id '
        
        . 'and c.submission_id = subs.id '
        . 'and subs.assignment = a.id '
        . 'and a.section = s.id '
        . 'and s.course = co.id '
        
        . 'and c.who > 0 '
        
        . 'order by co.dept, co.course, s.section, a.due_date, a.id, st.last, st.first ';
    
    $comments_result = $db->query( $comments_query );
    
    $section = '';
    
    while( $comment = $comments_result->fetch_object( ) ) {
        
        $temp_section = "$comment->dept $comment->course $comment->section";
        if( $temp_section != $section ) {
            $section = $temp_section;
            print "<h2>$section</h2>\n";
            $title = '';
        }
        
        if( $title != $comment->title ) {
            $title = $comment->title;
            print "<h3><a href=\"assignment.php?assignment=$comment->assignment_id\">"
                . "$title</a> (Due "
                . date( 'M. jS', strtotime( $comment->due_date ) )
                . ")</h3>\n";
        }

        print "<div class=\"student_comment\" id=\"$comment->comment_id\">";
        print "<div class=\"who\">By $comment->first $comment->last</div>\n";
        print "<div class=\"when\">On "
            . date( 'l, F j \a\t g:i a', strtotime( $comment->when ) )
            . "</div>\n";
        print "<div class=\"comment\">"
            . wordwrap( nl2br( stripslashes( $comment->comment ) ) )
            . "</div>\n";
        print "</div>\n";
        

/*        
        print "<pre>\n";
        print_r( $comment );
        print "</pre>\n";
 */
    }
}

?>