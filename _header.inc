<?php

/* This is always the first PHP code executed by any script.  Make
 * sure we connect to the database, set up any variables that will be
 * needed later, and display the top of the page (unless told not to).
 */

function array_debug( $array ) {
    print "<pre>";
    print_r( $array );
    print "</pre>\n";
}

function name( $person ) {
    $name = ucwords( $person[ 'first' ] ) . ' ';
    if( $person[ 'middle' ] != '' ) {
        $name .= ucwords( $person[ 'middle' ] );
    }
    if( strlen( $person[ 'middle' ] ) == 1 )
	$name .= '.';
    $name .= ' ' . ucwords( $person[ 'last' ] );
    return $name;
}

function lastfirst( $person ) {
    $name = ucwords( $person[ 'last' ] ) . ', '
        . ucwords( $person[ 'first' ] );
    if( $person[ 'middle' ] != '' ) {
        $name .= ' ' . ucwords( $person[ 'middle' ] );
    }
    if( strlen( $person[ 'middle' ] ) == 1 )
	$name .= '.';
    return $name;
}
 
function convert_smart_quotes($string) 
{ 
    $search = array(chr(145), 
                    chr(146), 
                    chr(147), 
                    chr(148), 
                    chr(151),
                    "%u2013",
                    "%u2014",
                    "%u2018",
                    "%u2019",
                    "%u201C",
                    "%u201D"); 
 
    $replace = array("'", 
                     "'", 
                     '"', 
                     '"', 
                     '-',
                     '-',
                     '--',
                     "'",
                     "'",
                     '"',
                     '"'); 
 
    return str_replace($search, $replace, $string); 
}

function browser( $browser ) {
    if( preg_match('|Firefox/([0-9.]+)|', $browser, $matches ) == 1 ) {
        return "Firefox {$matches[ 1 ]}";
    } else if( preg_match( '|Opera.*Version/([0-9.]+)|', $browser, $matches ) == 1 ) {
        return "Opera {$matches[ 1 ]}";
    } else if( preg_match( '|Konqueror/([0-9.]+)|', $browser, $matches ) == 1 ) {
        return "Konqueror {$matches[ 1 ]}";
    } else if( preg_match( '|Galeon/([0-9.]+)|', $browser, $matches ) == 1 ) {
        return "Galeon {$matches[ 1 ]}";
    } else if( preg_match( '|SeaMonkey/([0-9.]+)|', $browser, $matches ) == 1 ) {
        return "Seamonkey {$matches[ 1 ]}";
    } else if( preg_match( '|Chrome/([0-9.]+)|', $browser, $matches ) == 1 ) {
        return "Chrome {$matches[ 1 ]}";
    } else if( preg_match( '|AppleWebKit/([0-9.]+)|', $browser, $matches ) == 1 ) {
        $string = 'Safari ';
        if( preg_match( '/526/', $matches[ 1 ] ) ) {
            return '4.0dp1';
        } else if( preg_match( '/531.9/', $matches[ 1 ] ) ) {
            $string .= '4.0.3';
        } else if( preg_match( '/530.19/', $matches[ 1 ] ) ) {
            $string .= '4.0.2';
        } else if( preg_match( '/530.18/', $matches[ 1 ] ) ) {
            $string .= '4.0.1';
        } else if( preg_match( '/528.16/', $matches[ 1 ] ) ) {
            $string .= '4.0';
        } else if( preg_match( '/525.29|525.28.3/', $matches[ 1 ] ) ) {
            $string .= '3.2.3';
        } else if( preg_match( '/525.28.1/', $matches[ 1 ] ) ) {
            $string .= '3.2.2';
        } else if( preg_match( '/525.27.1/', $matches[ 1 ] ) ) {
            $string .= '3.2.1';
        } else if( preg_match( '/525.26.13/', $matches[ 1 ] ) ) {
            $string .= '3.2';
        } else if( preg_match( '/525.21|525.20.1/', $matches[ 1 ] ) ) {
            $string .= '3.1.2';
        } else if( preg_match( '/525.17|525.20|525.18/', $matches[ 1 ] ) ) {
            $string .= '3.1.1';
        } else if( preg_match( '/525/', $matches[ 1 ] ) ) {
            $string .= '3.1';
        }
        return $string;
    } else {
        return 'Unknown Browser';
    }
}

