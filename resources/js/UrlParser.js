import camelToKebab from './filters/String.js';

export default {
    getUrl() {
        return window.location.href;
    },

    urlOnly(url) {
        return url.split('?')[0];
    },

    queryOnly(url) {
        return url.split('?')[1];
    },

    queryToObject(query) {
        return query.split('&').reduce((obj, pair) => {
            obj[this.kebabToCamel(pair.split('=')[0])] = pair.split('=')[1];
            return obj;
        }, {});
    },

    mergeQueryObjectIntoUrl(obj, url) {
        return this.buildUrl(
            this.urlOnly(url),
            this.mergeParameters(this.queryToObject(this.queryOnly(url)), obj)
        );
    },

    current(params = {}) {
        return this.mergeQueryObjectIntoUrl(params, this.getUrl());
    },

    mergeParameters(initialParameters, mergeParameters) {
        return Object.assign({}, initialParameters, mergeParameters);
    },

    buildUrl(url, params) {
        let keys = Object.keys(params).filter(key => {
            if (typeof params[key] == 'undefined') {
                return false;
            }
            return params[key] != null
        });
        return url + '?' + keys.map(key => camelToKebab(key) + '=' + params[key]).join('&');
    },

    kebabToCamel(string) {
        return string.replace(/-([a-z])/g, g => g[1].toUpperCase());
    }
}