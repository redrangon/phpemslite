<template>
	<div class="question" style="margin: 0;">
		<div class="topic">
			{{index+1}}、{{ questionType.questype }}
		</div>
		<div style="padding:10px">
			<div v-if="question.parent" style="padding:10px 5px 10px 5px;" v-html="question.parent"></div>
			<div style="padding:0 5px 10px 5px;" v-html="question.question"></div>
			<van-space direction="vertical" style="width: 100%;">
				<template v-if="questionType.questsort === 1">
                    <template v-if="!disabled">
                        <label class="selectCard">
                            <input v-model="answer" :disabled="disabled" :name="`answer_${question.questionid}`" type="radio" value="A">
                            <div class="selector">A、已掌握</div>
                        </label>
                        <label class="selectCard">
                            <input v-model="answer" :disabled="disabled" :name="`answer_${question.questionid}`" type="radio" value="B">
                            <div class="selector">B、未掌握</div>
                        </label>
                    </template>
				</template>
				<template v-else-if="questionType.questchoice === 1">
					<label v-for="(selector, sid) in question.questionhtml" :key="sid" class="selectCard">
						<input v-model="answer" :disabled="disabled" :name="`answer_${question.questionid}`" :value="selectors[sid]" type="radio">
						<div class="selector" v-html="selector"></div>
					</label>
				</template>
				<template v-else-if="questionType.questchoice === 4">
					<label class="selectCard">
						<input v-model="answer" :disabled="disabled" :name="`answer_${question.questionid}`" type="radio" value="A">
						<div class="selector">A、正确</div>
					</label>
					<label class="selectCard">
						<input v-model="answer" :disabled="disabled" :name="`answer_${question.questionid}`" type="radio" value="B">
						<div class="selector">B、错误</div>
					</label>
				</template>
				<template v-else-if="questionType.questchoice === 2 || questionType.questchoice === 3">
					<label v-for="(selector, sid) in question.questionhtml" :key="sid" class="selectCard">
						<input v-model="manswer" :disabled="disabled" :value="selectors[sid]" type="checkbox">
						<div class="selector" v-html="selector"></div>
					</label>
				</template>
				<template v-else-if="questionType.questchoice === 5">
					<textarea v-model="answer" :disabled="disabled" rows="2"></textarea>
				</template>
				<template v-else>
					<div>未知题型无法处理</div>
				</template>
			</van-space>
		</div>
	</div>
</template>

<script>
export default {
	name: 'Question',
	data() {
		return {
			answer: '', // 字符串，用于单选/填空
			manswer: [], // 数组，用于多选
			selectors: ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'],
			inited: false
		};
	},
	props: {
		question: { type: Object, required: true },
		questionType: { type: Object, required: true },
		userAnswer: { type: [String, null], default: '' },
		index: { type: Number, default: 0 },
		disabled: { type: Boolean, default: false }
	},
	emits: ['saveAnswer', 'update:userAnswer'],
	created() {
		this.inited = false;
		this.answer = this.userAnswer;

		if (!this.question.parent) {
			this.question.parent = '';
		}
		if (this.answer && this.questionType.questsort !== 1 && (this.questionType.questchoice === 2 || this.questionType.questchoice === 3)) {
			this.manswer = this.answer.split('').filter(c => c.trim());
		}
	},
	mounted() {
		this.inited = true;
	},
	watch: {
		manswer: {
			handler(newVal) {
				if (!Array.isArray(newVal)) return;
				const sorted = [...newVal].sort();
				this.answer = sorted.join('');
			},
			deep: true
		},
		answer(newVal, oldVal) {
			try{
				if (!this.inited) return;
				if (newVal !== oldVal) {
					this.$emit('update:userAnswer', newVal);
					this.$emit('saveAnswer');
				}
			}catch (e) {
				console.log(e.message);
			}
		}
	}
};
</script>

<style scoped>


.selectCard {
	line-height: 1.5;
	cursor: pointer;
	padding: 0;
}

.selectCard .selector {
	padding: 1em;
	background: #ffffff;
	border-radius: 5px;
	border: 1px solid #f2f2f2;
}

.selectCard input[type="radio"],
.selectCard input[type="checkbox"] {
	display: none;
}

input:checked + .selector {
	background: #007aff;
	border: 1px solid #007aff;
	color: #fff;
}

input:checked + .selector .vditor-reset {
	color: #fff;
}

.question {
	line-height: 2;
	font-size:16px;
	background: #ffffff;
	padding: 15px 10px;
	border-radius: 5px;
	margin-bottom: 10px;
}

.question .topic{
	border-bottom: 1px solid #f2f2f2;
	margin-bottom: 10px;
	padding: 8px 10px;
}
</style>