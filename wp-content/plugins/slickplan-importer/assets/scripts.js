jQuery(document).ready(function($) {

    $('#slickplan-page-content-radios')
        .find('input[type="radio"]').on('change', function() {
            $(this).closest('td')
                .find('.content-suboption').css(
                    'display',
                    (this.value === 'contents') ? 'inline-block' : 'none'
                )
                .end()
                .find('.content-suboption-br').css(
                    'display',
                    (this.value === 'contents') ? 'block' : 'none'
                );
        })
        .filter(':checked')
        .trigger('change');

    if (!window.SLICKPLAN_JSON) {
        return;
    }

    var $window = $(window);
    var $form = $('form#slickplan-importer');
    var $summary = $form.find('.slickplan-summary');
    var $progress = $('#slickplan-progressbar');
    var $wpbody = $('#wpbody-content');

    $progress.progressbar({
        value: 0,
        change: function() {
            $progress.find('.progress-label').text($progress.progressbar('value') + '%');
        }
    });

    var _pages = [];
    var _importIndex = 0;

    var _generatePagesFlatArray = function(pages, parent) {
        $.each(pages, function(index, data) {
            if (data.id) {
                _pages.push({
                    id: data.id,
                    parent: parent,
                    title: data.title
                });
                if (data.childs) {
                    _generatePagesFlatArray(data.childs, data.id);
                }
            }
        });
    };

    var _addMenuID = function(parent_id, mlid) {
        for (var i = 0; i < _pages.length; ++i) {
            if (_pages[i].parent === parent_id) {
                _pages[i].mlid = mlid;
            }
        }
    };

    var _importPage = function(page) {
        var html = ('' + slickplan_ajax.html).replace('{title}', page.title);
        var $element = $(html).appendTo($summary);
        $summary.scrollTop(999999);
        var percent = Math.round((_importIndex / _pages.length) * 100);
        $progress.progressbar('value', percent);
        $.ajax({
            url: slickplan_ajax.ajaxurl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'slickplan-importer',
                slickplan: {
                    page: page.id,
                    parent: page.parent ? page.parent : '',
                    mlid: page.mlid ? page.mlid : 0,
                    last: (_pages && _pages[_importIndex + 1]) ? 0 : 1
                },
                _ajax_nonce: slickplan_ajax.nonce
            },
            success: function(data) {
                if (data && data.html) {
                    $element.replaceWith(data.html);
                    ++_importIndex;
                    if (data) {
                        if (data.mlid) {
                            _addMenuID(page.id, data.mlid);
                        }
                    }
                    if (_pages && _pages[_importIndex]) {
                        _importPage(_pages[_importIndex]);
                    } else {
                        $progress.progressbar('value', 100);
                        $form.find('h3').text('Success!');
                        $form.find('.slickplan-show-summary').show();
                        $window.trigger('resize');
                        //setTimeout(function() {
                        //    $progress.remove();
                        //}, 500);
                    }
                }
            },
            error: function() {
                alert('Unknown error, please delete imported pages and try again.');
            }
        });
    };

    var types = ['home', '1', 'util', 'foot'];
    for (var i = 0; i < types.length; ++i) {
        if (window.SLICKPLAN_JSON[types[i]] && window.SLICKPLAN_JSON[types[i]].length) {
            _generatePagesFlatArray(window.SLICKPLAN_JSON[types[i]]);
        }
    }

    $window
        .on('load', function() {
            _importIndex = 0;
            if (_pages && _pages[_importIndex]) {
                _importPage(_pages[_importIndex]);
            }
        })
        .on('load resize', function() {
            var top = $summary.offset().top;
            $summary.hide();
            var form_height = $form.height();
            $summary.show();
            var height = $window.height() - form_height - top - 5;
            $summary.height(height);
        });

});