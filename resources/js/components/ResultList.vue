<template>
    <div>
        <ul class="list-reset" v-if="surveys.length > 0">
            <li v-for="item in surveys">
                <div class="flex flex-col pl-10 pr-5 py-6 border-b border-black">
                    <div class="flex">
                        <div class="flex-1 font-bold text-sm mb-1 font-light">
                            {{field('f_1_0',item)}}
                        </div>
                        <div>
                            <a target="_blank" :href="`/api/xml-surveys/${item.id}`"
                               class="text-xs text-blue-800"
                            >XML</a>
                        </div>
                    </div>
                    <div class="text-xl leading-tight text-blue-500 font-bold">
                        <a :href="`/surveys/${item.id}`" class="hover:underline">
                            {{field('f_1_3',item)}}
                        </a>
                    </div>
                    <div class="leading-tight mb-8 sans-serif">{{field('f_1_4',item)}}</div>
                    <ul class="list-reset text-sm" v-if="viewFields">
                        <li class="leading-tight" v-for="(viewField,index) in viewFields" v-if="index > 2">
                            <span class="font-bold" v-text="viewField.name+': '"></span>
                            <span class="sans-serif" >{{field(viewField.field_code, item)}}</span>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        <div v-else>
            <slot name="empty">
                No surveys found.
            </slot>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['surveys', 'viewFields']
    }
</script>
