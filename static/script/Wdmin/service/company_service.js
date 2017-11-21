'use strict';

/* global angular */

var services = angular.module('Company.services', []);

services.factory('Company', ['$http', function ($http) {
    return {
        /**
         * 获取代理列表
         * @param {type} p
         * @returns {*}
         */
        getList: function (p) {
            return $http.get('?/wCompany/getList/', {
                params: p
            }).error(function (ret) {
                _process_error(ret);
            });
        },
        /**
         * 获取代理信息
         * @param p
         * @returns {*}
         */
        getInfo: function (p) {
            return $http.get('?/wCompany/getInfo/', {
                params: p
            }).error(function (ret) {
                _process_error(ret);
            });
        },
        /**
         * 编辑代理信息
         * @param p
         */
        modify: function (p) {
            return $http.post('?/wCompany/modify/', $.param(p), {
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).error(function (ret) {
                _process_error(ret);
            });
        },
        /**
         * 获取未审核计数
         * @returns {*}
         */
        getUnVerifedCount: function () {
            return $http.get('?/wCompany/getUnVerifedCount/', {
                params: {}
            }).error(function (ret) {
                _process_error(ret);
            });
        },
        /**
         * 获取已审核计数
         * @returns {*}
         */
        getVerifedCount: function () {
            return $http.get('?/wCompany/getVerifedCount/', {
                params: {}
            }).error(function (ret) {
                _process_error(ret);
            });
        },
        /**
         * 代理审核不通过
         * @param p
         * @returns {*}
         */
        companyReqDeny: function (p) {
            return $http.post('?/wCompany/companyReqDeny/', $.param(p), {
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).error(function (ret) {
                _process_error(ret);
            });
        }
    };
}]);