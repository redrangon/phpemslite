<template>
	<div ref="mathContainer">
		<lay-space class="question" direction="vertical" fill wrap>
			<div v-if="question.parent && (childIndex === 0)" style="padding:10px 5px 10px 5px;" v-html="question.parent"></div>
			<div style="padding:0 5px 10px 5px;" v-html="question.question"></div>

			<!-- 主观题 -->
			<template v-if="questionType.questsort === 1"></template>

			<!-- 单选题 -->
			<template v-else-if="questionType.questchoice === 1">
				<template v-if="question.questionselecttype === 1">
					<lay-checkcard-group v-model="answer" :disabled="disabled" class="selector" single>
						<lay-checkcard v-for="(selector, sid) in question.questionhtml" :value="selectors[sid]">
							<template #description>
								<div v-html="selector"></div>
							</template>
						</lay-checkcard>
					</lay-checkcard-group>
				</template>
				<template v-else>
					<lay-checkcard-group v-model="answer" :disabled="disabled" class="selector" single>
						<lay-checkcard v-for="(selector, sid) in question.questionhtml" :description="selector" :value="selectors[sid]"></lay-checkcard>
					</lay-checkcard-group>
				</template>
			</template>

			<!-- 判断题（正确/错误） -->
			<template v-else-if="questionType.questchoice === 4">
				<lay-checkcard-group v-model="answer" :disabled="disabled" class="selector" single>
					<lay-checkcard description="正确" value="A"></lay-checkcard>
					<lay-checkcard description="错误" value="B"></lay-checkcard>
				</lay-checkcard-group>
			</template>

			<!-- 多选题 (核心修改部分) -->
			<template v-else-if="questionType.questchoice === 2 || questionType.questchoice === 3">
				<template v-if="question.questionselecttype === 1">
					<lay-checkcard-group v-model="manswer" :disabled="disabled" class="selector">
						<lay-checkcard v-for="(selector, sid) in question.questionhtml" :value="selectors[sid]">
							<template #description>
								<div v-html="selector"></div>
							</template>
						</lay-checkcard>
					</lay-checkcard-group>
				</template>
				<template v-else>
					<lay-checkcard-group v-model="manswer" :disabled="disabled" class="selector">
						<lay-checkcard v-for="(selector, sid) in question.questionhtml" :description="selector" :value="selectors[sid]"></lay-checkcard>
					</lay-checkcard-group>
				</template>
			</template>

			<!-- 填空题 -->
			<template v-else-if="questionType.questchoice === 5" class="selector">
				<lay-input v-model="answer" :disabled="disabled"></lay-input>
			</template>

			<template v-else>
				<div>未知题型无法处理</div>
			</template>
		</lay-space>
	</div>
</template>

<script>
import { ref } from 'vue';

export default {
	name: 'question',
	data() {
		return {
			manswer: [], // 仅保留多选题的本地状态（数组形式）
			selectors: ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
		}
	},
	props: ['question', 'index', 'questionType', 'childIndex', 'userAnswer', 'disabled'],
	emits: ['saveAnswer', 'update:userAnswer'],

	computed: {
		answer: {
			get() {
				// 场景1：如果是多选题（类型2或3），基于 manswer 计算最终字符串
				if (this.questionType.questchoice === 2 || this.questionType.questchoice === 3) {
					if (this.manswer && this.manswer.length > 0) {
						// 使用 [...] 复制数组，避免 sort() 修改原数组引用
						return [...this.manswer].sort().join('');
					}
					return '';
				}
				// 场景2：其他题型（单选、判断、填空），直接使用 userAnswer
				return this.userAnswer || '';
			},
			set(val) {
				// 当值发生变化时（无论是计算得出的，还是用户输入的），触发事件
				this.$emit('update:userAnswer', val);
				this.$emit('saveAnswer');
			}
		}
	},

	watch: {
		userAnswer: {
			handler(newVal) {
				this.update();
			},
			immediate: false
		},
		manswer: {
			handler(newVal) {
				// 当 manswer 变化时，通过 computed 的 setter 触发 update:userAnswer 事件
				if (this.questionType.questchoice === 2 || this.questionType.questchoice === 3) {
					const sortedAnswer = newVal ? [...newVal].sort().join('') : '';
					this.$emit('update:userAnswer', sortedAnswer);
					this.$emit('saveAnswer');
				}
			},
			deep: true
		}
	},
	created() {
		this.update();
	},
	methods: {
		update: function () {
			// 仅负责将 props 中的 userAnswer 同步到本地 manswer 数组
			if (this.questionType.questchoice === 2 || this.questionType.questchoice === 3) {
				if (this.userAnswer) {
					this.manswer = this.userAnswer.split('');
				} else {
					this.manswer = [];
				}
			}
			// 注意：不需要手动赋值 this.answer，computed 会自动处理
		}
	}
}
</script>

<style>
.selector, .selector .layui-checkcard {
	font-size: 16px;
	width: 100%;
}

.question {
	line-height: 2.5;
	padding: 2px;
	font-size: 16px;
}

.question p {
	text-indent: 2em;
}

.question img {
	margin: 10px auto;
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