function os( $os ) {
    if( preg_match( '/(Mac OS X)/', $os, $matches ) == 1 ) {
        return $matches[ 1 ];
    } else if( preg_match( '|Ubuntu/?([0-9.]+)?|i', $os, $matches ) == 1 ) {
        return "Ubuntu {$matches[ 1 ]}";
    } else if( preg_match( '/Fedora/', $os, $matches ) == 1 ) {
        return "Fedora Linux {$matches[ 1 ]}";
    } else if( preg_match( '/Linux/', $os ) == 1 ) {
        return 'Linux';
    } else if( preg_match( '/Windows NT 5.0/', $os, $matches ) == 1 ) {
        return 'Windows 2000';
    } else if( preg_match( '/Windows NT 5.1/', $os, $matches ) == 1 ) {
        return 'Windows XP';
    } else if( preg_match( '/Windows NT 6.0/', $os, $matches ) == 1 ) {
        return 'Windows Vista';
    } else if( preg_match( '/Windows NT 6.1/', $os, $matches ) == 1 ) {
        return 'Windows 7';
    }
}

$cwd = getcwd( );
if( preg_match( '|^(/home/faculty/)(.+)/public_html|', $cwd, $matches ) ) {
    $home_directory = $matches[ 1 ] . $matches[ 2 ];
    $username = $matches[ 2 ];
}
require_once( "$home_directory/.htpasswd" );
$docroot = "/~{$username}";
$fileroot = "$home_directory/public_html";
$admin = "$docroot/admin";
$student = "$docroot/student";
$url = "http://{$_SERVER[ 'SERVER_NAME' ]}{$docroot}/";

$page = $_SERVER[ 'PHP_SELF' ];
$first_get = true;
foreach( $_GET as $k=>$v ) {
    if( ! $first_get ) $get_string .= '&';
    $first_get = false;
    $get_string .= "$k=$v";
}

// DB connection has been made.  Let's get some initial info:

// Professor information
$prof_query = 'select * from prof';
$prof_result = $db->query( $prof_query );

if( $prof_result->num_rows == 0 ) {
    header( 'Location: install.php' );
    print 'Redirecting...';
    die( );
}

$prof = $prof_result->fetch_assoc( );
$prof[ 'name' ] = $prof[ 'first' ] . ' ';
if( trim( $prof[ 'middle' ] ) != '' ) {
  $prof[ 'name' ] .= $prof[ 'middle' ] . ' ';
}
$prof[ 'name' ] .= $prof[ 'last' ];
if( $prof[ 'suffix' ] != 0 ) {
  $suffix_query = 'select suffix from suffixes '
    . "where id = \"{$prof[ 'suffix' ]}\"";
  $suffix_result = $db->query( $suffix_query );
  $suffix_row = $suffix_result->fetch_assoc();
  $suffix_result->close();
  $prof[ 'name' ] .= ', ' . $suffix_row[ 'suffix' ];
}
$prof_result->close();

// Version Information
$version_fh = fopen( "$fileroot/version.txt", 'r' );
$ocsw_version = trim( fgets( $version_fh ) );
fclose( $version_fh );

// Letter grades
function letter_grade( $grade ) {
    $letter_grade_query = 'select letter from letter_grades '
        . "where grade <= $grade "
        . 'order by grade desc limit 1';
    $letter_grade_result = $db->query( $letter_grade_query );
    $letter_grade_row = $letter_grade_result->fetch_assoc( );
    return $letter_grade_row[ 'letter' ];
}

// Snow
$snow = 0;
$snow_query = 'select v from ocsw where k = "snow"';
$snow_result = $db->query( $snow_query );
$snow_row = $snow_result->fetch_assoc( );
$snow_result->close( );
$snow = $snow_row[ 'v' ];

/* This site just plain won't run on IE <= 7. */

/* Updated: It doesn't work on IE 8, either.  */

$ie_query = 'select v from ocsw where k = "block_ie"';
$ie_result = $db->query( $ie_query );
$block_ie = $ie_result->fetch_assoc( );
 
