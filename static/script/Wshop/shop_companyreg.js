/**
 * 代理注册页面
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */

require(['config'], function (config) {

    require(['util', 'jquery', 'Spinner', 'Tiping'], function (util, $, Spinner, Tiping) {

        $('#confirm').click(function () {
            // 提交代理申请
        });

        var companyType = false;

        util.fnTouchEnd('.sec', secTouch);

        $('.sec').each(function () {
            if (!$(this).hasClass('disable')) {
                secTouch($(this));
                return false;
            }
        });

        /**
         * 确认按钮点击监听
         * @param {type} node
         * @returns {undefined}
         */
        function secTouch(node) {
            if (!node.hasClass('disable')) {
                $('.check').removeClass('ed');
                node.find('.check').toggleClass('ed');
                companyType = parseInt(node.attr('data-type'));
                $('#confirm').removeClass('disable');
                if (companyType === 7) {
                    $('#confirm').attr('href', '?/Uc/companyReg/');
                } else {
                    $('#confirm').attr('href', '?/Company/companyDirectReg/type=' + companyType);
                }
            }
        }

    });
});