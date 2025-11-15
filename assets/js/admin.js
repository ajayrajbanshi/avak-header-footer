/**
 * AVAK Header Footer - Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize code editors with syntax highlighting
        if (typeof wp.codeEditor !== 'undefined') {
            // Header Code Editor
            var headerEditor = wp.codeEditor.initialize('avak_hf_header_code', {
                codemirror: {
                    mode: 'htmlmixed',
                    lineNumbers: true,
                    lineWrapping: true,
                    indentUnit: 2,
                    tabSize: 2,
                    indentWithTabs: false,
                    theme: 'default',
                    matchBrackets: true,
                    autoCloseBrackets: true,
                    autoCloseTags: true,
                    matchTags: {bothTags: true},
                    extraKeys: {
                        "Ctrl-Space": "autocomplete",
                        "Ctrl-/": "toggleComment",
                        "Cmd-/": "toggleComment"
                    }
                }
            });

            // Body Open Code Editor
            var bodyOpenEditor = wp.codeEditor.initialize('avak_hf_body_open_code', {
                codemirror: {
                    mode: 'htmlmixed',
                    lineNumbers: true,
                    lineWrapping: true,
                    indentUnit: 2,
                    tabSize: 2,
                    indentWithTabs: false,
                    theme: 'default',
                    matchBrackets: true,
                    autoCloseBrackets: true,
                    autoCloseTags: true,
                    matchTags: {bothTags: true},
                    extraKeys: {
                        "Ctrl-Space": "autocomplete",
                        "Ctrl-/": "toggleComment",
                        "Cmd-/": "toggleComment"
                    }
                }
            });

            // Footer Code Editor
            var footerEditor = wp.codeEditor.initialize('avak_hf_footer_code', {
                codemirror: {
                    mode: 'htmlmixed',
                    lineNumbers: true,
                    lineWrapping: true,
                    indentUnit: 2,
                    tabSize: 2,
                    indentWithTabs: false,
                    theme: 'default',
                    matchBrackets: true,
                    autoCloseBrackets: true,
                    autoCloseTags: true,
                    matchTags: {bothTags: true},
                    extraKeys: {
                        "Ctrl-Space": "autocomplete",
                        "Ctrl-/": "toggleComment",
                        "Cmd-/": "toggleComment"
                    }
                }
            });

            // Refresh editors on window resize
            $(window).on('resize', function() {
                if (headerEditor) {
                    headerEditor.codemirror.refresh();
                }
                if (bodyOpenEditor) {
                    bodyOpenEditor.codemirror.refresh();
                }
                if (footerEditor) {
                    footerEditor.codemirror.refresh();
                }
            });

            // Auto-save warning prevention
            var hasChanges = false;

            if (headerEditor) {
                headerEditor.codemirror.on('change', function() {
                    hasChanges = true;
                });
            }

            if (bodyOpenEditor) {
                bodyOpenEditor.codemirror.on('change', function() {
                    hasChanges = true;
                });
            }

            if (footerEditor) {
                footerEditor.codemirror.on('change', function() {
                    hasChanges = true;
                });
            }

            // Warn before leaving if there are unsaved changes
            $(window).on('beforeunload', function() {
                if (hasChanges) {
                    return 'You have unsaved changes. Are you sure you want to leave?';
                }
            });

            // Reset hasChanges flag on form submit
            $('form').on('submit', function() {
                hasChanges = false;
            });
        }

        // Smooth scroll to section on error
        if (window.location.hash) {
            var hash = window.location.hash;
            if ($(hash).length) {
                $('html, body').animate({
                    scrollTop: $(hash).offset().top - 50
                }, 500);
            }
        }

        // Add copy button functionality (optional enhancement)
        $('.avak-hf-section').each(function() {
            var section = $(this);
            var textarea = section.find('textarea');

            if (textarea.length) {
                // You can add a copy button here if needed
            }
        });
    });

})(jQuery);