if( $block_ie[ 'v' ] == 1 &&
        preg_match( "/MSIE (\d+)\.(\d+)/", $_SERVER[ 'HTTP_USER_AGENT' ], $matches ) > 0 &&
        $matches[ 1 ] <= 8 ) {
    $release_dates = array( 0, 1995, 1995, 1996, 1997, 1999, 2001, 2006, 2009 );
    
    print "<div style=\"width: 800px; margin: 0 auto;\">\n";
    
    print "<h1>We Apologize For The Inconvenience...</h1>\n";
    
    if( $matches[ 1 ] <= 7 ) {
        print wordwrap( "<p>Hello.  Welcome to Prof. {$prof[ 'last' ]}'s web site.  I'm sorry to have "
         . 'to tell you this, but the web browser you\'re using, called '
         . "Internet Explorer Version {$matches[ 1 ]}.{$matches[ 2 ]}, just "
         . 'isn\'t modern enough to display this site properly.  (Did you '
         . 'know that it was released in ' . $release_dates[ $matches[ 1 ] ]
         . "?)</p>\n\n" );
     } else {
         print wordwrap( "<p>Hello.  Welcome to Prof. {$prof[ 'last' ]}'s web site.  "
             . "I'm sorry to have to tell you this, but the web browser you're using, "
             . "called Internet Explorer Version {$matches[ 1 ]}.{$matches[ 2 ]} "
             . "does not display modern web pages correctly.  Due to the nature of "
             . "the incompatiblities in IE, it's not even possible to log in to "
             . "your student account with this browser.</p>\n\n" );
     }
    
    print wordwrap( '<p>There are some really wonderful modern web browsers '
     . 'available these days, completely for free, which will make your '
     . 'web browsing experience much better, since they display web pages '
     . 'the way they\'re meant to look!  Please try one of these browsers, '
     . "and then come back to this site:</p>\n\n" );
    
    print "<ul>\n";
    print "<li><a href=\"http://www.google.com/chrome\">Chrome</a></li>\n";
    print "<li><a href=\"http://www.firefox.com/\">Firefox</a></li>\n";
    print "<li><a href=\"http://www.flock.com/\">Flock</a> (just like Firefox, "
     . "but with social networking features built in)</li>\n";
    print "<li><a href=\"http://www.opera.com/\">Opera</a></li>\n";
    print "<li><a href=\"http://www.apple.com/safari/\">Safari</a></li>\n";
    print "</ul>\n";
    
    print wordwrap( '<p>I\'m sorry to have to keep you out of the site for now, '
        . 'but once you see the features I have included, I think you\'ll '
        . 'understand why you need to visit with a modern web browser.  Thank '
        . "you for your patience; see you soon.</p>" );
        
    print "<p>";
    print_link( 'http://www.ocsw.net/',
                "<img src=\"$docroot/images/blackboard-100x50.png\" height=\"50\" width=\"100\" />" );
    print "</p>\n";

    print "</div>\n";
    
    die();
}

require_once( "$fileroot/Calendar.inc" );
$this_page = "";

function make_link( $url, $linktext=false, $class="main", $title="" ) {
  return sprintf("<a class=\"%s\" href=\"%s\" title=\"%s\">%s</a>",
		 $class,
		 $url,
		 $title,
		 ($linktext ? $linktext : $url)
		 );
}

// print_link()
// echo a hyperlink to something, within the site
//

function print_link( $url, $linktext=false, $class="main", $title="" ) {
  echo make_link( $url, $linktext, $class, $title );
}

function print_email( $email, $linktext=false, $class="main" ) {
  print_link( "mailto:$email", ( $linktext ? $linktext : $email ), $class );
}

function leading_zeros( $course ) {
  $return_me = "";
  if( $course < 10 ) {
    $return_me = "0";
  }
  if( $course < 100 ) {
    $return_me .= "0";
  }
  $return_me .= $course;
  return( $return_me );
}

function number_suffix( $number ) {
    if( is_numeric( $number ) ) {
        $last_two = $number % 100;
        
        if( $last_two > 3 && $last_two < 21 ) {
            return $number . 'th';
        }
        
        switch( $last_two % 10 ) {
            case '1': return $number . 'st';
            case '2': return $number . 'nd';
            case '3': return $number . 'rd';
            default: return $number . 'th';
        }
    } else {
        return $number;
    }
}

// Login types

$login_type = array( );
/*
$lt_query = "select * from login_type";
$lt_result = $db->query( $lt_query );
while( $row = $lt_result->fetch_assoc( ) ) {
  $login_type[ $row[ 'type' ] ] = $row[ 'id' ];
}
$lt_result->close();
*/

$no_admin = "<div class=\"ui-state-error ui-corner-all\" id=\"no_admin\">\n"
  . "<p>You must be a site administrator to view this page.</p>\n"
  . "</div>  <!-- div#no_admin -->\n";

