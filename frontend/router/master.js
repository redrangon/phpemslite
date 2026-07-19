export default [
    {
        path: '',
        redirect: '/desktop/master/dashboard',
    },
    {
        path: 'dashboard',
        meta: {
            module:"core",
            menu:"1",
            breadcrumb: (route) => [
                { title: '首页', path: '/desktop/master' }
            ]
        },
        component: () => import('@/views/desktop/master/dashboard/Index.vue')
    },
    {
        path: 'exam/view/:ehid',
        component: () => import('@/views/desktop/master/exam/ExamView.vue'),
        meta:{
            menu:"21",
            openMenu:"2",
            breadcrumb: (route) => [
                { title: '首页', path: '/desktop/master' },
                { title: '考试', path: '/desktop/master/exam' },
                { title: '考场管理', path: '/desktop/master/exam/basic' },
                { title: '成绩' }
            ]
        },
    },
    {
        path: 'cert',
        component: () => import('@/views/desktop/master/cert/Index.vue'),
        meta: {
            module:"cert"
        },
        children:[
            {
                path: '',
                component: () => import('@/views/desktop/master/cert/Home.vue'),
                meta:{
                    menu:"1",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '证书管理', path: '/desktop/master/cert' }
                    ]
                },
            },
            {
                path: 'cert',
                component: () => import('@/views/desktop/master/cert/Cert.vue'),
                meta:{
                    menu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '证书管理', path: '/desktop/master/cert' },
                        {
                            title: `证书列表`,
                            path: route.path
                        }
                    ]
                },
            },
            {
                path: 'member/:ceid',
                component: () => import('@/views/desktop/master/cert/Member.vue'),
                meta:{
                    menu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '证书管理', path: '/desktop/master/cert' },
                        {
                            title: `证书列表`,
                            path:'/desktop/master/cert/cert'
                        },
                        {
                            title: `人员管理`
                        }
                    ]
                },
            },
        ]
    },
    {
        path: 'user',
        component: () => import('@/views/desktop/master/user/Index.vue'),
        meta: {
            module:"user"
        },
        children:[
            {
                path: '',
                component: () => import('@/views/desktop/master/user/Home.vue'),
                meta:{
                    menu:"1",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '用户管理', path: '/desktop/master/user' }
                    ]
                },
            },
            {
                path: 'user',
                component: () => import('@/views/desktop/master/user/User.vue'),
                meta:{
                    menu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '用户管理', path: '/desktop/master/user' },
                        {
                            title: `用户列表`,
                            path: route.path
                        }
                    ]
                },
            },
            {
                path: 'log/:userid',
                component: () => import('@/views/desktop/master/user/Log.vue'),
                meta: {
                    menu: "2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '用户模块', path: '/desktop/master/user' },
                        { title: '用户管理', path: '/desktop/master/user/user' },
                        { title: '登录日志', path: '/desktop/master/user/log' }
                    ]
                }
            },
            {
                path: 'coin/:passport',
                component: () => import('@/views/desktop/master/user/Coin.vue'),
                meta: {
                    menu: "2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '用户模块', path: '/desktop/master/user' },
                        { title: '用户管理', path: '/desktop/master/user/user' },
                        { title: '积分管理', path: '/desktop/master/user/coin' }
                    ]
                }
            },
            {
                path: 'verify',
                component: () => import('@/views/desktop/master/user/Verify.vue'),
                meta:{
                    menu:"5",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '用户管理', path: '/desktop/master/user' },
                        {
                            title: `实名认证`,
                            path: route.path
                        }
                    ]
                },
            },
            {
                path: 'group',
                component: () => import('@/views/desktop/master/user/Group.vue'),
                meta:{
                    menu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '用户管理', path: '/desktop/master/user' },
                        {
                            title: `用户组`,
                            path: route.path
                        }
                    ]
                }
            },
            {
                path: 'setting',
                component: () => import('@/views/desktop/master/user/Setting.vue'),
                meta:{
                    menu:"4",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '用户管理', path: '/desktop/master/user' },
                        {
                            title: `模块设置`,
                            path: route.path
                        }
                    ]
                }
            },
        ]
    },
    {
        path: 'member',
        component: () => import('@/views/desktop/master/member/Index.vue'),
        meta: {
            module:"member"
        },
        children:[
            {
                path: '',
                component: () => import('@/views/desktop/master/member/Home.vue'),
                meta:{
                    menu:"1",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '档案管理', path: '/desktop/master/member' }
                    ]
                },
            },
            {
                path: 'member',
                component: () => import('@/views/desktop/master/member/Member.vue'),
                meta:{
                    menu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '档案管理', path: '/desktop/master/,' },
                        {
                            title: `档案列表`,
                            path: route.path
                        }
                    ]
                },
            },
        ]
    },
    {
        path: 'course',
        component: () => import('@/views/desktop/master/course/Index.vue'),
        props: true,
        meta: {
            module:"course"
        },
        children: [
            {
                path: '',
                component: () => import('@/views/desktop/master/course/Home.vue'),
                meta:{
                    menu:"1",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '课程管理', path: '/desktop/master/course' }
                    ]
                },
            },
            {
                path: 'category',
                component: () => import('@/views/desktop/master/course/Category.vue'),
                meta:{
                    menu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '课程管理', path: '/desktop/master/course' },
                        { title: '课程分类', path: '/desktop/master/course/category' }
                    ]
                },
            }, {
                path: 'subject',
                component: () => import('@/views/desktop/master/course/Subject.vue'),
                meta:{
                    menu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '课程', path: '/desktop/master/course' },
                        { title: '课程管理', path: '/desktop/master/course/subject' }
                    ]
                },
            }, {
                path: 'member/:csid',
                component: () => import('@/views/desktop/master/course/Member.vue'),
                meta:{
                    menu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '课程', path: '/desktop/master/course' },
                        { title: '课程管理', path: '/desktop/master/course/subject' },
                        { title: '人员管理' },
                    ]
                },
            }, {
                path: 'price/:csid',
                component: () => import('@/views/desktop/master/course/Price.vue'),
                meta:{
                    menu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '课程', path: '/desktop/master/course' },
                        { title: '课程管理', path: '/desktop/master/course/subject' },
                        { title: '课程价格' },
                    ]
                },
            }, {
                path: 'course/:csid',
                component: () => import('@/views/desktop/master/course/Course.vue'),
                meta:{
                    menu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '课程', path: '/desktop/master/course' },
                        { title: '课程管理', path: '/desktop/master/course/course' }
                    ]
                },
            }, {
                path: 'course/:csid/:dirid',
                component: () => import('@/views/desktop/master/course/Course.vue'),
                meta:{
                    menu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '课程', path: '/desktop/master/course' },
                        { title: '课程管理', path: '/desktop/master/course/course' }
                    ]
                },
            }
        ]
    },
    {
        path: 'content',
        component: () => import('@/views/desktop/master/content/Index.vue'),
        meta: {
            module:"content"
        },
        children: [
            {
                path: '',
                component: () => import('@/views/desktop/master/content/Home.vue'),
                meta:{
                    menu:"1",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '内容管理', path: '/desktop/master/content' }
                    ]
                },
            }, {
                path: 'category',
                component: () => import('@/views/desktop/master/content/Category.vue'),
                meta:{
                    menu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '内容管理', path: '/desktop/master/content' },
                        { title: '分类管理', path: '/desktop/master/content/category' }
                    ]
                },
            }, {
                path: 'content',
                component: () => import('@/views/desktop/master/content/Content.vue'),
                meta:{
                    menu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '内容管理', path: '/desktop/master/content' },
                        { title: '内容管理', path: '/desktop/master/content/content' }
                    ]
                },
            }
        ]
    },
    {
        path: 'exam',
        component: () => import('@/views/desktop/master/exam/Index.vue'),
        meta: {
            module:"exam"
        },
        children: [
            {
                path: '',
                component: () => import('@/views/desktop/master/exam/Home.vue'),
                meta:{
                    menu:"1",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' }
                    ]
                },
            },
            {
                path: 'basic',
                component: () => import('@/views/desktop/master/exam/Basic.vue'),
                meta:{
                    menu:"21",
                    openMenu:"2",
                    keepAlive:true,
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/basic' }
                    ]
                },
            },
            {
                path: 'member/:basicid',
                component: () => import('@/views/desktop/master/exam/Member.vue'),
                meta:{
                    menu:"21",
                    openMenu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/basic' },
                        { title: '人员管理' },
                    ]
                },
            },
            {
                path: 'history/:basicid',
                component: () => import('@/views/desktop/master/exam/ExamScore.vue'),
                meta:{
                    menu:"21",
                    openMenu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/basic' },
                        { title: '成绩' }
                    ]
                },
            },
            {
                path: 'price/:basicid',
                component: () => import('@/views/desktop/master/exam/Price.vue'),
                meta:{
                    menu:"21",
                    openMenu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/basic' },
                        { title: '价格' }
                    ]
                },
            },
            {
                path: 'monitor/:basicid',
                component: () => import('@/views/desktop/master/exam/Monitor.vue'),
                meta:{
                    menu:"21",
                    openMenu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/basic' },
                        { title: '监考' }
                    ]
                },
            },
            {
                path: 'decide/:basicid',
                component: () => import('@/views/desktop/master/exam/Decide.vue'),
                meta:{
                    menu:"21",
                    openMenu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/basic' },
                        { title: '客观题评分'}
                    ]
                },
            },
            {
                path: 'questype',
                component: () => import('@/views/desktop/master/exam/QuestionType.vue'),
                meta:{
                    menu:"22",
                    openMenu:"2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/content/content' }
                    ]
                },
            },
            {
                path: 'subject',
                component: () => import('@/views/desktop/master/exam/Subject.vue'),
                meta:{
                    menu:"31",
                    openMenu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/content/content' }
                    ]
                },
            },
            {
                path: 'section/:subjectid',
                component: () => import('@/views/desktop/master/exam/Section.vue'),
                meta:{
                    menu:"31",
                    openMenu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '科目管理', path: '/desktop/master/exam/subject' },
                        { title: '章节管理', path: '/desktop/master/exam/section/' + route.params.subjectid },
                    ]
                },
            },
            {
                path: 'point/:subjectid/:sectionid',
                component: () => import('@/views/desktop/master/exam/Point.vue'),
                meta:{
                    menu:"31",
                    openMenu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '科目管理', path: '/desktop/master/exam/subject' },
                        { title: '章节管理', path: '/desktop/master/exam/section/' + route.params.subjectid },
                        { title: '知识点管理', path: '/desktop/master/exam/point/' +  route.params.subjectid + '/' + route.params.sectionid },
                    ]
                },
            },
            {
                path: 'questions',
                component: () => import('@/views/desktop/master/exam/Question.vue'),
                meta:{
                    menu:"32",
                    openMenu:"3",
                    keepAlive:true,
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/content' }
                    ]
                },
            },
            {
                path: 'questions/:questionid',
                component: () => import('@/views/desktop/master/exam/Question.vue'),
                meta:{
                    menu:"32",
                    openMenu:"3",
                    keepAlive:true,
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/content' }
                    ]
                },
            },
            {
                path: 'rowsquestions',
                component: () => import('@/views/desktop/master/exam/RowsQuestion.vue'),
                meta:{
                    menu:"33",
                    openMenu:"3",
                    keepAlive:true,
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '题冒试题管理', path: '/desktop/master/exam/rowsquestions/'}
                    ]
                },
            },
            {
                path: 'rowsquestions/:questionid',
                component: () => import('@/views/desktop/master/exam/RowsQuestion.vue'),
                meta:{
                    menu:"33",
                    openMenu:"3",
                    keepAlive:true,
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '题冒试题管理', path: '/desktop/master/exam/rowsquestions/'}
                    ]
                },
            },
            {
                path: 'children/:questionid',
                component: () => import('@/views/desktop/master/exam/ChildQuestion.vue'),
                meta:{
                    menu:"33",
                    openMenu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '题冒管理', path: '/desktop/master/exam/rowsquestions/'},
                        { title: '子题管理', path: '/desktop/master/exam/children/:' + route.params.questionid }
                    ]
                },
            },
            {
                path: 'feedback',
                component: () => import('@/views/desktop/master/exam/FeedBack.vue'),
                meta:{
                    menu:"34",
                    openMenu:"3",
                    keepAlive:true,
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/feedback' }
                    ]
                },
            },
            {
                path: 'recyle',
                component: () => import('@/views/desktop/master/exam/Recyle.vue'),
                meta:{
                    menu:"34",
                    openMenu:"3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/recyle' }
                    ]
                },
            },
            {
                path: 'paper',
                component: () => import('@/views/desktop/master/exam/Paper.vue'),
                meta:{
                    menu:"4",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/paper' }
                    ]
                },
            },
            {
                path: 'tools',
                component: () => import('@/views/desktop/master/exam/Tool.vue'),
                meta:{
                    menu:"51",
                    openMenu:"5",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/tools' }
                    ]
                },
            },
            {
                path: 'setting',
                component: () => import('@/views/desktop/master/exam/Setting.vue'),
                meta:{
                    menu:"52",
                    openMenu:"5",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '考试', path: '/desktop/master/exam' },
                        { title: '考场管理', path: '/desktop/master/exam/setting' }
                    ]
                },
            }
        ]
    },
    {
        path: 'attach',
        component: () => import('@/views/desktop/master/attach/Index.vue'),
        meta: {
            module:"attach"
        },
        children: [
            {
                path: '',
                component: () => import('@/views/desktop/master/attach/Home.vue'),
                meta: {
                    menu: "1",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '附件', path: '/desktop/master/attach' }
                    ]
                }
            },
            {
                path: 'type',
                component: () => import('@/views/desktop/master/attach/Type.vue'),
                meta: {
                    menu: "2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '附件', path: '/desktop/master/attach' },
                        { title: '附件类型', path: '/desktop/master/attach/type' }
                    ]
                }
            },
            {
                path: 'attach',
                component: () => import('@/views/desktop/master/attach/Attach.vue'),
                meta: {
                    menu: "3",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '附件', path: '/desktop/master/attach' },
                        { title: '附件类型', path: '/desktop/master/attach/attach' }
                    ]
                }
            }
        ]
    },
    {
        path: 'trade',
        component: () => import('@/views/desktop/master/trade/Index.vue'),
        meta: {
            module:"trade"
        },
        children: [
            {
                path: '',
                component: () => import('@/views/desktop/master/trade/Home.vue'),
                meta: {
                    menu: "1",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '交易', path: '/desktop/master/trade' }
                    ]
                }
            },
            {
                path: 'order',
                component: () => import('@/views/desktop/master/trade/Order.vue'),
                meta: {
                    menu: "2",
                    breadcrumb: (route) => [
                        { title: '首页', path: '/desktop/master' },
                        { title: '交易', path: '/desktop/master/trade' },
                        { title: '订单管理', path: '/desktop/master/trade/order' }
                    ]
                }
            }
        ]
    },
];