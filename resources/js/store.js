import { defineStore } from 'pinia'
import camelToKebab from './filters/String.js';

export const useQueryParamsStore = defineStore('queryParams', {
  state: () => ({
    queryParams: {
      page: null,
      perPage: null,
      startedBefore: null,
      startedAfter: null,
      showFilters: false,
    },
  }),

  actions: {
    setQueryParam(payload) {
      this.queryParams[payload.key] = payload.value;
      // Note: router needs to be imported where this is used
      if (window.router) {
        window.router.setQueryParam(camelToKebab(payload.key), payload.value);
      }
    },

    setQueryParamsFromUrl() {
      if (window.router) {
        let urlParams = window.router.getQueryParams();
        Object.keys(this.queryParams).forEach(key => {
          this.setQueryParam({
            key: key,
            value: urlParams.get(camelToKebab(key)),
          });
        });
      }
    }
  }
});
