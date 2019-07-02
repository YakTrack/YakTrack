import camelToKebab from './filters/String.js';
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        queryParams: {
            page: null,
            perPage: null,
            startedBefore: null,
            startedAfter: null,
            showFilters: false,
        },
    },
    mutations: {
        setQueryParam: (state, payload) => {
            state.queryParams[payload.key] = payload.value;
        },
    },
    actions: {
        setQueryParam: (context, payload) => {
            context.commit('setQueryParam', payload);
            router.setQueryParam(camelToKebab(payload.key), payload.value);
        },
        setQueryParamsFromUrl: (context) => {
            let urlParams = router.getQueryParams();
            Object.keys(context.state.queryParams)
                .forEach(key => {
                    context.commit('setQueryParam', {
                        key: key,
                        value: urlParams.get(camelToKebab(key)),
                    });
                });
        }
    }
})

export default store;
