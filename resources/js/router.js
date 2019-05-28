import queryString from 'query-string';

class Router {
    setQueryParam(key, value) {
        let query = queryString.parse(window.location.search);

        query[key] = value;

        window.location.search = queryString.stringify(query);
    }
    getQueryParam(key) {
        return (new URL(window.location.href)).searchParams.get(key);
    }
    buildUrl(url, params, state) {
        let merged = this.mergeQueryParams(params, state);
        let keys = Object.keys(merged);
        return url + '?' + keys.map(key => params[key] + '=' + merged[key]).join('&');
    }
    mergeQueryParams(params, state) {
        let merged= {};

        let keys = Object.keys(params);

        keys.forEach((key) => {
            if (state[key] || this.getQueryParam(params[key])) {
                merged[key] = state[key] || this.getQueryParam(params[key]);
            }
        });

        return merged;
    }
}

export default Router;
