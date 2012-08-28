<?php

$no_header = 1;
require_once( './_header.inc' );

function print_textbooks( $db, $course, $required = 1 ) {
    $textbook_query = 'select t.id, t.title, t.edition, t.year, '
        . 't.publisher, t.isbn '
        . 'from textbooks as t, course_x_textbook as x '
        . "where x.course = $course "
        . 'and x.textbook = t.id '
        . "and x.required = $required "
        . 'order by t.title, t.edition, t.year';
    $textbook_result = $db->query( $textbook_query );
    if( $textbook_result->num_rows > 0 ) {
        print "<p id=\""
            . ( $required == 1 ? 'required' : 'recommended' )
            . "_textbooks\"><b>"
            . ( $required == 1 ? 'Required' : 'Recommended' ) . ' Textbook';
        if( $textbook_result->num_rows != 1 ) {
            print 's';
        }
        print ":</b>\n";
        print "<ul>\n";
        while( $book = $textbook_result->fetch_assoc( ) ) {
            print "<li><b>{$book[ 'title' ]}.</b>  ";
            $authors_query = 'select a.first, a.middle, a.last, a.email, '
                . 'a.url '
                . 'from authors as a, textbook_x_author as x '
                . "where x.textbook = {$book[ 'id' ]} "
                . 'and x.author = a.id '
                . 'order by x.sequence';
            $authors_result = $db->query( $authors_query );
            print_authors( $db, $authors_result );
            print '  ';
            print number_suffix( $book[ 'edition' ] ) . ' edition, '
                . $book[ 'year' ] . ".  ISBN " . $book[ 'isbn' ] . ".\n";
            
        }
        print "</ul>\n";
    }
}   // print_textbooks

function print_authors( $db, $authors_result ) {
    if( $authors_result->num_rows == 1 ) {
        $author = $authors_result->fetch_assoc( );
        if( $author[ 'url' ] != '' ) {
            print "<a href=\"{$author[ 'url' ]}\">";
        }
        print "{$author[ 'last' ]}, {$author[ 'first' ]}";
        if( $author[ 'middle' ] != '' ) {
            print ' ' . $author[ 'middle' ];
        }
        if( $author[ 'url' ] != '' ) {
            print "</a>";
        }
        if( $author[ 'email' ] != '' ) {
            print ' <';
            print_email( $author[ 'email' ] );
            print '>';
        }
        print ".";
    } else if( $authors_result->num_rows == 2 or
               $authors_result->num_rows > 3 ) {
        $a1 = $authors_result->fetch_assoc( );
        $a2 = $authors_result->fetch_assoc( );

        if( $a1[ 'url' ] != '' ) {
            print "<a href=\"{$author[ 'url' ]}\">";
        }
        print "{$a1[ 'last' ]}, {$a1[ 'first' ]}";
        if( $a1[ 'middle' ] != '' ) {
            print ' ' . $a1[ 'middle' ];
        }
        if( $a1[ 'url' ] != '' ) {
            print '</a>';
        }
        if( $a1[ 'email' ] != '' ) {
            print ' <';
            print_email( $a1[ 'email' ] );
            print '>';
        }
        
        print " &amp; ";
        
        if( $a2[ 'url' ] != '' ) {
            print "<a href=\"{$author[ 'url' ]}\">";
        }
        print $a2[ 'first' ] . ' ';
        if( $a2[ 'middle' ] != '' ) {
            print $a2[ 'middle' ] . ' ';
        }
        print $a2[ 'last' ];
        if( $a2[ 'url' ] != '' ) {
            print '</a>';
        }
        if( $a2[ 'email' ] != '' ) {
            print ' <';
            print_email( $a2[ 'email' ] );
            print '>';
        }
        
        if( $authors_result->num_rows > 3 ) {
            print ' et al.';
        }
        
    } else if( $authors_result->num_rows == 3 ) {
        $a1 = $authors_result->fetch_assoc( );
        $a2 = $authors_result->fetch_assoc( );
        $a3 = $authors_result->fetch_assoc( );
        
        if( $a1[ 'url' ] != '' ) {
            print "<a href=\"{$author[ 'url' ]}\">";
        }
        print "{$a1[ 'last' ]}, {$a1[ 'first' ]}";
        if( $a1[ 'middle' ] != '' ) {
            print ' ' . $a1[ 'middle' ];
        }
        if( $a1[ 'url' ] != '' ) {
            print '</a>';
        }
        if( $a1[ 'email' ] != '' ) {
            print ' <';
            print_email( $a1[ 'email' ] );
            print '>';
        }
        
        print ", ";
        
        if( $a2[ 'url' ] != '' ) {
            print "<a href=\"{$author[ 'url' ]}\">";
        }
        print $a2[ 'first' ] . ' ';
        if( $a2[ 'middle' ] != '' ) {
            print $a2[ 'middle' ] . ' ';
        }
        print $a2[ 'last' ];
        if( $a2[ 'url' ] != '' ) {
            print '</a>';
        }
        if( $a2[ 'email' ] != '' ) {
            print ' <';
            print_email( $a2[ 'email' ] );
            print '>';
        }
        
        print ", &amp; ";
        
        if( $a3[ 'url' ] != '' ) {
            print "<a href=\"{$author[ 'url' ]}\">";
        }
        print $a3[ 'first' ] . ' ';
        if( $a3[ 'middle' ] != '' ) {
            print $a3[ 'middle' ] . ' ';
        }
        print $a3[ 'last' ];
        if( $a3[ 'url' ] != '' ) {
            print '</a>';
        }
        if( $a3[ 'email' ] != '' ) {
            print ' <';
            print_email( $a3[ 'email' ] );
            print '>';
        }
        
    }
}   // print_authors

