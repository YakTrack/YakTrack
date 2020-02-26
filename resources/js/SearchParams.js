export default {
    all() {
        return (new URLSearchParams(window.location.search));
    },
    get(key) {
        return this.all().get(key);
    },
    set(key, value) {
        let params = this.all();
        
        params.set(key, value);
    
        window.history.replaceState({}, "", decodeURIComponent(`${window.location.pathname}?${params}`));
    },
}