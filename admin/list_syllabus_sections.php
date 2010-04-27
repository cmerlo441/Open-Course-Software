<?php

$no_header = 1;
require_once( '../_header.inc' );

if( $_SESSION[ 'admin' ] == 1 ) {
    
    if( $_POST[ 'action' ] == 'delete' ) {
        $delete_query = 'delete from syllabus_sections '
            . 'where id = "' . $db->real_escape_string( $_POST[ 'id' ] ) . '"';
        $delete_result = $db->query( $delete_query );
    }
    
    else if( $_POST[ 'new_content' ] != '' ) {
        $update_query = 'update syllabus_sections '
            . 'set default_value = "'
            . $db->real_escape_string( $_POST[ 'new_content' ] ) . '" '
            . 'where id = "' . $db->real_escape_string( $_POST[ 'id' ] ) . '"';
        $update_result = $db->query( $update_query );
    }
    
    else if( $_POST[ 'action' ] == 'up' or $_POST[ 'action' ] == 'down' ) {
        $moving_section_query = 'select * from syllabus_sections '
            . 'where id = "' . $db->real_escape_string( $_POST[ 'id' ] ) . '"';
        $moving_section_result = $db->query( $moving_section_query );
        $moving_section_row = $moving_section_result->fetch_assoc( );
        $m_sequence = $moving_section_row[ 'sequence' ];

        $displaced_section_query = 'select * from syllabus_sections '
            . 'where sequence = '
            . ( $_POST[ 'action' ] == 'up' ? ( $m_sequence - 1 ) : ( $m_sequence + 1 ) );
//        print "<pre>$displaced_section_query;</pre>\n";
        $displaced_section_result = $db->query( $displaced_section_query );
        $displaced_section_row = $displaced_section_result->fetch_assoc( );
        
//        print_r( $displaced_section_row );
        
        $d_sequence = $displaced_section_row[ 'sequence' ];
        
//        print '$d_sequence = ' . $d_sequence . "\n";

        $sequence_query_1 = 'update syllabus_sections '
            . "set sequence = $d_sequence where id = {$moving_section_row[ 'id' ]}";
        $sequence_query_2 = 'update syllabus_sections '
            . "set sequence = $m_sequence where id = {$displaced_section_row[ 'id' ]}";
        $db->query( $sequence_query_1 );
        $db->query( $sequence_query_2 );
    }
    
    else if( isset( $_POST[ 'new_title' ] ) and isset( $_POST[ 'new_value' ] ) ) {
        $sequence_query = 'select max( sequence ) as max from syllabus_sections';
        $sequence_result = $db->query( $sequence_query );
        $sequence_row = $sequence_result->fetch_assoc( );
        $sequence = $sequence_row[ 'max' ] + 1;
        
        $new_section_query = 'insert into syllabus_sections '
            . '( id, section, default_value, editable, sequence ) values '
            . '( null, "' . $db->real_escape_string( $_POST[ 'new_title' ] ) . '", '
            . '"' . $db->real_escape_string( $_POST[ 'new_value' ] ) . '", '
            . "1, $sequence )";
        $new_section_result = $db->query( $new_section_query );
    }
   
    $default_query = 'select id, section, editable from syllabus_sections order by sequence';
    $default_result = $db->query( $default_query );
    
    if( $default_result->num_rows == 0 ) {
        print 'None.';
    } else {
        $count = 1;
        print '<ul>';
        while( $row = $default_result->fetch_assoc( ) ) {
            print '<li class="syllabus_section">';
            if( $row[ 'editable' ] == 1 ) {
                print "<a href=\"javascript:void(0)\" id=\"{$row[ 'id' ]}\" "
                    . "class=\"delete_syllabus_section\">";
                print "<img src=\"$docroot/images/silk_icons/cancel.png\" "
                    . "height=\"16\" width=\"16\" "
                    . "title=\"Delete Section {$row[ 'section' ]}\" /></a> ";
                print "<a href=\"javascript:void(0)\" id=\"{$row[ 'id' ]}\" "
                    . "class=\"show_syllabus_section\">";
            }
            print $row[ 'section' ];
            if( $row[ 'editable' ] == 1 ) {
                print "</a>\n";
            }

            if( $count > 1 ) {
                print "<a href=\"javascript:void(0)\" class=\"move_up\" "
                    . "id=\"{$row[ 'id' ]}\" "
                    . "title=\"Move {$row[ 'section' ]} up\">"
                    . "<img src=\"$docroot/images/silk_icons/arrow_up.png\" "
                    . "height=\"16\" width=\"16\" /></a>";
            }
            if( $count < $default_result->num_rows ) {
                print "<a href=\"javascript:void(0)\" class=\"move_down\" "
                    . "id=\"{$row[ 'id' ]}\" "
                    . "title=\"Move {$row[ 'section' ]} down\">"
                    . "<img src=\"$docroot/images/silk_icons/arrow_down.png\" "
                    . "height=\"16\" width=\"16\" /></a>";
            }

            print "</li>\n";
            print "<div class=\"syllabus_section\" id=\"{$row[ 'id' ]}\"></div>\n";
            $count++;
        }
        print "</ul>\n";
    }
?>
<script type="text/javascript">
$(document).ready(function(){
    $("a.show_syllabus_section").click(function(){
        var id = $(this).attr('id');
        $.post( 'edit_syllabus_section.php',
            { id: id },
            function( data ) {
                $("div.syllabus_section[id="+id+"]").html(data);
            }
        )
    })
    
    $("a.delete_syllabus_section").click(function(){
        var id = $(this).attr('id');
        $.post( 'list_syllabus_sections.php',
            { action: 'delete', id: id },
            function( data ) {
                $('div#current_sections').html(data);
            }
        )
    })
    
    $("a.move_up").click(function(){
        var id = $(this).attr('id');
        $.post( 'list_syllabus_sections.php',
            { id: id, action: 'up' },
            function( data ) {
                $('div#current_sections').html(data);
            }
        )
    })

    $("a.move_down").click(function(){
        var id = $(this).attr('id');
        $.post( 'list_syllabus_sections.php',
            { id: id, action: 'down' },
            function( data ) {
                $('div#current_sections').html(data);
            }
        )
    })
})
</script>
<?php
}
    
?>
