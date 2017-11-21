'use strict';

/* global angular */

var services = angular.module('Util.services', []);

function _process_error(){

}

services.factory('Util', ['$http', function ($http) {
    return {
        /**
         * Alert
         * @param {type} message
         * @param {type} warn
         * @param {type} callback
         * @returns {undefined}
         */
        alert: function (message, warn, callback) {
            warn = warn || false;
            var node = $('<div id="__alert__"></div>');
            if (warn) {
                node.addClass('warn');
            } else {
                node.removeClass('warn');
            }
            node.html(message);
            $('body').append(node);
            node.css('left', ($('body').width() - node[0].clientWidth) / 2 + 'px').slideDown();
            window.setTimeout(function () {
                node.slideUp(300, function () {
                    if (typeof callback === 'function') {
                        callback();
                    }
                    $('#__alert__').remove();
                });
            }, 3000);
        },
        /**
         * 分页插件
         * @param {type} total
         * @param {type} func
         * @returns {undefined}
         */
        initPaginator: function (total, func) {
            $(".pagination").jqPaginator({
                totalPages: total,
                onPageChange: func
            });
        },
        /**
         * loading开始
         * @returns {boolean}
         */
        loading: function (status) {
            if (status === undefined) {
                status = true;
            }
            if (!status) {
                return $('.__LOADING__').hide();
            }
            if ($('.__LOADING__').length > 0) {
                $('.__LOADING__').eq(0).show();
                return false;
            }
            var blocksize = 70;
            var imgsize = 40;
            var block = $('<div class="__LOADING__"><img src="static/images/icon/iconfont-loading.png" /></div>');
            block.css({
                height: blocksize,
                width: blocksize,
                borderRadius: '5px',
                background: 'rgba(0,0,0,0.3)',
                position: 'fixed',
                cursor: 'progress'
            });
            block.find('img').css({
                width: imgsize,
                marginTop: (blocksize - imgsize) / 2,
                marginLeft: (blocksize - imgsize) / 2,
                '-webkit-animation-name': 'rotate',
                '-webkit-animation-duration': '1.3s',
                '-webkit-animation-iteration-count': 'infinite',
                '-webkit-animation-timing-function': 'linear'
            });
            $(window).bind('resize', function () {
                block.css({
                    left: ($(window).width() - blocksize) / 2,
                    top: ($(window).height() - blocksize) / 2,
                });
            }).resize();
            $('body').append(block);
            return true;
        }
    };
}]);