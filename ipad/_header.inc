<?php

/* This is always the first PHP code executed by any script.  Make
 * sure we connect to the database, set up any variables that will be
 * needed later, and display the top of the page (unless told not to).
 */

$days = array( "Monday" => 1,
               "Tuesday" => 2,
               "Wednesday" => 3,
               "Thursday" => 4,
               "Friday" => 5,
               "Saturday" => 6,
               "Sunday" => 7 );
    
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
$main_site = "/~{$username}";
$docroot = "/~{$username}/ipad";
$fileroot = "$home_directory/public_html/ipad";
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
$version_fh = fopen( "$fileroot/../version.txt", 'r' );
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

$iphone = preg_match( '/iPhone/', $_SERVER[ 'HTTP_USER_AGENT' ] );

if( $no_header != 1 ) {

?>

<!DOCTYPE html>
<html lang="en"><head><title><?= $title ?></title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/themes/blue-and-gold.min.css" />
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0-rc.1/jquery.mobile-1.1.0-rc.1.min.css" />
<link rel="icon" type="image/png" href="<?php echo $docroot ?>/images/ocsw.favicon.png" />

<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.1.0-rc.1/jquery.mobile-1.1.0-rc.1.min.js"></script></head>

<body>

<div data-role="page" id="the_page" data-title="<?php echo $prof[ 'name' ]; ?>">

    <div data-role="header">
        <a href="<?php echo $docroot; ?>" data-icon="home" class="ui-btn-right">Home</a>
        <a data-rel="back" data-icon="back" class="ui-btn-left">Back</a>
        <h1>Prof. <?php echo $prof[ 'name' ] ?></h1>
    </div> <!-- div[data-role='header'] -->

    <div data-role="content">
        <div style="text-align: center;"><?php echo $prof[ 'title' ]; ?><br />
            Department of <?php echo $prof[ 'department' ]; ?><br />
            <?php echo $prof[ 'college_name' ]; ?>
        </div>

<?php
}

if( $_GET[ 'logout' ] == 'true' ) {
    foreach( $_SESSION as $key=>$value ) {
        unset( $_SESSION[ $key ] );
    }
}

$login_error = 0;

if( isset( $_POST[ 'username' ] ) and isset( $_POST[ 'password' ] ) ) {
    $admin_query = 'select * from prof '
      . 'where username = "' . $db->real_escape_string( $_POST[ 'username' ] ) . '" '
      . 'and password = md5( "' . $db->real_escape_string( $_POST[ 'password' ] )
      . '" )';
    $admin_result = $db->query( $admin_query );
    
    $student_query = 'select * from students '
      . 'where banner = "'
          . strtoupper( $db->real_escape_string( $_POST[ 'username' ] ) ) . '" '
      . 'and password = md5( "' . $db->real_escape_string( $_POST[ 'password' ] )
      . '" )';
    $student_result = $db->query( $student_query );
    
    if( $admin_result->num_rows == 1 ) {
        $_SESSION[ 'admin' ] = 1;
    }
    
    // do student stuff here
    
    else {
        $login_error = 1;
    }
}

if( $_SESSION[ 'admin' ] == 1 ) {
?>
    <div data-role="header" data-mini="true" data-position="inline" data-theme="b">
        <h1>Logged in as <?php echo $prof[ 'name' ]; ?>.</h1>
    </div>

<?php
} else {
    if( $login_error == 1 ) {
?>

    <div data-role="header" data-position="inline" data-theme="e">
        <h1>There was a problem logging you in.  Please check your credentials and try again.</h1>
    </div>

<?php
    }
}
?>
