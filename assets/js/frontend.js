/**
 * Frontend script for the Elementor Markdown Widget.
 *
 * This script initializes KaTeX rendering on the widget's output.
 */
(function($) {
    'use strict';

    /**
     * Define the handler for rendering math.
     * @param {jQuery} $scope The widget's container element.
     */
    var MarkdownWidgetHandler = function($scope) {
        var markdownElement = $scope.find('.markdown-output').get(0);

        if (markdownElement && typeof renderMathInElement === 'function') {
            // Use the KaTeX auto-render extension to find and render math
            renderMathInElement(markdownElement, {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false},
                    {left: '\\(', right: '\\)', display: false},
                    {left: '\\[', right: '\\]', display: true}
                ],
                throwOnError: false
            });
        }
    };

    // Make sure you run this code under Elementor's JS hook
    $(window).on('elementor/frontend/init', function() {
        // The 'markdown_widget' is from the get_name() method in the widget's PHP class.
        elementorFrontend.hooks.addAction('frontend/element_ready/markdown_widget.default', MarkdownWidgetHandler);
    });

})(jQuery);
