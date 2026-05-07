(function (global) {
    'use strict';

    var root = global || window;
    var ns = root.RestatifyShared = root.RestatifyShared || {};
    ns.registry = ns.registry || {};

    function key(component, version) {
        return String(component).toLowerCase().trim() + '@' + String(version).trim();
    }

    ns.register = function register(component, version, payload) {
        var k = key(component, version);
        if (!Object.prototype.hasOwnProperty.call(ns.registry, k)) {
            ns.registry[k] = payload;
        }
        return ns.registry[k];
    };

    ns.get = function get(component, version) {
        var k = key(component, version);
        return Object.prototype.hasOwnProperty.call(ns.registry, k)
            ? ns.registry[k]
            : null;
    };

    ns.has = function has(component, version) {
        var k = key(component, version);
        return Object.prototype.hasOwnProperty.call(ns.registry, k);
    };
})(typeof window !== 'undefined' ? window : this);
