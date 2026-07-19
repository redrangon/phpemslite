<template>
	<div style="width:100%;">
		<template v-if="activeBar === 1">
			<van-nav-bar title="课程" placeholder fixed/>
			<div class="card-container">
				<van-cell-group v-if="courses.length >= 1" class="menu-list">
					<van-cell v-for="(lesson, index) in courses" :key="index" :is-link="true" :title="lesson.cstitle" center title-style="flex: 1;min-width: 0;" :to="'/mobile/course/detail/'+lesson.csid">
						<template #label>
							<div class="detailDesc" v-html="lesson.csdescribe"></div>
						</template>
					</van-cell>
				</van-cell-group>
				<div style="margin: 16px;">
					<van-button block type="default" @click="$router.push('/mobile/course/category/0')">
						查看更多
					</van-button>
				</div>
			</div>
		</template>
		<template v-else-if="activeBar === 2">
			<van-nav-bar title="考试" placeholder fixed/>
			<div class="card-container">
				<van-cell-group v-if="exams.length >= 1" class="menu-list">
					<van-cell v-for="(basic, index) in exams" :key="index" :is-link="true" :title="basic.basic" center title-style="flex: 1;min-width: 0;" :to="'/mobile/exam/detail/'+basic.basicid">
						<template #label>
							<div class="detailDesc" v-html="basic.basicdescribe"></div>
						</template>
					</van-cell>
				</van-cell-group>
				<div style="margin: 16px;">
					<van-button block type="default" @click="$router.push('/mobile/exam')">
						查看更多
					</van-button>
				</div>
			</div>
		</template>
		<template v-else-if="activeBar === 3">
			<van-nav-bar title="新闻" placeholder fixed/>
			<div class="card-container">
				<van-cell-group class="menu-list">
					<van-cell  v-for="(content, index) in notices" :key="index" :is-link="true" :label="content.contentinputtime" :title="content.contenttitle" :to="'/mobile/content/content/'+content.contentid" center title-style="flex: 1;min-width: 0;"/>
				</van-cell-group>
				<div style="margin: 16px;">
					<van-button block type="default" @click="$router.push('/mobile/content/category/0')">
						查看更多
					</van-button>
				</div>
			</div>
		</template>
		<template v-else>
			<van-nav-bar title="中心" placeholder fixed/>
			<div class="card-container" style="padding:35px 25px;">
				<van-row gutter="15">
					<van-col span="8">
						<van-icon :name="user.userphoto" size="72"/>
					</van-col>
					<van-col span="15" style="text-align: left;">
						<div class="title">
							{{user.username}}
							<RouterLink to="/mobile/core/profile">
								<van-icon name="setting-o" size="22" style="float: right;"/>
							</RouterLink>
						</div>
						<RouterLink to="/mobile/core/verify">
							<p> {{user.usertruename??'点击认证姓名和通行证ID'}}</p>
						</RouterLink>

					</van-col>
				</van-row>
			</div>
            <div class="card-container" style="padding:20px 20px 10px 20px;margin-top:20px;">
                <div class="title">
                    <span style="line-height: 44px;">积分：{{userPoints||0}}</span>
                    <van-button type="primary" style="float:right;" @click="showRechargeDialog()">积分充值</van-button>
                </div>
            </div>
			<div class="card-container" style="margin-top:20px;">
				<van-cell-group class="menu-list">
					<van-cell title="我的课程" is-link to="/mobile/core/course" />
					<van-cell title="我的考试" is-link to="/mobile/core/exam" />
					<van-cell title="我的证书" is-link to="/mobile/core/cert" />
					<van-cell title="实名认证" is-link to="/mobile/core/verify" />
					<van-cell title="消费记录" is-link to="/mobile/core/expense" />
					<van-cell title="个人信息" is-link to="/mobile/core/profile" />
					<van-cell title="修改密码" is-link to="/mobile/core/password" />
					<van-cell title="微信绑定/解绑" is-link @click="bindWechat" v-if="wechatEnv" to="/mobile/core/wechat"/>
				</van-cell-group>
				<div style="margin: 16px;">
					<van-button block type="primary" @click="logout">
						退出登录
					</van-button>
				</div>
			</div>
		</template>
        <van-dialog v-model:show="rechargeDialog" :close-on-click-overlay="true">
            <van-cell-group inset>
                <van-radio-group v-model="rechargeValue">
                    <van-cell-group>
                        <van-cell class="price-cell" >
                            <template #title>
                                <div style="padding: 10px;font-size: 16px;text-align: center">
                                    <span class="price-days">1元 = 10积分</span>
                                </div>
                            </template>
                        </van-cell>
                        <van-cell v-for="coin in coins" :key="coin" @click="rechargeValue = coin" class="price-cell" >
                            <template #title>
                                <div style="padding: 10px;font-size: 16px;">
                                    <span class="price-days">{{ coin }} 积分</span>
                                </div>
                            </template>
                            <template #right-icon>
                                <van-radio :name="coin" />
                            </template>
                        </van-cell>
                    </van-cell-group>
                </van-radio-group>
            </van-cell-group>
            <template #footer>
                <div class="container-footer">
                    <van-button type="primary" size="large" @click="handleRecharge" style="border-radius: 0">
                        确认
                    </van-button>
                </div>
            </template>
        </van-dialog>
		<van-tabbar v-model="activeBar" placeholder>
			<van-tabbar-item icon="user-o">我的</van-tabbar-item>
			<van-tabbar-item icon="tv-o">课程</van-tabbar-item>
			<van-tabbar-item icon="notes-o">考试</van-tabbar-item>
			<van-tabbar-item icon="newspaper-o">新闻</van-tabbar-item>
		</van-tabbar>
	</div>
