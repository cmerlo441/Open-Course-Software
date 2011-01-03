<?php

$title_stub = 'Login Statistics';
require_once( '../_header.inc' );

print "<h2>Total Logins By Section</h2>\n";
print "<div id=\"sections\" style=\"width:550px;height:300px;margin:auto\"></div>\n";

print "<h2>Total Logins By Day of Week</h2>\n";
print "<div id=\"days\" style=\"width:550px;height:300px;margin:auto\"></div>\n";

print "<h2>Total Logins By Hour</h2>\n";
print "<div id=\"hours\" style=\"width:550px;height:300px;margin:auto\"></div>\n";

print "<h2>Total Logins By Browser</h2>\n";
print "<div id=\"browsers\" style=\"width:550px;height:300px;margin:auto\"></div>\n";

print "<h2>Total Logins By Operating System</h2>\n";
print "<div id=\"os\" style=\"width:550px;height:300px;margin:auto\"></div>\n";

print "<h2>Logins By Day of Week By Section</h2>\n";
print "<div id=\"section_by_day\" style=\"width:550px;height:300px;margin:auto\"></div>\n";

print "<h2>Logins By Hour By Section</h2>\n";
print "<div id=\"section_by_hour\" style=\"width:550px;height:300px;margin:auto\"></div>\n";

?>

