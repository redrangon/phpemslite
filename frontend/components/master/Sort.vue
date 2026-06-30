<template>
    <div ref="sortContainer">
	    <template v-for="did in modelValue" :key="did">
            <lay-button type="primary" :value="did" v-if="datas[did]">{{datas[did]}}</lay-button>
        </template>
        
    </div>
</template>
<script setup>
import {ref,onMounted} from 'vue';
import Sortable from 'sortablejs';

const modelValue = defineModel();
const props = defineProps({
	datas: Object
});
const sortContainer = ref();
onMounted(() => {
	new Sortable(sortContainer.value, {
		animation: 150,
		easing: "cubic-bezier(1, 0, 0, 1)",
		onEnd:(evt) => {
			const index = modelValue.value;
			const el = index[evt.oldIndex];
			index.splice(evt.oldIndex,1);
			index.splice(evt.newIndex,0,el);
			modelValue.value = index;
		}
	});
})
</script>