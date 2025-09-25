<template>
    <div ref="dataSection" class="flex align-items-stretch container mx-auto mb-10">
        <div style="min-height: fit-content;max-width: 300px;" class="w-1/3 min-w-32 p-3 bg-grey-lighter text-sm">
            <h3 class="font-bold text-xl mb-4">Simple filtering
                <span class="text-sm">(<a @click="showAdvancedFilters = true"
                                          class="text-blue-500 hover:text-underline cursor-pointer">Advanced</a>)</span>
            </h3>
            <ul class="list-reset">
                <li class="mb-2" v-for="filter in simpleFilters" :key="filter.code">
                    <filter-display :filter="filter"
                                    :value="queryFilters[filter.code] || null"
                                    @updated="updateFilters">
                    </filter-display>
                </li>
            </ul>
            <div class="modal z-10 fixed w-full h-full top-0 left-0 flex items-center justify-center"
                 :class="{'opacity-0 pointer-events-none': !showAdvancedFilters}" v-show="showAdvancedFilters">
                <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

                <div class="container w-11/12 mx-auto shadow-lg z-20 overflow-y-auto border border-black bg-white-90%"
                     style="overscroll-behavior: none">
                    <div class="flex items-start mb-6">
                        <div class="flex-1 font-bold text-4xl text-center pt-6">
                            Advanced Filtering:
                        </div>
                        <button
                            class="close px-4 py-2 text-xl border-b border-l hover:bg-teal-600 hover:text-white
                                border-black"
                            @click="showAdvancedFilters = false"
                        >&times;
                        </button>
                    </div>
                    <div class="overflow-y-auto mb-6 px-4 max-h-500px">
                        <div v-for="(filters, parent) in advancedFilters">
                            <h2 class="text-bold text-xl mb-2" v-text="parent"></h2>
                            <ul class="list-reset ml-3">
                                <li class="mb-2 break-inside-avoid" v-for="filter in filters">
                                    <template v-if="'fields' in filter && filter.fields.length > 0">
                                        <h2 class="text-bold text-xl mb-2" v-text="filter.name"></h2>
                                        <filter-inline-display
                                            class="ml-3"
                                            v-for="subFilter in filter.fields"
                                            :filter="subFilter"
                                            :key="subFilter.code"
                                            :value="queryFilters[subFilter.code] || null"
                                            @updated="updateFilters"/>
                                    </template>
                                    <filter-inline-display
                                        v-else
                                        :filter="filter"
                                        :key="filter.code"
                                        :value="queryFilters[filter.code] || null"
                                        @updated="updateFilters"/>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div
                        class="py-4 text-xl text-center border-t font-bold border-black cursor-pointer hover:bg-teal-600 hover:text-white"
                        @click="showAdvancedFilters = false"
                    >
                        DONE
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col p-3 w-full">
            <div class="border-b border-black flex items-center pb-3 mb-3">
                <img src="/images/search.png"
                     class="w-6 mx-5">
                <input type="text"
                       ref="searchInput"
                       v-model="query.text"
                       placeholder="Free text search: search for country, keyword, institution, scope, etc."
                       class="w-full h-6 outline-none"
                >
                <a href="https://ethmigsurveydatahub.eu/boolean-logic/"
                   target="_blank"
                   class="flex-shrink-0 ml-2 text-gray-500 text-sm">Help</a>
            </div>
            <div class="flex items-center mb-3 border-b border-black pb-3">
                <img src="/images/sort.png" class="w-6 mx-5">
                <div class="flex flex-col">
                    <div>Showing: <strong>{{pagination.from}} - {{pagination.to}}</strong> of
                        <strong>{{pagination.total}}</strong>
                        search results
                    </div>
                    <sorting-list @update="updateSorting"/>
                </div>
            </div>
            <div class="flex mb-3 border-b border-black pb-3">
                <div class="flex items-center">
                    <img src="/images/clear.png" class="w-6 ml-5 mr-2">
                    <button
                        @click="clearFilters"
                        class="underline outline-none focus:outline-none">
                        Clear all selections
                    </button>
                </div>
                <div class="flex items-center">
                    <img src="/images/options.png" class="w-6 ml-5 mr-2">
                    <button
                        @click="showAdvancedFilters = true"
                        class="underline outline-none">
                        Advanced filtering
                    </button>
                </div>
            </div>
            <div v-if="Object.keys(query.filters).length > 0" class="flex mb-3 border-b border-black pb-1 flex-wrap">
                <div v-for="filter in queryFiltersForTags"
                     class="flex items-center bg-gray-300 py-1 pl-2 pr-1 border border-gray-500 mr-2 mb-2 rounded-md text-xs flex-shrink-0">
                    {{filter.label}}:&nbsp;<strong>{{filter.value}}</strong>
                    <span
                        @click="removeSelectedFilterOption(filter.fieldCode, filter.rawValue)"
                        class="ml-2 text-xl leading-none px-1 cursor-pointer rounded-md hover:bg-gray-400">&times;</span>
                </div>
            </div>
            <div>
              <div v-show="loading">Loading surveys...</div>
              <result-list v-show="!loading" :surveys="results" :view-fields="viewFields"></result-list>
            </div>
            <div>
                <div class="container mx-auto">
                    <vue-pagination v-if="results.length > 0 && pagination.total > 0"
                                    :total-items="pagination.total"
                                    :items-per-page="pagination.per_page"
                                    :page="pagination.current_page-1"
                                    @page-change="pageChanged"
                    ></vue-pagination>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {
    clone,
    filter,
    each,
    findIndex,
    find,
    assign,
    omitBy,
    isEmpty,
    omit,
    isArray,
    debounce,
    groupBy,
} from 'lodash';
import queryString from 'qs';