<script type="text/javascript" src="<?php echo $docroot; ?>/js/flot/jquery.flot.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    
    days = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
    
    $.getJSON( '../graph_data/sections.php', function( data ){
        var labels = [];
        var values = [];
        var i = 1;
        for( var key in data ) {
            labels.push( data[ key ][ 'name' ] + " (" + data[ key ][ 'count' ] + ")" );
            values.push( [ i++, data[ key ][ 'count' ] ] );
        }

        $.plot( $('#sections'), [
            {
                data: values,
                bars: {
                    show: true, align: "center"
                },
                label: "Total Amount of Logins"
            }
        ], {
            xaxis: {
                ticks: labels.length,
                tickFormatter: function(v, axis){
                    if (v <= labels.length) {
                        return labels[ v - 1 ];
                    }
                    else {
                        return '';
                    }
                }
            },
            grid: {
                color: "#aaabac",
                /* tickColor: "#5d562c", */
                aboveData: false
            },
            legend: {
                show: false
            }
        });
    })

    $.getJSON( '../graph_data/browsers.php', function( data ){
        var labels = [];
        var values = [];
        var i = 1;
        for( var key in data ) {
            labels.push( data[ key ][ 'name' ] + " (" + data[ key ][ 'count' ] + ")" );
            values.push( [ i++, data[ key ][ 'count' ] ] );
        }

        $.plot( $('#browsers'), [
            {
                data: values,
                bars: {
                    show: true, align: "center"
                },
                label: "Total Amount of Logins"
            }
        ], {
            xaxis: {
                ticks: labels.length,
                tickFormatter: function(v, axis){
                    if (v <= labels.length) {
                        return labels[ v - 1 ];
                    }
                    else {
                        return '';
                    }
                }
            },
            grid: {
                color: "#aaabac",
                /* tickColor: "#5d562c", */
                aboveData: false
            },
            legend: {
                show: false
            }
        });
    })

    $.getJSON( '../graph_data/os.php', function( data ) {
        var labels = [];
	var values = [];
	var i = 1;
	for( var key in data ) {
	    labels.push( data[ key ][ 'name' ]
	        + ' (' + data[ key ][ 'count' ] + ')' );
	    values.push( [ i++, data[ key ][ 'count' ] ] );
	}

	$.plot( $('#os'), [
            {
	    data: values,
	        bars: {
		    show: true, align: "center"
                },
	        label: 'Total Amount of Logins'
	    }
        ], {
            xaxis: {
                ticks: labels.length,
                tickFormatter: function(v,axis){
                    if( v <= labels.length ) {
                        return labels[ v - 1 ];
		    } else {
			return '';
		    }
		}
	    },
	    grid: {
	        color: "#aaabac",
	        aboveData: false
	    },
            legend: {
                show: false
            }
	});
    })

    $.getJSON( '../graph_data/days.php', function( data ) {
        var values = [];
        for( var key in data ) {
            values.push( [ key, data[ key ] ] );
        }
        
        $.plot( $( '#days' ), [
            {
                data: values,
                label: 'Total Amount of Logins'
            }
        ], {
            xaxis: {
                ticks: values.length,
                tickFormatter: function(v, axis){
                    return days[ v ];
                }
            },
            lines: { show: true },
            points: { show: true },
            grid: {
                color: "#aaabac",
                aboveData: false,
                hoverable: true
            },
            legend: {
                show: false
            }
        });
    })

    $.getJSON( '../graph_data/hours.php', function( data ) {
        var values = [];
        for( var key in data ) {
            values.push( [ key, data[ key ] ] );
        }
        
        $.plot( $( '#hours' ), [
            {
                data: values,
                label: 'Total Amount of Logins'
            }
        ], {
            xaxis: {
                ticks: 24,
                tickFormatter: function(v, axis){
                    var label = '';
                    switch (v) {
                        case 0:
                            label = 'mid';
                            break;
                        case 2:
                        case 4:
                        case 6:
                        case 8:
                        case 10:
                            label = v;
                            break;
                        case 12:
                            label = 'noon';
                            break;
                        case 14:
                        case 16:
                        case 18:
                        case 20:
                        case 22:
                            label = (v - 12);
                            break;
                    }
                    if (label != '') {
                        return label;
                    } else {
                        return '';
                    }
                }
            },
            lines: { show: true },
            points: { show: true },
            grid: {
                color: "#aaabac",
                aboveData: false,
                hoverable: true
            },
            legend: {
                show: false
            }
        });
    })

    var section_names = [];
    $.getJSON( '../graph_data/days_by_section.php', function(data){
        var section_data = [];
        for( var section in data ) {

            section_names[ section ] = data[ section ][ 'name' ];
            section_data[ section ] = [];
            for( var day in data[ section ][ 'data' ] ) {
                section_data[ section ].push( [ day, data[ section ][ 'data' ][ day ] ] );
            }
        }
        
        $.plot( $('#section_by_day' ), 
        [
            { label: section_names[ 1 ], data: section_data[ 1 ] },
            
            // This is so hacky.  Isn't there a better way?
            
            {
                label: section_names.length > 1 ? section_names[ 2 ] : '',
                data: section_data[section_data.length > 1 ? 2 : -1 ]
            },
            {
                label: section_names.length > 2 ? section_names[ 3 ] : '',
                data: section_data[section_data.length > 2 ? 3 : -1 ]
            },
            {
                label: section_names.length > 3 ? section_names[ 4 ] : '',
                data: section_data[section_data.length > 3 ? 4 : -1 ]
            },
            {
                label: section_names.length > 4 ? section_names[ 5 ] : '',
                data: section_data[section_data.length > 4 ? 5 : -1 ]
            },
            {
                label: section_names.length > 5 ? section_names[ 6 ] : '',
                data: section_data[section_data.length > 5 ? 6 : -1 ]
            },
            {
                label: section_names.length > 6 ? section_names[ 7 ] : '',
                data: section_data[section_data.length > 6 ? 7 : -1 ]
            },
            {
                label: section_names.length > 7 ? section_names[ 8 ] : '',
                data: section_data[section_data.length > 7 ? 8 : -1 ]
            },
            {
                label: section_names.length > 8 ? section_names[ 9 ] : '',
                data: section_data[section_data.length > 8 ? 9 : -1 ]
            },
            {
                label: section_names.length > 9 ? section_names[ 10 ] : '',
                data: section_data[section_data.length > 9 ? 10 : -1 ]
            }
        ], {
            xaxis: {
                ticks: section_data.length,
                tickFormatter: function(v, axis){
                    return days[ v ];
                }
            },
            lines: { show: true },
            points: { show: true },
            grid: {
                color: "#aaabac",
                aboveData: false,
                hoverable: true
            },
            legend: {
                backgroundColor: "#1e273e"
            }
        } );
        
    })

    $.getJSON( '../graph_data/hours_by_section.php', function(data){
        var section_data = [];
        for( var section in data ) {

            section_names[ section ] = data[ section ][ 'name' ];
            section_data[ section ] = [];
            for( var day in data[ section ][ 'data' ] ) {
                section_data[ section ].push( [ day, data[ section ][ 'data' ][ day ] ] );
            }
        }
        
        $.plot( $('#section_by_hour' ), 
        [
            { label: section_names[ 1 ], data: section_data[ 1 ] },
            
            // This is so hacky.  Isn't there a better way?
            
            {
                label: section_names.length > 1 ? section_names[ 2 ] : '',
                data: section_data[section_data.length > 1 ? 2 : -1 ]
            },
            {
                label: section_names.length > 2 ? section_names[ 3 ] : '',
                data: section_data[section_data.length > 2 ? 3 : -1 ]
            },
            {
                label: section_names.length > 3 ? section_names[ 4 ] : '',
                data: section_data[section_data.length > 3 ? 4 : -1 ]
            },
            {
                label: section_names.length > 4 ? section_names[ 5 ] : '',
                data: section_data[section_data.length > 4 ? 5 : -1 ]
            },
            {
                label: section_names.length > 5 ? section_names[ 6 ] : '',
                data: section_data[section_data.length > 5 ? 6 : -1 ]
            },
            {
                label: section_names.length > 6 ? section_names[ 7 ] : '',
                data: section_data[section_data.length > 6 ? 7 : -1 ]
            },
            {
                label: section_names.length > 7 ? section_names[ 8 ] : '',
                data: section_data[section_data.length > 7 ? 8 : -1 ]
            },
            {
                label: section_names.length > 8 ? section_names[ 9 ] : '',
                data: section_data[section_data.length > 8 ? 9 : -1 ]
            },
            {
                label: section_names.length > 9 ? section_names[ 10 ] : '',
                data: section_data[section_data.length > 9 ? 10 : -1 ]
            }
        ], {
            xaxis: {
                ticks: 24,
                tickFormatter: function(v, axis){
                    var label = '';
                    switch (v) {
                        case 0:
                            label = 'mid';
                            break;
                        case 2:
                        case 4:
                        case 6:
                        case 8:
                        case 10:
                            label = v;
                            break;
                        case 12:
                            label = 'noon';
                            break;
                        case 14:
                        case 16:
                        case 18:
                        case 20:
                        case 22:
                            label = (v - 12);
                            break;
                    }
                    if (label != '') {
                        return label;
                    } else {
                        return '';
                    }
                }
            },
            lines: { show: true },
            points: { show: true },
            grid: {
                color: "#aaabac",
                aboveData: false,
                hoverable: true
            },
            legend: {
                backgroundColor: "#1e273e"
            }
        } );
        
    })

    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 15,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    };
    
    var previousPoint = null;

    $("#days").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;
                
                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(0),
                    y = item.datapoint[1].toFixed(0);
                
                showTooltip(item.pageX, item.pageY,
                            y + " logins on " + days[ x ] );
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;            
        }
    });

    $("#hours").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;
                
                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(0),
                    y = item.datapoint[1].toFixed(0);
                    
                var hour;
                if( x == 0 || x == 12 ) hour = 12;
                else hour = x % 12;
                
                var am;
                if( x <= 11 ) am = 'am';
                else am = 'pm';
                
                showTooltip(item.pageX, item.pageY,
                            y + " logins between " + hour + ":00 and " +
                            hour + ":59 " + am );
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;            
        }
    });

    $("#section_by_day").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;
                
                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(0),
                    y = item.datapoint[1].toFixed(0);
                
                showTooltip(item.pageX, item.pageY,
                            section_names[ item.seriesIndex + 1 ] + ': ' + y + " logins on " + days[ x ] );
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;            
        }
    });

    $("#section_by_hour").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;
                
                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(0),
                    y = item.datapoint[1].toFixed(0);
                    
                var hour;
                if( x == 0 || x == 12 ) hour = 12;
                else hour = x % 12;
                
                var am;
                if( x <= 11 ) am = 'am';
                else am = 'pm';
                
                showTooltip(item.pageX, item.pageY,
                            section_names[ item.seriesIndex + 1 ] + ': ' +
                            y + " logins between " + hour + ":00 and " +
                            hour + ":59 " + am );
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;            
        }
    });

})
</script>

<?php

$lastmod = filemtime( $_SERVER[ 'SCRIPT_FILENAME' ] );
include( "$fileroot/_footer.inc" );
    
?>