</template>
<style scoped>
.title{
    font-size: 1.2em;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px
}
.menu-list div{
	padding:20px;
	font-size: 16px;
	background: transparent;
}
.detailDesc {
    padding: 10px 0 !important;
    font-size: 14px !important;
    line-height: 1.5;
    max-height: 60px;
    overflow: hidden;
}
</style>
<script>
    import userApi from '@/framework/api/user.js';
    import wechatApi from '@/framework/api/wechat.js';
	import authApi from "@/framework/api/auth.js";
    import { useAuthStore} from '@/stores/auth.js';
    import { storeToRefs } from 'pinia'; // 必须引入
    import { useRouterStore } from '@/stores/router';
    import contentApi from "@/framework/api/content.js";
    import baseMixin from "@/framework/mixins/baseMixin.js";
    import courseApi from "@/framework/api/course.js";
    import examApi from "@/framework/api/exam.js";
    export default{
		name:'MobileCorePage',
		mixins: [baseMixin],
		setup(){
			const authStore = useAuthStore();
            const routerStore = useRouterStore();
            const { pageData } = storeToRefs(routerStore);
			return {authStore,pageData,routerStore};
		},
        data(){
            return{
                wechatEnv:false,
	            courses:[
		            {
						csid:1,
		                cstitle:'课程标题'
					}
	            ],
	            exams:[
					{
			            basicid:1,
			            basic:'考场标题'
	                }
				],
                notices:[],
                userPoints:0,
                rechargeDialog:false,
                rechargeValue: 10,
                coins:[10,50,100,200,500,1000],
	            page:{
		            current:1,
		            total:0,
		            limit:10
	            }
            }
        },
        async mounted(){
			await this.getData();
		},
        methods:{
            logout(){
                authApi.logout();
				this.$router.push('/mobile/auth/login');
            },
	        getData:async function(){
		        await this.execute(async () => {
                    const user = await userApi.getCurrentUser();
                    this.userPoints = user?.usercoin || 0;
                    const data = await contentApi.getContentList({
                        page:this.page.current,
                        limit:this.page.limit
                    });
                    this.notices = data?.data??[];
                    const courseData = await courseApi.getSubjectList({
                        page:this.page.current,
                        limit:this.page.limit
                    });
                    this.courses = courseData?.data??[];
                    const examData = await examApi.getBasicList({
                        page:this.page.current,
                        limit:this.page.limit
                    });
                    this.exams = examData?.data??[];
		        },null,null);
	        },
            showRechargeDialog: function () {
                this.rechargeDialog = true;
            },
            handleRecharge: async function () {
                await this.execute(async () => {
                    const data = await userApi.recharge(this.rechargeValue);
                    this.$router.push('/mobile/core/pay/' + data.ordersn);
                }, null, null);
            },
        },
	    computed:{
			'user':function(){
				return this.authStore.userInfo;
			},
            'activeBar':{
                get(){
                    return this.pageData?.MobileCorePage?.activeBar??0;
                },
                set(val){
                    this.routerStore.updatePageData('MobileCorePage', {
                        activeBar: val
                    });
                }
            }
	    }
	}
</script>
