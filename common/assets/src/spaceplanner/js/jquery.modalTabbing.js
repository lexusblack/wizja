(function($) {

    $.fn.modalTabbing = function() {

        var tabbing = function(jqSelector) {
            var inputs = $(jqSelector).find('select, input, textarea, button, a[href]').filter(':visible').not(':disabled');

            //Focus to first element in the container.
            inputs.first().focus();

            $(jqSelector).on('keydown', function(e) {
                if (e.which === 9) {

                    var inputs = $(jqSelector).find('select, input, textarea, button, a[href]').filter(':visible').not(':disabled');

                    /*redirect last tab to first input*/
                    if (!e.shiftKey) {
                        if (inputs[inputs.length - 1] === e.target) {
                            e.preventDefault();
                            inputs.first().focus();
                        }
                    }
                    /*redirect first shift+tab to last input*/
                    else {
                        if (inputs[0] === e.target) {
                            e.preventDefault();
                            inputs.last().focus();
                        }
                    }
                }
            });
        };

        return this.each(function() {
            tabbing(this);
        });
    };
})(jQuery);
