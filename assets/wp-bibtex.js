/* WP-BibTeX JS */
(function($) {
    $('.wpbibtex-trigger').click(function() {
        var bibtexInfoContainer = $('.bibtex', $(this).parent().parent().parent());
        if ( $(bibtexInfoContainer).is(':visible') ) {
            $(bibtexInfoContainer).slideUp(250);
        } else {
            $(bibtexInfoContainer).slideDown(250);
        }
    });

    $('.wpbibtex-item-highlight').on('mouseenter touchstart', function(evt) {
        let overlayImg = $('.overlay', $(this));
        if ( overlayImg.length ) {
            $('.overlay', $(this)).removeClass('transparent');
        }
    })
    
    $('.wpbibtex-item-highlight').on('mouseleave touchend', function() {
        let overlayImg = $('.overlay', $(this));
        if ( overlayImg.length ) {
            $('.overlay', $(this)).addClass('transparent');
        }
    })
})(jQuery);
