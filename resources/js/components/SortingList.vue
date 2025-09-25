<template>
    <div class="flex">
        <div class="flex-shrink-0">
            Sort by:
        </div>
        <div class="ml-2 flex flex-wrap">
            <sort-option label="country" field-code="country_name" :selected="currentFieldCode" :order="order" @click="setField"/>
            <sort-option label="scope" field-code="answers.f_1_5" :selected="currentFieldCode" :order="order" @click="setField"/>
            <sort-option label="region" field-code="answers.f_1_7a.raw" :selected="currentFieldCode" :order="order" @click="setField"/>
            <sort-option label="start date" field-code="answers.f_1_11" :selected="currentFieldCode" :order="order" @click="setField"/>
            <sort-option label="end date" field-code="answers.f_1_12" :selected="currentFieldCode" :order="order" @click="setField"/>
            <sort-option label="EMM target population" field-code="answers.f_3_2" :selected="currentFieldCode" :order="order" @click="setField"/>
            <sort-option label="sample size" field-code="answers.f_5_2" :selected="currentFieldCode" :order="order" @click="setField" :pipe="false"/>
        </div>
    </div>
</template>

<script>
    import {default as SortOption} from "./SortOption.vue";

    export default {
        components: {SortOption},
        data() {
            return {
                fields: [],
                order: 'asc',
                currentFieldCode: 'country_name'
            }
        },
        methods: {
            setField(fieldCode) {
                if (this.currentFieldCode === fieldCode) {
                    this.switchOrder()
                } else {
                    this.currentFieldCode = fieldCode
                }
                this.$emit('update', {field_code: this.currentFieldCode, order: this.order});
            },
            switchOrder() {
                if (this.order === 'desc') {
                    this.order = 'asc';
                } else {
                    this.order = 'desc';
                }
            }
        }
    }
</script>
