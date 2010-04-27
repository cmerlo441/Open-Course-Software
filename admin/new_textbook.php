<?php

require_once( '/home/cmerlo/.htpasswd' );
session_start( );

if( $_SESSION[ 'admin' ] == 1 ) {

	// Textbook details
	$values[ 'title' ] = htmlentities( trim( $_POST[ 'title' ] ) );
	
	if( preg_match( "/^([0-9]+)/", trim( $_POST[ 'edition' ] ), $matches ) == 1 ) {
		$values[ 'edition' ] = trim( $matches[ 1 ] );
	} else {
		print "Invalid request: edition";
		die();
	}
	
	if( preg_match( "/^[0-9]{4}$/", trim( $_POST[ 'year' ] ) ) == 1 ) {
		$values[ 'year' ] = trim( $_POST[ 'year' ] );
	} else {
		print "Invalid request: year";
		die();
	}
	
	if( preg_match( "/^[0-9X-]+$/", trim( $_POST[ 'isbn' ] ) ) == 1 ) {
		$values[ 'isbn' ] = trim( $_POST[ 'isbn' ] );
	} else {
		print "Invalid request: isbn";
		die();
	}
	
	// Publisher details
	
	if( preg_match( "/^[0-9]+$/", trim( $_POST[ 'pub' ] ) ) == 1 ) {
		$values[ 'pub' ] = trim( $_POST[ 'pub' ] );
	} else {
		print "Invalid request: pub";
		die();
	}
	
	if( $values[ 'pub' ] == "0" ) {
		// New publisher
		$values[ 'pub_name' ] = htmlentities( trim( $_POST[ 'pub_name' ] ) );
		$values[ 'pub_url' ] = htmlentities( trim( $_POST[ 'pub_url' ] ) );
		$db->query( 'lock table publishers' );
		$insert_query = 'insert into publishers ( id, name, url ) values '
			. "( null, \"{$values[ 'pub_name' ]}\", \"{$values[ 'pub_url' ]}\" )";
		$insert_result = $db->query( $insert_query );
		if( $db->affected_rows == 0 ) {
			print "Invalid request: inserting publisher into DB";
			$db->query( 'unlock tables' );
			die();
		}
		$id_query = 'select id from publishers order by id desc limit 1';
		$id_result = $db->query( $id_query );
        $id_row = $id_result->fetch_assoc( );
        $id_result->close( );
		$values[ 'pub' ] = $id_row[ 'id' ];
		$db->query( 'unlock tables' );
	}
	
	// Author details
	
	$authors = 0;
	
	foreach( $_POST as $key=>$value ) {
		if( preg_match( "/^author([0-9]+)$/", $key, $matches ) == 1 ) {
			$sequence = $matches[ 1 ];
			$authors++;
			if( $value == 0 ) {
				// New author
				$first = htmlentities( trim( $_POST[ "author{$sequence}first" ] ) );
				$middle = htmlentities( trim( $_POST[ "author{$sequence}middle" ] ) );
				$last = htmlentities( trim( $_POST[ "author{$sequence}last" ] ) );
				$email = htmlentities( trim( $_POST[ "author{$sequence}email" ] ) );
				$url = htmlentities( trim( $_POST[ "author{$sequence}url" ] ) );
				
				$db->query( 'lock table authors' );
				$insert_query = 'insert into authors '
					. '( id, first, middle, last, email, url ) values '
					. "( null, \"$first\", \"$middle\", \"$last\", \"$email\", "
					. "\"$url\" )";
				$insert_result = $db->query( $insert_query );
				if( $db->affected_rows == 0 ) {
					print "Invalid request: inserting author into DB";
					$db->query( 'unlock tables' );
					die();
				}
				$id_query = 'select id from authors order by id desc limit 1';
				$id_result = $db->query( $id_query );
                $id_row = $id_result->fetch_assoc( );
                $id_result->close( );
				$values[ "author{$sequence}" ] = $id_row[ 'id' ];
				$db->query( 'unlock tables' );
			} else {
				$values[ "author{$sequence}" ] = $value;
			}
		}
	}
	
	// Publisher and authors are taken care of.  Add the textbook to the DB.
	
	$db->query( 'lock table textbooks' );
	$insert_query = 'insert into textbooks '
		. '( id, title, edition, year, isbn, publisher ) values '
		. "( null, \"{$values[ 'title' ]}\", \"{$values[ 'edition' ]}\", \"{$values[ 'year' ]}\", "
		. "\"{$values[ 'isbn' ]}\", \"{$values[ 'pub' ]}\" )";
	$insert_result = $db->query( $insert_query );
	if( $db->affected_rows == 0 ) {
		print "Invalid request: inserting textbook into DB";
		$db->query( 'unlock tables' );
		die();
	}
	$id_query = 'select id from textbooks order by id desc limit 1';
	$id_result = $db->query( $id_query );
    $id_row = $id_result->fetch_assoc( );
    $id_result->close( );
	$text_id = $id_row[ 'id' ];
	$db->query( 'unlock tables' );

	// Now that we have the ID # of the newly-added textbook, we can attach authors
	
	for( $i = 1; $i <= $authors; $i++ ) {
		$author_query = 'insert into textbook_x_author( id, textbook, author, `order` ) '
			. "values( null, \"$text_id\", \"" . $values[ "author{$i}" ] . "\", \"$i\" )";
		$author_result = $db->query( $author_query );
		if( $db->affected_rows == 0 ) {
			print "Invalid request: inserting values into textbook_x_author";
			die();
		}
        $author_result->close( );
	}
	
	// Done!
	$textbooks_query = 'select id, title from textbooks order by title';
    $textbooks_result = $db->query( $textbooks_query );
    if( $textbooks_result->num_rows == 0 ) {
        print "<p>There are no textbooks in the database.</p>\n";
    } else {
    	print "<ul id=\"textbook_list\">\n";
        while( $row = $textbooks_result->fetch_assoc( ) ) {
			if( preg_match( "/^(.*):/", $row[ 'title' ], $matches ) == 1 ) {
				$title = $matches[ 1 ];
			} else {
				$title = $row[ 'title' ];
			}
        	print "<li id=\"{$row[ 'id' ]}\"><a href=\"javascript:void(0)\" "
        		. "class=\"textbook_details\" id=\"{$row[ 'id' ]}\">$title</a>\n";
        	print "<div class=\"textbook_details\" id=\"{$row[ 'id' ]}\"></div>\n";
        	print "</li>\n";
        }
        print "</ul>\n";
    }
	
} else {
	print "Invalid request: not admin";
}

?>