$no_student = "<div class=\"ui-state-error ui-corner-all\" id=\"no_student\">\n"
  . "<p>You must be logged in as a student to view this page.</p>\n"
  . "</div>  <!-- div#no_student -->\n";

session_name( "ocsw_{$prof[ 'last' ]}_{$prof[ 'first' ]}" );
session_start( );

// Log this page view
if( isset( $_SESSION[ 'student' ] ) and $no_header != 1 ) {

    $page_view_query = 'insert into page_views '
	. '( id, student, page, get_string, datetime, referrer, ip ) '
	. "values( null, {$_SESSION[ 'student' ]}, \"$page\", \"$get_string\", "
	. '"' . date( 'Y-m-d H:i:s' ) . '", '
	. "\"{$_SERVER[ 'HTTP_REFERER' ]}\", \"{$_SERVER[ 'REMOTE_ADDR' ]}\" )";
    $page_view_result = $db->query( $page_view_query );

}

// Set current semester

$semester_result = $db->query( 'select * from semester' );
$semester_row = $semester_result->fetch_assoc( );

$semester = $semester_row[ 'name' ];
$semester_start = $semester_row[ 'start' ];
$semester_end = $semester_row[ 'end' ];
$semester_result->close( );

$title = $prof[ 'name' ];
if( isset( $title_stub ) ) {
	$title .= ' :: ' . $title_stub;
}

// Stuff to do Google Calendar integration

/*
set_include_path( get_include_path( ) . ":$fileroot/ZendGdata-1.11.0/library/" );
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
*/

