<template>
	<lay-side width="auto" class="mySide">
		<lay-menu :tree="true" class="myMenu" :selected-key="selectedKey" v-model:openKeys="openKeys" :indent="true" @changeSelectedKey="changeSelectedKey">
			<div style="text-align: center;">
				<h1 class="logo">PHPEMS</h1>
			</div>
			<div v-if="module === 'plan'">
				<router-link to="/desktop/master/plan">
					<lay-menu-item id="1">
						<template #title>
							<lay-icon type="layui-icon-tips"></lay-icon>
							模块简介
						</template>
					</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/plan/plan">
					<lay-menu-item id="2">
						<lay-icon type="layui-icon-cols"></lay-icon>
						培训计划
					</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/plan/cert">
					<lay-menu-item id="4">
						<lay-icon type="layui-icon-carousel"></lay-icon>
						凭证管理
					</lay-menu-item>
				</router-link>
				<!--
				<router-link to="/desktop/master/plan/search">
					<lay-menu-item id="5">
						<lay-icon type="layui-icon-search"></lay-icon>
						综合查询
					</lay-menu-item>
				</router-link>
				-->
			</div>
			<div v-else-if="module === 'user'">
				<router-link to="/desktop/master/user">
					<lay-menu-item id="1">
						<template #title>
							<lay-icon type="layui-icon-tips"></lay-icon>
							模块简介
						</template>
					</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/user/user">
					<lay-menu-item id="2"><lay-icon type="layui-icon-username"></lay-icon> 用户管理</lay-menu-item>
				</router-link>
                <router-link to="/desktop/master/user/verify">
					<lay-menu-item id="5"><lay-icon type="layui-icon-vercode"></lay-icon> 实名认证</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/user/group">
					<lay-menu-item id="3"><lay-icon type="layui-icon-user"></lay-icon> 用户组管理</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/user/setting">
					<lay-menu-item id="4"><lay-icon type="layui-icon-set"></lay-icon> 模块设置</lay-menu-item>
				</router-link>
			</div>
			<div v-else-if="module === 'exam'">
				<router-link to="/desktop/master/exam">
					<lay-menu-item id="1">
						<template #title>
							<lay-icon type="layui-icon-tips"></lay-icon>
							模块简介
						</template>
					</lay-menu-item>
				</router-link>
				<lay-sub-menu id="2">
					<template #title>
						<lay-icon type="layui-icon-read"></lay-icon> 考试设计
					</template>
					<router-link to="/desktop/master/exam/basic">
						<lay-menu-item id="21">考场管理</lay-menu-item>
					</router-link>
					<router-link to="/desktop/master/exam/questype">
						<lay-menu-item id="22">题型管理</lay-menu-item>
					</router-link>
				</lay-sub-menu>
				<lay-sub-menu id="3">
					<template #title>
						<lay-icon type="layui-icon-help-circle"></lay-icon> 试题管理
					</template>
					<router-link to="/desktop/master/exam/subject">
						<lay-menu-item id="31">科目管理</lay-menu-item>
					</router-link>
					<router-link to="/desktop/master/exam/questions">
						<lay-menu-item id="32">普通试题</lay-menu-item>
					</router-link>
					<router-link to="/desktop/master/exam/rowsquestions">
						<lay-menu-item id="33">题冒试题</lay-menu-item>
					</router-link>
					<router-link to="/desktop/master/exam/feedback">
						<lay-menu-item id="34">错题反馈</lay-menu-item>
					</router-link>
				</lay-sub-menu>
				<router-link to="/desktop/master/exam/paper">
					<lay-menu-item id="4">
						<template #title>
							<lay-icon type="layui-icon-list"></lay-icon>
							试卷管理
						</template>
					</lay-menu-item>
				</router-link>
				<lay-sub-menu id="5">
					<template #title>
						<lay-icon type="layui-icon-set"></lay-icon> 模块管理
					</template>
					<!--
					<router-link to="/desktop/master/exam/tools">
						<lay-menu-item id="51">批量工具</lay-menu-item>
					</router-link>
					-->
					<router-link to="/desktop/master/exam/setting">
						<lay-menu-item id="52">模块设置</lay-menu-item>
					</router-link>
				</lay-sub-menu>
			</div>
            <div v-else-if="module === 'cert'">
                <router-link to="/desktop/master/cert">
                    <lay-menu-item id="1">
                        <template #title>
                            <lay-icon type="layui-icon-tips"></lay-icon>
                            模块简介
                        </template>
                    </lay-menu-item>
                </router-link>
                <router-link to="/desktop/master/cert/cert">
                    <lay-menu-item id="2">
                        <lay-icon type="layui-icon-carousel"></lay-icon> 证书管理
                    </lay-menu-item>
                </router-link>
            </div>
			<div v-else-if="module === 'attach'">
				<router-link to="/desktop/master/attach">
					<lay-menu-item id="1">
						<template #title>
							<lay-icon type="layui-icon-tips"></lay-icon>
							模块简介
						</template>
					</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/attach/type">
					<lay-menu-item id="2"><lay-icon type="layui-icon-transfer"></lay-icon> 附件类型</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/attach/attach">
					<lay-menu-item id="3"><lay-icon type="layui-icon-file"></lay-icon> 附件管理</lay-menu-item>
				</router-link>
			</div>
			<div v-else-if="module === 'member'">
				<router-link to="/desktop/master/member">
					<lay-menu-item id="1">
						<template #title>
							<lay-icon type="layui-icon-tips"></lay-icon>
							模块简介
						</template>
					</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/member/member">
					<lay-menu-item id="2"><lay-icon type="layui-icon-list"></lay-icon> 档案管理</lay-menu-item>
				</router-link>
			</div>
			<div v-else-if="module === 'trade'">
				<router-link to="/desktop/master/orders">
					<lay-menu-item id="1">
						<template #title>
							<lay-icon type="layui-icon-tips"></lay-icon>
							模块简介
						</template>
					</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/trade/order">
					<lay-menu-item id="2"><lay-icon type="layui-icon-list"></lay-icon> 订单管理</lay-menu-item>
				</router-link>
			</div>
			<div v-else-if="module === 'course'">
				<router-link to="/desktop/master/course">
					<lay-menu-item id="1">
						<template #title>
							<lay-icon type="layui-icon-tips"></lay-icon>
							模块简介
						</template>
					</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/course/category">
					<lay-menu-item id="2"><lay-icon type="layui-icon-list"></lay-icon> 分类管理</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/course/subject">
					<lay-menu-item id="3"><lay-icon type="layui-icon-video"></lay-icon> 课程管理</lay-menu-item>
				</router-link>
			</div>
			<div v-else-if="module === 'content'">
				<router-link to="/desktop/master/content">
					<lay-menu-item id="1">
						<template #title>
							<lay-icon type="layui-icon-tips"></lay-icon>
							模块简介
						</template>
					</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/content/category">
					<lay-menu-item id="2"><lay-icon type="layui-icon-list"></lay-icon> 分类管理</lay-menu-item>
				</router-link>
				<router-link to="/desktop/master/content/content">
					<lay-menu-item id="3"><lay-icon type="layui-icon-form"></lay-icon> 内容管理</lay-menu-item>
				</router-link>
			</div>
			<div v-else>
				<router-link to="/">
					<lay-menu-item id="1">
						<template #title>
							<lay-icon type="layui-icon-tips"></lay-icon>
							系统简介
						</template>
					</lay-menu-item>
				</router-link>
				<lay-menu-item id="2"><a href="http://www.phpems.net"><lay-icon type="layui-icon-website"></lay-icon> 帮助信息</a></lay-menu-item>
			</div>
		</lay-menu>
	</lay-side>
