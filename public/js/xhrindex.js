(function($) {

    "use strict";

    let methods = {

        init: function(settings) {

            let form = $(this);
            let url = settings.url ? settings.url : form.attr('action');
            let method = settings.method ? settings.method : (form.attr('method') ?? 'POST');
            let resetPageInput = true;

            let currentXhr = null;

            form.submit(function() {

                if (currentXhr) {
                    currentXhr.abort();
                }

                settings.ajaxLoading.toggle();
                settings.responseContainer.html('');
                
                if (resetPageInput) {
                    settings.pageInput.val(1);
                }

                let data = form.serialize();

                currentXhr = $.ajax({
                    url: url,
                    type: method,
                    data: data
                }).fail(function(jqXHR, textStatus, errorThrown) {

                    if (jqXHR.status === 419) {
                        window.location.reload();
                        return false;
                    }

                    if (settings.onFail) {
                        settings.onFail();
                    }

                    if (jqXHR.responseJSON) {
                        let error_msg = 'Error ' + jqXHR.status + '\n' +
                            jqXHR.responseJSON.message;
                        showError(error_msg);
                        console.error([jqXHR, textStatus, errorThrown]);
                        return false;
                    }

                    if (typeof textStatus !== 'string' || textStatus !== 'abort') {
                        showError('Ocurrió un error inesperado');
                    }
                    console.error([jqXHR, textStatus, errorThrown]);

                }).done(function(res, textStatus, jqXHR) {

                    if (typeof res.result !== 'undefined' && res.result) {
                        settings.responseContainer.html(res.html);
                        activatePagination(settings.responseContainer.find('.pagination'));
                        if (settings.onDone) {
                            settings.onDone();
                        }
                        if (settings.writeConsole) {
                            console.info(res);
                        }
                        return false;
                    }

                    showError('Ocurrió un error inesperado');

                }).always(function() {
                    currentXhr = null;
                    settings.ajaxLoading.toggle();
                });

                return false;

            });

            if (settings.autoload) {
                form.trigger('submit');
            }

            function activatePagination(el) {

                if (el.length && settings.pageInput) {
                    el.find('a').click(function(ev) {
                        let page = $(this).data('page');
                        settings.pageInput.val(page);
                        ev.preventDefault();
                        resetPageInput = false;
                        form.trigger('submit');
                        resetPageInput = true;
                        return false;
                    });
                }

            }

            function showError(message) {

                if (typeof toastr === 'undefined') {
                    alert(message);
                } else {
                    toastr.error(message);
                }

            }

        }

    }

    $.fn.xhrIndex = function(methodOrOptions, options = {}) {

        if (methods[methodOrOptions]) {
            let settings = $.extend({}, $.fn.xhrIndex.defaults, options);
            this.data('settings', settings);
            return methods[methodOrOptions].call(this, settings, Array.prototype.slice.call(arguments, 1));
        } else if (typeof methodOrOptions === 'object' || !methodOrOptions) {
            let settings = $.extend({}, $.fn.xhrIndex.defaults, methodOrOptions);
            let results = [];
            this.each(function() {
                results.push(methods.init.call($(this), settings, arguments));
            });
            return results;
        } else {
            console.warn('El método ' + methodOrOptions + ' no existe en xhrIndex');
        }

    };

    $.fn.xhrIndex.defaults = {
        'method': 'POST',
        'url': '',
        'ajaxLoading': $('#ajaxLoading'),
        'responseContainer': '',
        'writeConsole': false,
        'autoload': true,
        'pageInput': '',
        'searchInput': $('#search'),
        'onDone': '',
        'onFail': ''
    };

})(jQuery);