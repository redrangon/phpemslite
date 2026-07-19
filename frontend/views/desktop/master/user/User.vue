<template>
	<lay-card>
		<lay-space direction="vertical">
			<lay-space size="lg">
				<lay-space></lay-space>
				<lay-space>
					<span style='width:70px'> 用户ID：</span><lay-input v-model="search.userid"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 用户名：</span><lay-input v-model="search.username"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 邮箱：</span><lay-input v-model="search.useremail"></lay-input>
				</lay-space>
				<lay-space>
					<span style='width:70px'> 用户组：</span>
					<lay-select v-model="search.groupid" placeholder="请选择" :allow-clear="true">
						<lay-select-option :value="group.groupid" :label="group.groupname" v-for="group in groups"></lay-select-option>
					</lay-select>
				</lay-space>
			</lay-space>
			<lay-space size="lg">
				<lay-space></lay-space>
				<lay-space>
					<span style='width:70px'> 起止时间：</span>
					<lay-date-picker v-model="search.range" range :placeholder="['开始日期','结束日期']" :allow-clear="true"></lay-date-picker>
				</lay-space>
				<lay-space>
					<lay-button type="primary" @click="getData">搜索</lay-button>
				</lay-space>
			</lay-space>
		</lay-space>
	</lay-card>
	<lay-card>
		<lay-table :default-toolbar="false" @change="getData" :columns="columns" :data-source="dataSource" ref="table" id="userid" v-model:selected-keys="selectedKeys" even>
			<template #toolbar>
                <lay-space>
                    <lay-button type="primary" size="sm" @click="showAddPage = true">添加用户</lay-button>
                    <lay-button type="primary" size="sm" @click="importUser">导入用户</lay-button>
                    <a href="public/attach/user.xlsx">
                        <lay-button type="primary" size="sm">下载导入模板</lay-button>
                    </a>
                </lay-space>
			</template>
			<template #footer>
				<lay-col md="12">
					<lay-button type="primary" size="sm" :disabled="selectedKeys.length < 1" @click="delUser()">删除选中用户</lay-button>
				</lay-col>
				<lay-col md="12">
					<lay-page v-model="page.current" v-model:limit="page.limit" :total="page.total" :layout="layout" @change="changePage" style="float:right;"></lay-page>
				</lay-col>
			</template>
			<template v-slot:operator="{ row }">
                <lay-space>
                    <router-link :to="`/desktop/master/user/log/${row.userid}`">
                        <lay-button size="xs" type="primary">日志</lay-button>
                    </router-link>
                    <router-link :to="`/desktop/master/user/coin/${row.userpassport}`">
                        <lay-button size="xs" type="primary">消费</lay-button>
                    </router-link>
                    <lay-button size="xs" type="primary" @click="showModify(row)">编辑</lay-button>
                    <lay-button size="xs" type="primary" @click="showModifyPassword(row)">改密</lay-button>
                    <lay-button size="xs" type="danger" @click="delUser(row.userid)">删除</lay-button>
                </lay-space>
			</template>
		</lay-table>
	</lay-card>
	<lay-layer v-model="showAddPage" :area="['800px']" :btn="addPageBtns" title="添加用户">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="model" ref="addPageForm">
				<lay-form-item label="照片" prop="userphoto">
					<myThumb v-model:src="model.userphoto" style="width: 90px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="用户名" prop="username" required>
					<lay-input v-model="model.username"></lay-input>
				</lay-form-item>
				<lay-form-item label="邮箱" prop="useremail" required>
					<lay-input v-model="model.useremail"></lay-input>
				</lay-form-item>
				<lay-form-item label="密码" prop="userpassword" required>
					<lay-input v-model="model.userpassword" password></lay-input>
				</lay-form-item>
				<lay-form-item label="用户组" prop="usergroupid" required>
					<lay-select v-model="model.usergroupid" placeholder="请选择">
						<lay-select-option :value="group.groupid" :label="group.groupname" v-for="group in groups"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="姓名" prop="usertruename" required>
					<lay-input v-model="model.usertruename"></lay-input>
				</lay-form-item>
				<lay-form-item label="性别" prop="usergender" required>
					<lay-radio v-model="model.usergender" name="usergender" value="男" label="男"></lay-radio>
					<lay-radio v-model="model.usergender" name="usergender" value="女" label="女"></lay-radio>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPage" :area="['800px']" :btn="modifyPageBtns" title="修改用户">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modelModify" ref="modifyPageForm">
				<lay-form-item label="照片" prop="userphoto">
					<myThumb v-model:src="modelModify.userphoto" style="width: 90px;height:120px;"></myThumb>
				</lay-form-item>
				<lay-form-item label="用户组" prop="usergroupid" required>
					<lay-select v-model="modelModify.usergroupid" placeholder="请选择">
						<lay-select-option :value="group.groupid" :label="group.groupname" v-for="group in groups"></lay-select-option>
					</lay-select>
				</lay-form-item>
				<lay-form-item label="姓名" prop="usertruename">
					<lay-input v-model="modelModify.usertruename"></lay-input>
				</lay-form-item>
				<lay-form-item label="性别" prop="usergender">
					<lay-radio v-model="modelModify.usergender" name="usergender" value="男" label="男"></lay-radio>
					<lay-radio v-model="modelModify.usergender" name="usergender" value="女" label="女"></lay-radio>
				</lay-form-item>
				<lay-form-item label="通行证ID" prop="userpassport">
					<lay-input v-model="modelModify.userpassport" :disabled="true"></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
	<lay-layer v-model="showModifyPasswordPage" :shade="false" :area="['500px']" :btn="modifyPasswordPageBtns" title="修改密码">
		<div style="padding: 20px 50px 20px 20px;">
			<lay-form :model="modelModifyPassword" ref="modifyPasswordPageForm" required>
				<lay-form-item label="新密码" prop="password">
					<lay-input v-model="modelModifyPassword.password"></lay-input>
				</lay-form-item>
			</lay-form>
		</div>
	</lay-layer>