if( $no_header != 1 ) {

?>

<!DOCTYPE html>
<html lang="en"><head><title><?= $title ?></title>
<meta charset="utf-8" />
<!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo $docroot ?>/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/js/jquery-ui-1.8.8.custom.min.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/jquery.corner.js"></script>
<!-- <script type="text/javascript" src="<?php echo $docroot ?>/jquery.inplace.pack.js"></script> -->
<script type="text/javascript" src="<?php echo $docroot ?>/js/editinplace/src/jquery.editinplace.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/tablesorter/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/jquery.clockpick.1.2.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/markitup/markitup/jquery.markitup.pack.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/markitup/markitup/sets/html/set.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/highlighter/scripts/shCore.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/highlighter/scripts/shBrushJava.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/highlighter/scripts/shBrushJScript.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/highlighter/scripts/shBrushPhp.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/highlighter/scripts/shBrushCpp.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/highlighter/scripts/shBrushPlain.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/js/jquery.uploadify.v2.0.3.min.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/js/swfobject.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/snow.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/js/jquery.pnotify.min.js"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/js/jquery.pnotify.default.css"></script>
<script type="text/javascript" src="<?php echo $docroot ?>/js/jquery.ba-dotimeout.min.js"></script>
<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = "<?php echo $docroot ?>/highlighter/scripts/clipboard.swf";
		SyntaxHighlighter.all();
	</script>
<script type="text/javascript">
  $(document).ready(function(){
    
    // $(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
    
    var admin = <?php echo $_SESSION[ 'admin' ] == 1 ? 1 : 0; ?>;
    var student = <?php echo $_SESSION[ 'student' ] > 0 ? 1 : 0; ?>;
    if( admin == 1 ) {
        $.ajax({
            type: "POST",
            url: "<?php echo $docroot; ?>/admin_tools.php",
            success: function( msg ) {
                $("div#middle div#admin").html( msg ).show();
                $("div#admin div#admin_details").slideUp(1000,function(){
                    $("div#admin div#admin_bottom a#show_hide").html("Show Details");
                });
            }
        })
    }
    
    else if( student == 1 ) {
        $.ajax({
            type: "POST",
            url: "<?php echo $docroot; ?>/student_tools.php",
            success: function( msg ) {
                $("div#middle div#student").html( msg ).show();
                $("div#student div#student_details").slideUp(1000,function(){
                    $("div#student div#student_bottom a#show_hide").html("Show Details");
                });
            }
        })
    }
    
    // else if...

    else {
        $.ajax({
            type: "POST",
            url: "<?php echo $docroot; ?>/not_logged_in_tools.php",
            success: function( msg ) {
                $("div#middle div#not_logged_in").html( msg ).show();

            }
        })
    }
    
    /*
    $(".success").hide();
    $(".failure").hide();
    */
        
    $("div#wrapper_content").fadeIn("100");

    var snow = "<?php echo $snow; ?>";
    if( snow == 1 ) {
        $('div#wrapper').snow();
    }

    $('a#snow').click(function(){
        $('.snowflake').hide();
        $('div#snow').fadeOut();
    })
    
  });
  
</script>

<link rel="stylesheet" href="<?php echo $docroot ?>/style.css" />
<link rel="icon" type="image/png" href="<?php echo $docroot ?>/images/ocsw.favicon.png" />
</head>


<body>

<div id="top_bar">
</div> <!-- div#top_bar -->

<div id="wrapper">
<div id="wrapper_content">

<!-- Begin div#top -->

<div id="top">

<div id="pic">
<!-- This should probably be stored in the DB -->
<!-- <img src="<?= $docroot ?>/images/smiley.png" width="80" height="80" alt="<?= $prof[ 'name' ] ?>" /> -->
</div> <!-- div#pic -->

<div id="prof">
<p id="name"><?= $prof[ 'name' ] ?></p>
<p id="title"><?= $prof[ 'title' ] ?></p>
<p id="department">
<?php
if( $prof[ 'department_url' ] != '' ) {
	print_link( $prof[ 'department_url' ], 'Department of ' . $prof[ 'department' ] );
} else {
	print "<p>Department of ${prof[ 'department' ]}";
}
print "</p>\n";
print "<p id=\"college\">";
if( $prof[ 'college_url' ] != '' ) {
	print_link( $prof[ 'college_url' ], $prof[ 'college_name' ] );
} else {
	print "<p>${prof[ 'college_name' ]}</p>\n";
}
?>
<p id="college_address"><?= $prof[ 'college_address' ] ?></p>
</div> <!-- div#prof -->

<div id="links">
<ul>
<li><? print_link( $docroot, 'Home' ) ?></li>
<li><? print_link( "$docroot/schedule.php", 'Schedule' ) ?></li>
<li><? print_link( "$docroot/contact.php", 'Contact Information' ) ?></li>
</ul>
</div> <!-- div#links -->

</div> <!-- div#top -->

<!-- Begin div#middle -->

<div id="middle">
    
<!-- Begin div#left -->

<div id="left">

<?php if( $snow == 1 ) {
?>
<div id="snow" style="text-align: center; border: 1px solid white; padding: 0.5em"><a href="javascript:void(0)" id="snow">Turn off the snow</a></div>
<?php
}
?>

<div id="admin"></div>  <!-- div#admin -->

<div id="student"></div>  <!-- div#student -->

<div id="ta"></div>  <!-- div#ta -->

<div id="not_logged_in"></div>  <!-- div#not_logged_in -->

<div id="create_account"></div> <!-- div#create_account -->

<!-- Begin div#calendars -->

<div id="calendars">

<div id="time">
<?php echo date( 'l, F j Y g:i a' ); ?>
</div> <!-- div#time -->

<?php

$this_month = new Calendar( date( 'n' ), date( 'Y' ), $docroot, $db );
$this_month->disp( );

$next_month = new Calendar( date( 'n' ) + 1, date( 'Y' ), $docroot, $db );
$next_month->disp( );

?>

</div> <!-- div#calendars -->

<?php

$delicious_query = 'select delicious from prof';
$delicious_result = $db->query( $delicious_query );
$delicious_row = $delicious_result->fetch_assoc( );
if( trim( $delicious_row[ 'delicious' ] ) != '' ) {
    $del = $delicious_row[ 'delicious' ];
?>
<div id="delicious">
<script type="text/javascript"
  src="http://del.icio.us/feeds/js/<?php echo $del; ?>?count=5;title=My%20Bookmarks;icon=s;name"></script>
<noscript><a href="http://del.icio.us/<?php echo $del; ?>">my del.icio.us</a></noscript>
</div> <!-- div#delicious -->

<?php
} // if delicious username is set
?>

<div id="ocsw_logo">
    <?php print_link( "http://www.ocsw.net/",
                      "<img src=\"$docroot/images/blackboard-100x50.png\" "
                      . "height=\"50\" width=\"100\" />" ); ?>
</div> <!-- div#ocsw_logo -->

</div> <!-- div#left -->

<!-- Begin #main div -->

<div id="main">
    
<div id="show_left">
</div>  <!-- div#show_left -->

<div id="content">

<?php

    print "<h1>" . ( isset( $title_stub ) ? $title_stub : 'Welcome!' ) . "</h1>\n";

} // if $no_header != 1
?>
