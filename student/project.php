<?php

$title_stub = 'Project';
require_once( '../_header.inc' );

if( $_SESSION[ 'student' ] > 0 ) {
    $project = $db->real_escape_string( $_GET[ 'project' ] );
    $section = $db->real_escape_string( $_GET[ 'section' ] );
    
    $project_query = 'select a.id, a.due_date, a.title, a.description '
        . 'from assignments as a, grade_types as t, student_x_section as x '
        . "where a.section = $section "
        . 'and a.grade_type = t.id '
        . 'and t.grade_type = "project" '
        . 'and a.section = x.section '
        . "and x.student = {$_SESSION[ 'student' ]} "
        . "order by due_date";
    //    print "<pre>$project_query;</pre>\n";
    $project_result = $db->query( $project_query );
    if( $project_result->num_rows > 0 ) {
        for( $count = 1; $count < $project; $count++ ) {
            $not_the_right_project = $project_result->fetch_assoc( );
        }
            
        $project_row = $project_result->fetch_assoc( );
        $id = $project_row[ 'id' ];
        $name = $project_row[ 'title' ];
        print "<h2>Due Date</h2>\n"
            . '<p style="text-align: center">This project '
            . ( $project_row[ 'due_date' ] > date( 'Y-m-d H:i:s' ) ? 'is' : 'was' )
            . ' due on ' . date( 'l, F j, Y \a\t g:i a',
                                 strtotime( $project_row[ 'due_date' ] ) )
            . ".</p>\n\n";
            
        print "<h2>Related Files</h2>\n";
        $files_query = 'select * from assignment_documents '
            . "where assignment = $id "
            . "order by name";
        $files_result = $db->query( $files_query );
        if( $files_result->num_rows == 0 ) {
            print 'None.';
        } else {
            print "<ul>\n";
            while( $doc = $files_result->fetch_assoc( ) ) {
                print "<li><a href=\"$docroot/download_assignment_document.php?id={$doc[ 'id' ]}\">"
                    . "{$doc[ 'name' ]} ({$doc[ 'size' ]} bytes)</a></li>\n";

            }
            print "</ul>\n";
        }
        
        print "<h2>Your Solutions</h2>\n";
        
        $sub_query = 'select u.id, u.datetime '
	    . 'from assignment_uploads as u, '
	    . 'assignment_upload_requirements as r '
	    . 'where u.assignment_upload_requirement = r.id '
            . "and r.assignment = {$project_row[ 'id' ]} "
            . "and u.student = {$_SESSION[ 'student' ]} "
            . "order by datetime desc limit 1";
        $sub_result = $db->query( $sub_query );
        if( $sub_result->num_rows == 0 ) {
            print 'No submission';
        } else {
            $sub = $sub_result->fetch_assoc( );
            print '<p style="text-align: center">Last submission: ' . date( 'F j \a\t g:i a', strtotime( $sub[ 'datetime' ] ) );
            if( $sub[ 'datetime' ] > $project_row[ 'due_date' ] ) {
                $diff = strtotime( $sub[ 'datetime' ] ) - strtotime( $project_row[ 'due_date' ] );
                $seconds_in_a_day = 60 * 60 * 24;
                $days_late = ceil( $diff / $seconds_in_a_day );
                print ", <span class=\"late\">$days_late day"
                    . ( $days_late == 1 ? '' : 's' ) . " late</span>";
            }
        }
        print ".</p>\n";
        
        print "<div class=\"accordion\" id=\"solutions\">\n";

        $upload_reqs_query = 'select * from assignment_upload_requirements '
            . "where assignment = {$project_row[ 'id' ]} "
            . 'order by filename';
        $upload_reqs_result = $db->query( $upload_reqs_query );
        while( $reqs = $upload_reqs_result->fetch_assoc( ) ) {
	    $extension = preg_replace( '/^.*\.([^\.]+)$/', "$1", $reqs[ 'filename' ] );
            print "<h3><a href=\"#\">{$reqs[ 'filename' ]}</a></h3>\n";
            print "<div class=\"upload\" name=\"{$reqs[ 'filename' ]}\" "
                . "extension=\"$extension\" id=\"{$reqs[ 'id' ]}\">\n";
            print "</div>\n";
        }
        print "</div>  <!-- div.accordion#solutions -->\n";
        
        print '<p style="text-align: center">';
        $late_deadline = date( 'Y-m-d H:i:s',
            mktime( date( 'H', strtotime( $project_row[ 'due_date' ] ) ),
                    date( 'i', strtotime( $project_row[ 'due_date' ] ) ),
                    date( 's', strtotime( $project_row[ 'due_date' ] ) ),
                    date( 'n', strtotime( $project_row[ 'due_date' ] ) ),
                    date( 'j', strtotime( $project_row[ 'due_date' ] ) ) + 5 ) );
        if( date( 'Y-m-d H:i:s' ) <= $late_deadline ) {
            print "<input id=\"upload\" />";
        } else {
            print 'This project is now more than five days past due, and can '
            . 'no longer be submitted.';
        }
        print "</p>\n";
?>

<script type="text/javascript">
$(document).ready(function(){
    var student_id = "<?php echo $_SESSION[ 'student' ]; ?>";
    var project_number = "<?php echo $project; ?>";
    var project_name = "<?php echo $project_row[ 'title' ]; ?>";
    var project_id = "<?php echo $project_row[ 'id' ]; ?>";
    var project_info = ' #' + project_number + ': ' + project_name;
    
    $('h1').html( $('h1').html( ) + project_info );
    $(document).attr('title', $(document).attr('title') + project_info );

    $('div.upload').each(function(){
        var id = $(this).attr('id');
        $.post( 'assignment_upload_contents.php',
            { id: id },
            function(data){
		var extension = "<?php echo $extension; ?>";
                $('div.upload[id=' + id + ']').html(data).addClass( "brush:" + extension );
            }
        )
    })
    
    $('div#solutions').accordion({
        active: false,
        autoHeight: false,
        collapsible: true
    })

    $('#upload').uploadify({
        'uploader': '../uploadify/uploadify.swf',
        'script': './student_project_upload.php',
        'cancelImg': '../uploadify/cancel.png',
        'auto': 'false',
        'folder': './uploads',
        'buttonText': 'Upload Files',
        'fileDesc': 'Source Code, Plain Text Files, and Archives',
        'fileExt': '*.java;*.php;*.js;*.css;*.py;*.pl;*.c;*.cpp;*.zip;*.gz;*.jar;*.tar;*.txt',
        'wmode': 'transparent',
        'sizeLimit': '500000',
        'scriptData': {student: student_id, assignment: project_id},
        'fileDataName': 'file',
        'multi': true,
        'onComplete': function(a,b,c,d,e){
	    //console.log(d);
            $('div.upload').each(function(){
                var id = $(this).attr('id');
                $.post( 'assignment_upload_contents.php',
                    { id: id },
                    function(data){
                        $('div.upload[id=' + id + ']').html(data);
                    }
                )
            })
        },
        'onError': function( a, b, c, d ){
            if( d.info == 404 )
                alert( 'Can not find upload script' );
            else
                alert( 'error ' + d.type + ": " + d.info );
        }
    })
})
</script>

<?php
    } else {
        print 'That project does not exist.';
    }
}

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );

?>