</template>
<style scoped></style>
<script setup>
import userApi from '@/framework/api/admin/user.js';
import {layer} from '@layui/layui-vue';
import {ref, onMounted} from 'vue';
import myThumb from '@/components/desktop/Thumb.vue';
import {withConfirm} from "@/framework/utils/decorators.js";
import {withLayer} from "@/framework/utils/decorator-adapter.js";

// 定义响应式数据
const columns = ref([{
	title:"选项",
	width: "55px",
	type: "checkbox",
	fixed: "left",
},{
	title:'ID',
	key:'userid',
	width:'60px'
},{
	title:'用户名',
	key:'username',
	width:'200px'
},{
	title:'通行证ID',
	key:'userpassport',
	width:'240px'
},{
	title:'邮箱',
	key:'useremail'
},{
	title:'姓名',
	key:'usertruename',
	width:'150px'
},{
    title:'积分',
    key:'usercoin',
    width:'100px'
},{
	title:'用户组',
	key:'groupname',
	width:'150px'
},{
	title:'注册时间',
	key:'userregtime',
	width:'120px'
},{
	title:"操作",
	width: "230px",
	customSlot:"operator",
	key:"operator",
	fixed: "right",
	ignoreExport: true ,
}]);

const dataSource = ref([]);
const selectedKeys = ref([]);
const page = ref({ current: 1, limit: 20, total: 0 });
const layout = ref(['count', 'prev', 'page', 'next', 'limits',  'refresh', 'skip']);
const search = ref({});
const subjectRight = ref({});
const showAddPage = ref(false);
const showModifyPage = ref(false);
const showModifyPasswordPage = ref(false);
const groups = ref([
	{
		groupid: "1",
		groupname: "超级管理员"
	}
]);
const model = ref({});
const modelModify = ref({});
const modelModifyPassword = ref({userId:'',password:''});
const addPageForm = ref();
const modifyPageForm = ref();
const modifyPasswordPageForm = ref();
// 按钮配置
const addPageBtns = ref([
	{
		text: "确认",
		callback: () => {
			addPageForm.value.validate().then((res) => {
				showAddPage.value = false;
				addUser();
			}).catch( res => {
				console.log(res);
			});
		}
	},
	{
		text: "取消",
		callback: () => {
			showAddPage.value = false;
		}
	}
]);

const modifyPageBtns = ref([
	{
		text: "确认",
		callback: () => {
			modifyPageForm.value.validate().then((res) => {
				showModifyPage.value = false;
				modifyUser();
			}).catch( res => {
				console.log(res);
			});
		}
	},
	{
		text: "取消",
		callback: () => {
			showModifyPage.value = false;
		}
	}
]);

const modifyPasswordPageBtns = ref([
	{
		text: "确认",
		callback: () => {
            modifyPasswordPageForm.value.validate().then((res) => {
				showModifyPasswordPage.value = false;
				modifyUserPassword();
			}).catch( res => {
				console.log(res);
			});
		}
	},
	{
		text: "取消",
		callback: () => {
			showModifyPasswordPage.value = false;
		}
	}
]);

// 方法定义
const getData = async() => {
    await withLayer(async () => {
        const data = await userApi.getUsers({
            limit: page.value.limit,
            page: page.value.current,
            search: search.value
        });
        page.value.total = data.total;
        dataSource.value = data.data;
    },[null,null]);
};

const getGroups = async() => {
    await withLayer(async () => {
        groups.value = await userApi.getGroups();
    },[null,null]);
};

const changePage = ({ current, limit }) => {
	page.value.current = current;
	page.value.limit = limit;
	getData();
};

const delUser = (id) => {
	let ids = selectedKeys.value;
	if(id){
		ids = [id];
	}
	withConfirm(
		'确定要删除吗？',
		async () => {
			await userApi.deleteUsers(ids);
		},
		getData
	)
};

const addUser = async () => {
	await userApi.addUser(model.value);
	await getData();
};

const modifyUser = async () => {
	await userApi.modifyUser(modelModify.value);
	await getData();
};

const modifyUserPassword = async () => {
	await userApi.modifyPassword(modelModifyPassword.value.userId,modelModifyPassword.value.password);
	await getData();
};

const importUser = () => {
	const input = document.createElement('input');
	input.setAttribute('type', 'file');
	input.setAttribute('accept', '.xlsx');
	input.click();
	input.onchange = async () => {
        await withLayer(async() => {
            const formData = new FormData();
            formData.append('file', input.files[0], input.files[0].name );
            await userApi.importUsers(formData);
            await getData();
        },[null,null])
	};
};

const showModify = (row) => {
	showModifyPage.value = true;
	modelModify.value = {...row};
};

const showModifyPassword = (row) => {
	showModifyPasswordPage.value = true;
	modelModifyPassword.value = {
        userId: row.userid,
        password: ''
    };
};

const showCoin = (row) => {
	layer.open({
		type: 2,
		title: '用户钱包',
		shadeClose: true,
		shade: 0.8,
		area: ['500px', '300px'],
		content: ['/admin/user/wallet?userid=' + row.userid, true]
	});
};

const downloadTemplate = (tpl) => {
	window.location.href="../../files/public/usertpl.xlsx";
};

// 在组件挂载时执行初始化逻辑
onMounted(async () => {
	await getGroups();
	await getData();
});
</script>