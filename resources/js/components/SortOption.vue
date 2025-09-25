<template>
    <div>
        <span v-if="active">{{order === 'asc' ? '&downarrow;': '&uparrow;'}}</span>
        <span :class="{'cursor-pointer hover:text-blue-400 hover:underline': true,'font-bold underline text-blue-400':active}" @click="$emit('click', fieldCode)">
            {{ label.trim() }}</span>
        <span class="mx-1" v-if="pipe">|</span>
    </div>
</template>
<script>
    export default {
        props: {
            fieldCode: {
                required: true
            },
            label: {
                required: true
            },
            selected: {
                required: true
            },
            order: {
                required: true
            },
            pipe: {
                default: true
            }
        },
        computed: {
            active(){
                return this.selected === this.fieldCode
            }
        }
    }
</script>