$course_id = $_REQUEST[ 'course' ] == ''
  ? 0
  : $db->real_escape_string( $_REQUEST[ 'course' ] );

$section_id = $_REQUEST[ 'section' ] == ''
  ? 0
  : $db->real_escape_string( $_REQUEST[ 'section' ] );

if( $section_id > 0 ) {
    $course_query = 'select c.id as course_id, c.dept, c.course, c.credits, '
      . 'c.long_name, c.prereq, c.catalog, c.outline, '
      . 's.id as section_id, s.section, s.banner, s.day '
      . 'from courses as c, sections as s '
      . 'where s.course = c.id '
      . 'and s.id = ' . $db->real_escape_string( $_POST[ 'section' ] );
    $course_result = $db->query( $course_query );
    $course_row = $course_result->fetch_assoc( );
    $course_id = $course_row[ 'course_id' ];
}

 else if( $course_id > 0 ) {
  $course_query = 'select id as course_id, dept, course, credits, '
    . 'long_name, prereq, catalog, outline '
    . 'from courses '
    . "where id = $course_id";
  $course_result = $db->query( $course_query );
  $course_row = $course_result->fetch_assoc( );
}

else {
    die( 'No course selected' );
}

$course_name = $course_row[ 'dept' ] . ' ' . $course_row[ 'course' ];
if( isset( $_POST[ 'section' ] ) ) {
    $course_name .= ' ' . $course_row[ 'section' ];
}

?>
<script type="text/javascript">
$(document).ready(function(){
    var course = " :: <?php echo $course_name ?>";
    document.title += course;

    var h1 = $("h1").html();
    $("h1").html(h1 + course);
})
</script>
<?php

$weights_query = 'select t.plural, w.grade_weight '
    . 'from grade_types as t, grade_weights as w '
    . "where w.course = $course_id "
    . 'and w.grade_type = t.id '
    . 'order by w.grade_weight desc';
//print "<pre>$weights_query;</pre>\n";
$weights_result = $db->query( $weights_query );
$weights = array( );
while( $weights_row = $weights_result->fetch_assoc( ) ) {
    $weights[ $weights_row[ 'plural' ] ] = $weights_row[ 'grade_weight' ];
}