</template>
<script setup>
import {ref,onMounted,inject,watch} from 'vue';
import {useRouter,useRoute} from "vue-router";

const router = useRouter();
const route = useRoute();
const module = inject('module');
const selectedKey = inject('menu');
const openKeys = ref([]);
const changeSelectedKey = function(val){
	selectedKey.value = val;
}

watch(
	() => route.meta,
	(meta) => {
		if (meta?.menu !== undefined) {
			selectedKey.value = meta.menu
		} else {
			selectedKey.value = "core"
		}
		if (meta?.openMenu) {
			openKeys.value = [meta.openMenu]
		} else {
			openKeys.value = []
		}
	},
	{ immediate: true }
)

onMounted(() => {
	if(route.meta?.menu)selectedKey.value = route.meta.menu;
	else selectedKey.menu = "1";
	if(route.meta?.openMenu)openKeys.value = [route.meta.openMenu];
})
</script>

<style scoped>
.mySide{
	align-items: unset!important;
}
.myMenu{
	padding:5px 10px;
	border-radius: 0px;
}
.myMenu .layui-nav-item,.myMenu .layui-nav-item:hover{
	border-radius: 5px;
}
.logo{
	font-size: 42px;
	font-weight: 300;
	line-height: 1.5;
	color:#16A98C;
	padding-bottom: 20px;
	font-family: fantasy;
	letter-spacing: 1px;
}
</style>
