<template>
    <div class="mb-6" style="max-width: 300px;">
        <div class="pl-3 pr-2 uppercase mb-2 font-bold border-dotted border-b-2 border-black">
            {{filter.name}}
        </div>
        <div class="pl-3" v-if="['choice','array'].includes(filter.type)">
            <ul class="list-reset" v-if="filter.options.length < 4 && filter.type === 'choice'">
                <li v-for="option in order(filter.options)" class="mb-2"
                    v-if="option.doc_count >= 0">
                    <div class="checkbox">
                        <label class="flex items-baseline">
                            <input type="checkbox"
                                   class="flex-shrink-0 form-checkbox text-green-600 rounded-none border-dotted border-black border-1 bg-transparent h-4 w-4 mr-2 cursor-pointer"
                                   :checked="isSelected(selected,option.value)"
                                   @change="checkboxChanged"
                                   :value="option.value">
                            <div class="ml-2">
                                {{option.label}} <span class="text-gray-500">({{ option.doc_count }})</span>
                            </div>
                        </label>
                    </div>
                </li>
            </ul>
            <div v-else>
                <v-select
                    class="style-chooser"
                    multiple
                    searchable
                    clearable
                    placeholder="Select options (several allowed)"
                    v-model="selected"
                    :options="order(filter.options)"
                >
                    <template slot="option" slot-scope="option">
                        {{ option.label }} ({{ option.doc_count }})
                    </template>
                </v-select>
            </div>
        </div>
        <div v-if="filter.type === 'date'">
            <datepicker class="text-xl pl-3" placeholder=".../..."
                        v-model="selected"
                        input-class="bg-transparent w-32"
                        calendar-class="text-sm"
                        clear-button
                        :open-date="new Date('2000-01-01')"
                        minimumView="month" maximumView="year" initialView="year"
                        format="MMM yyyy"
            >
            </datepicker>
        </div>
        <div v-if="filter.type === 'number'">
            <div class="flex justify-between px-4">
                <div v-text="filter.stats.min"></div>
                <div v-text="filter.stats.max"></div>
            </div>
            <vue-range-slider ref="slider"
                              class="mb-4"
                              :min-range="filter.stats.min"
                              :max-range="filter.stats.max"
                              :min="filter.stats.min"
                              :max="filter.stats.max"
                              tooltip-dir="bottom"
                              tooltip="hover" v-model="selected"></vue-range-slider>
        </div>
    </div>
</template>

<script>
import FilterMixin from "@/components/FilterMixin.vue";
import Datepicker from 'vuejs-datepicker';
import VueRangeSlider from 'vue-range-component';
import 'vue-range-component/dist/vue-range-slider.css'

export default {
    mixins: [FilterMixin],
    components: {Datepicker, VueRangeSlider},
}
</script>
