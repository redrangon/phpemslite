export default [
    {
        path: '',
        redirect: '/mobile/core',
    },
    {
        path: 'auth',
        component: () => import('../views/mobile/auth/Index.vue'),
        children:[
            {
                path: '',
                redirect: '/mobile/auth/login',
            },
            {
                path: 'login',
                component: () => import('../views/mobile/auth/Login.vue'),
                meta: {
                    level: 0
                }
            },
            {
                path: 'register',
                component: () => import('../views/mobile/auth/Register.vue'),
                meta: {
                    level: 1
                }
            },
            {
                path: 'forget',
                component: () => import('../views/mobile/auth/Forget.vue'),
                meta: {
                    level: 1
                }
            }
        ]
    },
    {
        path: 'core',
        component:() => import('../views/mobile/core/Index.vue'),
        children:[
            {
                path:'',
                name: 'MobileCorePage',
                component:() => import('../views/mobile/core/Home.vue'),
                meta:{
                    level: 0,
                    keepAlive: true
                },
            },
            {
                path:'course',
                component:() => import('../views/mobile/core/Course.vue'),
                meta:{
                    level: 1
                }
            },
            {
                path:'exam',
                component:() => import('../views/mobile/core/Exam.vue'),
                meta:{
                    level: 1
                }
            },
            {
                path:'cert',
                component:() => import('../views/mobile/core/Cert.vue'),
                meta:{
                    level: 1
                }
            },
            {
                path:"pay/:ordersn",
                component:() => import('../views/mobile/core/Pay.vue'),
                meta:{
                    level: 3
                }
            },
            {
                path:'profile',
                component:() => import('../views/mobile/core/Profile.vue'),
                meta:{
                    level: 1
                }
            },
            {
                path:'expense',
                component:() => import('../views/mobile/core/Expense.vue'),
                meta:{
                    level: 1
                }
            },
            {
                path:'order',
                component:() => import('../views/mobile/core/Order.vue'),
                meta:{
                    level: 1
                }
            },
            {
                path:'wechat',component:() => import('../views/mobile/core/WeChat.vue'),meta:{level: 1}
            },
            {
                path:'verify',component:() => import('../views/mobile/core/Verify.vue'),meta:{level: 1}
            },
            {
                path:'password',component:() => import('../views/mobile/core/Password.vue'),meta:{level: 1}
            }
        ]
    },
    {
        path:'content',
        component:() => import('../views/mobile/content/Index.vue'),
        children:[
            {
                path: '',
                redirect: '/mobile/core'

            },
            {
                path:'category/:catid',
                meta:{
                    level: 3,
                    keepAlive:true,
                },
                component:() => import('../views/mobile/content/Category.vue')
            },
            {
                path:'content/:contentid',
                name:"ContentPage",
                meta:{
                    level: 4,
                    keepAlive:true,
                },
                component:() => import('../views/mobile/content/Content.vue')
            }
        ]
    },
    {
        path:'course',
        component:() => import('../views/mobile/course/Index.vue'),
        children:[
            {
                path: '',
                redirect: '/mobile/course/course'

            },
            {
                path:'category/:catid',
                meta:{
                    level: 3
                },
                component:() => import('../views/mobile/course/Category.vue')
            },
            {
                path:'detail/:csid',
                meta:{
                    level: 4
                },
                component:() => import('../views/mobile/course/CourseDetail.vue')
            },
            {
                path:'course',
                meta:{
                    level: 5
                },
                component:() => import('../views/mobile/course/Course.vue')
            }
        ]
    },
    {
        path:"exam",
        component:() => import('../views/mobile/exam/Index.vue'),
        children:[
            {
                path:"",component:() => import('../views/mobile/exam/Home.vue'),
                meta:{
                    level: 2
                }
            },
            {
                path:"detail/:basicid",component:() => import('../views/mobile/exam/ExamDetail.vue'),
                meta:{
                    level: 3
                }
            },
            {
                path:"basic",component:() => import('../views/mobile/exam/Basic.vue'),
                meta:{
                    level: 3
                }
            },
            {
                path:"exam",component:() => import('../views/mobile/exam/Exam.vue'),
                meta:{
                    level: 4
                }
            },
            {
                path:'examhistory',
                component:() => import('../views/mobile/exam/ExamHistory.vue'),
                meta:{
                    level: 5
                }
            },
            {
                path:"exercise",component:() => import('../views/mobile/exam/Exercise.vue'),
                meta:{level: 4}
            },
            {
                path:"exercise/:pointid",component:() => import('../views/mobile/exam/ExerciseQuestion.vue'),
                meta:{level: 5}
            },
            {
                path:"exampaper",component:() => import('../views/mobile/exam/ExamPaper.vue'),
                meta:{level: 4}
            },
            {
                path:"result/:ehid",component:() => import('../views/mobile/exam/Result.vue'),
                meta:{level: 6}
            },
            {
                path:"view/:ehid",component:() => import('../views/mobile/exam/View.vue'),
                meta:{level: 7}
            },
            {
                path:"history",component:() => import('../views/mobile/exam/History.vue'),
                meta:{level: 4}
            },
            {
                path:"paper/:sessionid",component:() => import('../views/mobile/exam/Paper.vue'),
                meta:{level: 5}
            },
            {
                path:"favor",component:() => import('../views/mobile/exam/Favor.vue'),
                meta:{level: 4}
            },
        ]
    }
];