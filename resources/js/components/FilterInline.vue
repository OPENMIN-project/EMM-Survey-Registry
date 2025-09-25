<template>
    <div class="mb-6 pb-3 border-dotted border-b-2 border-black flex">
        <div class="w-1/2 pr-3 uppercase font-bold">
            {{filter.name}}
        </div>
        <div class="flex-1" v-if="['choice','array'].includes(filter.type)">
            <ul class="flex" v-if="filter.options.length < 4 && filter.type === 'choice'">
                <li class="w-1/3 ml-2 cursor-pointer"
                    v-for="option in order(filter.options)"
                    v-if="option.doc_count >= 0">
                    <div class="checkbox">
                        <label class="flex items-baseline cursor-pointer">
                            <input type="checkbox"
                                   class="flex-shrink-0 form-checkbox text-green-600 rounded-none border-dotted border-black border-1 bg-transparent h-4 w-4 mr-2 cursor-pointer"
                                   :checked="isSelected(selected,option.value)"
                                   @change="checkboxChanged"
                                   :value="option.value">
                            <div>
                                {{option.label}} <span class="text-gray-500">({{ option.doc_count }})</span>
                            </div>
                        </label>
                    </div>
                </li>
            </ul>
            <div v-if="filter.options.length >= 4 || filter.type === 'array'">
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
    </div>
</template>

<script>
import FilterMixin from "@/components/FilterMixin.vue";
import Datepicker from 'vuejs-datepicker';

export default {
    mixins: [FilterMixin],
    components: {Datepicker},
}
</script>