let defaultSort = {
    field: 'country_name',
    direction: 'asc'
};

export default {
    data() {
        return {
            results: [],
            viewFields: [],
            pagination: {},
            filters: [],
            query: {
                text: '',
                filters: {},
                sort: clone(defaultSort)
            },
            lastQueryPayload: "",
            showAdvancedFilters: false,
            loading: true,
        }
    },
    async created() {
        this.parseQueryString();
        await this.fetchSurveys();
        await this.fetchViewFields();
        document.onkeyup = (e) => {
            e = e || window.event;
            if (e.keyCode === 191 /* / */) {
                this.$refs.searchInput.focus();
            }
        };
    },
    computed: {
        simpleFilters() {
            return filter(this.filters, f => f.filter_simple === 1)
        },
        advancedFilters() {
            return this.groupByParentName(JSON.parse(JSON.stringify(this.filters)))
        },
        queryFilters() {
            return this.query.filters;
        },
        queryFiltersForTags() {
            let filters = [];
            if (!this.query.filters || !Object.keys(this.filters).length) return filters;
            each(this.query.filters, (values, fieldCode) => {
                if (typeof values === 'string') {
                    filters.push({
                        fieldCode,
                        rawValue: values,
                        label: this.filters[fieldCode].name,
                        value: values
                    })
                } else if (Array.isArray(values)) {
                    each(values, value =>  find(this.filters[fieldCode].options, {value}) && filters.push({
                        fieldCode,
                        rawValue: value,
                        label: this.filters[fieldCode].name,
                        value: find(this.filters[fieldCode].options, {value}).label || value
                    }))
                }
            })

            return filters;
        }
    },
    watch: {
        "query.text"(newV, oldV) {
            if (newV !== oldV) {
                this.applyFilters()
            }
        }
    },
    methods: {
        async fetchViewFields() {
            let viewFields = await axios.get('/api/list-fields');
            this.viewFields = viewFields.data;
        },
        async fetchSurveys(params = {}) {
            let query = assign({
                page: this.pagination.current_page
            }, this.query, params);
            query = omitBy(query, value => {
                if (typeof value === "number") {
                    return false;
                }
                return isEmpty(value)
            });
            this.loading = true
            if (this.queryUnchanged(query)) return;
            this.updateQueryString(query);
            axios.post('/api/surveys', query)
                .then(response => {
                    this.$set(this, 'results', response.data.data.data);
                    this.$set(this, 'filters', response.data.filters);
                    this.$set(this, 'pagination', omit(response.data.data, ['data']))
                    this.loading = false
                })
                .catch(err => {
                    console.error(err)
                    this.loading = false
                });
        },
        updateQueryString(query) {
            // let location = window.location;
            let queryAsString = queryString.stringify(query, {encode: false, arrayFormat: 'brackets'});
            window.history.pushState("", "", location.origin + location.pathname + `?${queryAsString}`)
        },
        parseQueryString() {
            let search = location.search;
            if (search[0] === '?') {
                search = search.substr(1, search.length)
            }
            let filters = queryString.parse(search);
            this.$set(this.query, 'text', filters.text || '');
            this.$set(this.query, 'sort', filters.sort || clone(defaultSort));
            this.$set(this.query, 'filters', filters.filters || {})
        },
        updateFilters(filter) {
            if (filter.selected === null || (isArray(filter.selected) && filter.selected.length < 1)) {
                this.$delete(this.query.filters, filter.field_code)
            } else {
                this.$set(this.query.filters, filter.field_code, filter.selected)
            }

            this.applyFilters()
        },
        applyFilters: debounce(function () {
            // we debounce it because it is called twice for some reason
            this.fetchSurveys({
                page: 1
            })
        }, 300),
        updateSorting(options) {
            this.query.sort.field = options.field_code;
            this.query.sort.direction = options.order;
            this.applyFilters()
        },
        pageChanged(n) {
            this.fetchSurveys({
                page: n + 1
            })
        },
        async clearFilters() {
            this.$set(this.query, 'filters', {});
            this.$set(this.query, 'text', '');
            this.$set(this.query, 'sort', clone(defaultSort));
            await this.applyFilters();
            let filtersBk = this.filters;
            this.filters = [];
            this.$nextTick(() => {
                this.filters = filtersBk;
            });
        },
        queryUnchanged(query) {
            let payload = btoa(unescape(encodeURIComponent(JSON.stringify(query))));
            if (payload === this.lastQueryPayload) return true;

            this.lastQueryPayload = payload;
        },

        groupByParentName(filters) {
            return groupBy(this.filterOutEmptySubHeadings(filters), 'parent.name')
        },
        filterOutEmptySubHeadings(filters) {
            return filter(this.mapToSubHeadings(filters), filter => {
                if (!filter || ('fields' in filter && filter.fields.length === 0)) {
                    return false;
                }
                return true;
            });
        },
        mapToSubHeadings(filters) {
            filters = this.onlyAdvancedFilters(filters);
            let temp = JSON.parse(JSON.stringify(filters));
            each(filters, (field) => {
                if (field.parent && field.parent.type === 'sub-heading' && findIndex(temp, {code: field.parent.code}) >= 0) {
                    temp[findIndex(temp, ['code', field.parent.code])].fields.push(field);
                    temp.splice(findIndex(temp, ['code', field.code]), 1)
                }
            });

            return temp;
        },
        onlyAdvancedFilters(filters) {
            return filter(filters, field => {
                return ['sub-heading'].includes(field.type)
                    || field.filter_advanced === 1;
            });
        },
        removeSelectedFilterOption(fieldCode, value) {
            let selected = this.query.filters[fieldCode]
            if(typeof selected === 'string') {
                this.updateFilters({
                    field_code: fieldCode,
                    selected: null
                });
            } else if (typeof selected === 'object' && selected.length){
                selected.splice(selected.indexOf(value),1)
                this.updateFilters({
                    field_code: fieldCode,
                    selected
                });
            }
        }
    }
}
</script>