$section_query = 'select * from syllabus_sections order by sequence';
$section_result = $db->query( $section_query );
while( $row = $section_result->fetch_assoc( ) ) {
    $field = $row[ 'section' ];
    $value = nl2br( stripslashes( $row[ 'default_value' ] ) );
    $customization_query = 'select * from syllabus_section_customization '
        . "where course = $course_id "
        . "and syllabus_section = {$row[ 'id' ]}";
    $customization_result = $db->query( $customization_query );
    if( $customization_result->num_rows == 1 ) {
        $customization_row = $customization_result->fetch_assoc( );
        $value = nl2br( stripslashes( $customization_row[ 'value' ] ) );
    }
    
    if( $field == 'Instructor' ) {
        print "<p id=\"instructor\"><b>Instructor:</b> {$prof[ 'name' ]}</p>\n";
    }
    
    else if( $field == 'Course' ) {
        print "<p id=\"course\"><b>Course:</b> {$course_row[ 'dept' ]} "
            . "{$course_row[ 'course' ]} {$course_row[ 'section' ]}: "
            . "{$course_row[ 'long_name' ]}</p>\n";
    }
    
    else if( $field == 'Credits' ) {
        print "<p id=\"credits\"><b>Credits:</b> {$course_row[ 'credits' ]}</p>\n";
    }
    
    else if( $field == 'Prerequisites' ) {
        print "<p id=\"prereq\"><b>Prerequisite:</b> {$course_row[ 'prereq' ]}</p>\n";
    }

    else if( $field == 'Schedule' ) {
      if( $section_id > 0 ) {
        print "<p id=\"schedule\"><b>Schedule:</b></p>\n";
        print "<ul>\n";
        
        $days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday',
                       'Friday', 'Saturday', 'Sunday' );
        
        $schedule_query = 'select * from section_meetings '
            . "where section = $section_id "
            . 'order by day, start';
        $schedule_result = $db->query( $schedule_query );
        while( $schedule_row = $schedule_result->fetch_assoc( ) ) {
            print "<li><b>{$days[ $schedule_row[ 'day' ] ]}</b> "
                . date( 'g:i a', strtotime( $schedule_row[ 'start' ] ) ) . ' to '
                . date( 'g:i a', strtotime( $schedule_row[ 'end' ] ) ) . ' in '
                . "{$schedule_row[ 'building' ]} {$schedule_row[ 'room' ]}</li>\n";
        }
        print "</ul>\n";
      } // if there's a section
    }
    
    else if( $field == 'Textbooks' ) {
        print_textbooks( $db, $course_row[ 'course_id' ], 1 );
        print_textbooks( $db, $course_row[ 'course_id' ], 0 );
    }
    
    else if( $field == 'Catalog Description' ) {
    print "<p id=\"catalog\"><b>Catalog Description:</b> "
        . "{$course_row[ 'catalog' ]}</p>\n";
    }
    
    else if( $field == 'Evaluation' ) {
        print "<p id=\"evaluation\"><b>Evaluation:</b> $value</p>\n";
        print "<ul>\n";
        foreach( $weights as $column=>$weight ) {
            if( $weight > 0 ) { print "<li>$column: $weight%"; }
	    print "</li>\n";
        }
        print "</ul>\n";
        
    } else if( $field == 'Course Outline' ) {
        print "<p id=\"course_outline\"><b>Course Outline:</b></br>\n";
        print wordwrap( html_entity_decode( $course_row[ 'outline' ] ) ) . "</p>\n";
    }

	else if( $field == 'Twitter' ) {
		$twitter_query = 'select twitter_username as u from prof';
		$twitter_result = $db->query( $twitter_query );
		if( $twitter_result->num_rows == 1 ) {
			$twitter_row = $twitter_result->fetch_assoc( );
			if( $twitter_row[ 'u' ] != '' ) {
				print "<p id=\"twitter\"><b>Twitter:</b>  The professor maintains a ";
				print_link( 'http://www.twitter.com/', 'Twitter' );
				print ' account with the username ';
				print_link( "http://www.twitter.com/{$twitter_row[ 'u' ]}",
							"@{$twitter_row[ 'u' ]}" );
				print ', to which important information will be posted from '
					. 'time to time, such as new assignments, upcoming exams, '
					. 'etc.  You may create a Twitter account and follow ';
				print_link( "http://www.twitter.com/{$twitter_row[ 'u' ]}",
							"@{$twitter_row[ 'u' ]}" );
				print ' to receive these updates on your mobile phone.  ';
				print "</p>\n";
			}
		}
	}
    
    else if( $field == 'Computer Center' ) {
        if( $course_row[ 'dept' ] == 'CMP' or $course_row[ 'dept' ] == 'CSC' or $course_row[ 'dept' ] == 'ITE' ) {
            print "<p id=\"computer_center\"><b>Computer Center:</b> $value</p>\n";
        }
    }
    
    else if( $field == 'Math Success Center' ) {
        if( $course_row[ 'dept' ] == 'MAT' and $course_row[ 'course' ] < 100 ) {
            print "<p id=\"math_success_center\"><b>Math Success Center:</b> $value</p>\n";
        }
    }
    
    else if( $field == 'Math Center' ) {
        if( $course_row[ 'dept' ] == 'MAT' and $course_row[ 'course' ] >= 100 ) {
            print "<p id=\"math_center\"><b>Math Center:</b> $value</p>\n";
        }
    }
    
    else {
        $p_id = strtolower( str_replace( ' ', '_', $field ) );
        
        if( preg_match( '/Project/', $field ) == 1 ) {
            if( array_key_exists( 'Projects', $weights ) ) {
                print "<p id=\"$p_id\"><b>$field:</b> $value</p>\n";
            }
        } else if( preg_match( '/Lab/', $field ) == 1 ) {
            if( array_key_exists( 'Lab Assignments', $weights ) ) {
                print "<p id=\"$p_id\"><b>$field:</b> $value</p>\n";
            }
        } else if( preg_match( '/Exams/', $field ) == 1 ) {
            if( array_key_exists( 'Exams', $weights ) ) {
                print "<p id=\"$p_id\"><b>$field:</b> $value</p>\n";
            }
        } else if( preg_match( '/Quiz/', $field ) == 1 ) {
            if( array_key_exists( 'Quizzes', $weights ) ) {
                print "<p id=\"$p_id\"><b>$field:</b> $value</p>\n";
            }
        } else if( preg_match( '/Discuss/', $field ) == 1 ) {
            if( array_key_exists( 'Online Discussion', $weights ) ) {
                print "<p id=\"$p_id\"><b>$field:</b> $value</p>\n";
            }
        } else if( preg_match( '/Homework/', $field ) == 1 ) {
            if( array_key_exists( 'Homework', $weights ) ) {
                print "<p id=\"$p_id\"><b>$field:</b> $value</p>\n";
            }
        }
        
        // Do something with customized data

        else {
            print "<p id=\"$p_id\"><b>$field:</b> $value</p>\n";
        }
    }

}   // while

?>
