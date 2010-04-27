;(function($) {
// displays snowflakes in a giving selector
$.fn.snow = function(options) {
    
    var opts = $.extend({}, $.fn.snow.defaults, options);
    
    
    // generates a random position
    function getRandomPosition(width) {
        return Math.floor(Math.random() * width);
    }
    
    // move the flake!
    function moveFlakes(flakes, speed) {
        for(i in flakes) {
            var $left = parseInt(flakes[i].css('left'));
            var $top = parseInt(flakes[i].css('top'));
            
            if($top > $(window).height()) {
                $top = 0;
            }
            if($left > $(window).width()) {
                $left = 0;
            }
            
            flakes[i].css({
                left: $left + flakes[i].particleSpeed + 'px',
                top: $top + flakes[i].particleSpeed + 5 + 'px'
            });
        }
    }
    
    return this.each(function() {
        
        var $this = $(this);
        var $width = $(window).width();
        var $height = $(window).height();
        
        // Support for the Metadata Plugin.
        var o = $.meta ? $.extend({}, opts, $this.data()) : opts;
        
        // create a flake
        var elm = $('<div>' + o.dot + '</div>');
        elm.css({
            left: '0px',
            top: '10px',
            color: o.color,
            fontSize: o.dotSize,
            position:'absolute'
        })
        elm.addClass('snowflake');
        
        var flakes = [];
        
        // and clone it for the amount of particles
        for(var p = 0; p <= o.particles; p++) {
            var flake = elm.clone();
            flake.attr('id', 'snowflake-' + p);
            flake.css({
                top: Math.floor(Math.random() * -$height),
                left: getRandomPosition($width)
            })
            flake.particleSpeed = Math.floor(Math.random() * o.particleSpeed);
            flake.appendTo($this);
            flakes.push(flake);
        }
        
        setInterval(function() {
            moveFlakes(flakes, o.particleSpeed)
        }, o.speed);
        
    });
    
    // private function for debugging
    function debug($obj) {
        if (window.console && window.console.log) {
            window.console.log($obj);
        }
    }
};

// default options
$.fn.snow.defaults = {
    dot: '&#149;',
    dotSize: '10px',
    color: '#fff',
    particles: 100,
    particleSpeed: 3,
    speed: 20
};

})(jQuery);