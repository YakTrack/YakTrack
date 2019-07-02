import camelToKebab from './filters/String.js';

class Router {
    getQueryParam(key) {
        return this.getQueryParams().get(camelToKebab(key));
    }

    getQueryParams() {
        return this.getURL().searchParams;
    }

    getURL() {
        return (new URL(window.location.href))
    }

    buildUrl(url, params) {
        let keys = Object.keys(params).filter(key => {
            if (typeof params[key] == 'undefined') {
                return false;
            }
            return params[key] != null
        });
        return url + '?' + keys.map(key => camelToKebab(key) + '=' + params[key]).join('&');
    }

    unsetQueryParam(key) {
        this.getQueryParams().delete(key)
    }

    setQueryParam(key, value) {
        var url = this.getURL();
        url.searchParams.set(camelToKebab(key), value);
        window.history.pushState({
            path: url.toString()
        }, '', url.toString());
    }
}

export default Router;
