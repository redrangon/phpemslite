<template>
	<div ref="mathContainer">
		<lay-space direction="vertical" class="question" fill wrap>
            <markDownView :content="question.question"></markDownView>
			<template v-if="questype.questsort === 1">
				<lay-checkcard-group single v-model="answer" class="selector" :disabled="disabled">
					<lay-checkcard value="A" description="已掌握"></lay-checkcard>
					<lay-checkcard value="B" description="未掌握"></lay-checkcard>
				</lay-checkcard-group>
			</template>
			<template v-else-if="questype.questchoice === 1">
				<template v-if="question.questionselecttype === 1">
					<lay-checkcard-group single v-model="answer" class="selector" :disabled="disabled">
						<lay-checkcard :value="selectors[sid]" v-for="selector, sid in question.questionhtml">
							<template #description>
								<markDownView :content="selector"></markDownView>
							</template>
						</lay-checkcard>
					</lay-checkcard-group>
				</template>
				<template v-else>
					<lay-checkcard-group single v-model="answer" class="selector" :disabled="disabled">
						<lay-checkcard :value="selectors[sid]" :description="selector" v-for="selector, sid in question.questionhtml"></lay-checkcard>
					</lay-checkcard-group>
				</template>
			</template>
			<template v-else-if="questype.questchoice === 4">
				<lay-checkcard-group single v-model="answer" class="selector" :disabled="disabled">
					<lay-checkcard value="A" description="正确"></lay-checkcard>
					<lay-checkcard value="B" description="错误"></lay-checkcard>
				</lay-checkcard-group>
			</template>
			<template v-else-if="questype.questchoice === 2 || questype.questchoice === 3">
				<template v-if="question.questionselecttype === 1">
					<lay-checkcard-group v-model="manswer" class="selector" :disabled="disabled">
						<lay-checkcard :value="selectors[sid]" v-for="selector, sid in question.questionhtml">
							<template #description>
								<markDownView :content="selector"></markDownView>
							</template>
						</lay-checkcard>
					</lay-checkcard-group>
				</template>
				<template v-else>
					<lay-checkcard-group v-model="manswer" class="selector" :disabled="disabled">
						<lay-checkcard :value="selectors[sid]" :description="selector" v-for="selector, sid in question.questionhtml"></lay-checkcard>
					</lay-checkcard-group>
				</template>
			</template>
			<template class="selector" v-else-if="questype.questchoice === 5">
				<lay-input v-model="answer" :disabled="disabled"></lay-input>
			</template>
			<template v-else>
				<div>
					未知题型无法处理
				</div>
			</template>
		</lay-space>
	</div>
</template>
<script>
	import { ref } from 'vue';
	import markDownView from '@/components/desktop/MarkDownView.vue';
	export default {
		name: 'question',
		components: { markDownView },
		data() {
			return {
				answer: ref(),
				manswer: ref([]),
				selectors: ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
                inited:ref(false)
			}
		},
		props: ['question', 'index', 'questype', 'childindex', 'useranswer','disabled'],
		emits: ['saveAnswer', 'update:useranswer'],
		created() {		
            if(this.questype.questsort !== 1 && (this.questype.questchoice === 2 ||  this.questype.questchoice === 3))
			{
				this.manswer = this.useranswer?.split('');
			}
			this.answer = this.useranswer;            
		},
        mounted(){
            this.inited = true;
        },
		methods:{
			//
		},
		watch: {
			manswer: function (n, o) {
				let manswer = this.manswer;
				manswer.sort();
				this.answer = manswer.join('');
			},
			answer:function(n,o){
				if(!this.inited){
                    return;
                }
                if(n !== o){
                    this.$emit('update:useranswer', this.answer);
                    this.$emit('saveAnswer');
				}
			}
		}
	}
</script>
<style>
	.selector,.selector .layui-checkcard {
		font-size: 16px;
		width: 100%;
	}

	.question {
		line-height: 2.5;
		padding: 2px;
	}

	.question img{
		margin-left: 5px;
		margin-right: 5px;
	}

	.layui-checkcard-desc {
		line-height: 2;
	}

	.layui-checkcard-checked {
		background-color: rgb(22, 186, 170);
	}

	.layui-checkcard-checked .layui-checkcard-desc {
		color: #FFFFFF;
	}
</style>