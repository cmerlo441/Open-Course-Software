<?php

class Calendar {
    private $month;                   // Month that this calendar displays
    private $year;                    // Year that this calendar displays
    private $first_day;               // Day of week of the first of this month
    private $num_days;                // What is the date of the last day in this month?
    private $assignments = array();   // List of assignments to be highlighted
    private $holidays = array();      // List of holidays to be highlighted
    private $admin;                   // Is an admin logged in?

    function __construct( $m, $y, $db ) {
        if( $m >= 1 and $m <= 9 ) {
            $this->month = "0$m";
        } else if( $m >= 10 and $m <= 12 ) {
            $this->month = $m;
        } else {
            $this_month = date( 'm' );
        }
        $this->year = $y;
        
        $this->first_day = date( 'w', mktime( 0, 0, 0, $this->month, 1, $this->year ) );
        $this->num_days = date( 't', mktime( 0, 0, 0, $this->month, 1, $this->year ) );
        
        // Initialize holidays array
        $holidays_query = 'select * from holidays '
            . 'where date >= "' . date( 'Y-m-d', mktime( 0, 0, 0, $this->month, 1, $this->year ) ) . '" '
            . 'and date <= "' . date( 'Y-m-d', mktime( 0, 0, 0, $this->month, $this->num_days, $this->year ) ) . '" '
            . 'order by date, description ';
        $holidays_result = $db->query( $holidays_query );
        while( $row = $holidays_result->fetch_assoc( ) ) {
            $day = date( 'j', strtotime( $row[ 'date' ] ) );
            foreach( explode( ' ', 'day evening' ) as $day_or_eve ) {
                if( $row[ $day_or_eve ] == 1 ) {
                    $holidays[ $day ][ $day_or_eve ] = $row[ 'description' ];
                }
            }
        }
    }
    
    function __toString( ) {
        print "<!-- Calendar for $this->month/$this->year -->\n";
        print "<div class=\"calendar\" id=\"{$this->year}{$this->month}\">\n";
        print "<table>\n";
        print "  <thead>\n";
        print "    <tr>\n";
        print "      <th colspan=\"7\">\""
            . date( 'F Y', mktime( 0, 0, 0, $this->month, 1, $this->year ) ) . "\"</th>\n";
        print "    </tr>\n";
        print "  </thead>\n\n";
        
        print "  <tbody>\n";
        print "    <tr>\n";
        for( $col = 0; $col < $this->first_day; $col++ ) {
            print "      <td class=\"" . ( $col == 0 ? 'weekend' : 'day' ) . "\">&nbsp;</td>\n";
        }
        for( $date = 1; $date <= $this->num_days; $date++ ) {
            if( $col == 0 ) {
                print "    </tr>\n    <tr>\n";
            }
        }

    }
}
?>
