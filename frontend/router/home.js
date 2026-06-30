export default [
    {
        path: '',
        redirect: '/desktop/home/core/home',
    },
    {
        path: 'auth',
        component: () => import('../views/desktop/home/auth/Index.vue'),
        children:[
            {
                path: '',
                redirect: '/desktop/home/auth/login',
            },
            {
                path: 'login',
                component: () => import('../views/desktop/home/auth/Login.vue'),
                meta: {requiresAuth: false}
            },
            {
                path: 'register',
                component: () => import('../views/desktop/home/auth/Register.vue'),
                meta: {requiresAuth: false}
            },
            {
                path: 'forget',
                component: () => import('../views/desktop/home/auth/Forget.vue'),
                meta: {requiresAuth: false}
            }
        ]
    },
    {
        path: 'core',
        component: () => import('../views/desktop/home/core/Index.vue'),
        children:[
            {
                path: '',
                redirect: '/desktop/home/core/home',
                meta:{lm:1}
            },
            {
                path:"home",
                component:() => import('../views/desktop/home/core/Home.vue'),
                meta:{
                    lm:"1",
                },
            },
            {
                path:"course",
                component:() => import('../views/desktop/home/core/Course.vue'),
                meta:{
                    lm:"2",
                },
            },
            {
                path:"exam",
                component:() => import('../views/desktop/home/core/Exam.vue'),
                meta:{
                    lm:"3",
                },
            },
            {
                path:"cert",
                component:() => import('../views/desktop/home/core/Cert.vue'),
                meta:{
                    lm:"4",
                }
            },
            {
                path:"expense",
                component:() => import('../views/desktop/home/core/Expense.vue'),
                meta:{
                    lm:"5",
                },
            },
            {
                path:"order",
                component:() => import('../views/desktop/home/core/Order.vue'),
                meta:{
                    lm:"5",
                },
            },
            {
                path:"pay/:ordersn",
                component:() => import('../views/desktop/home/core/Pay.vue'),
                meta:{
                    lm:"5",
                },
            },
            {
                path:"profile",
                component:() => import('../views/desktop/home/core/Profile.vue'),
                meta:{
                    lm:"6"
                },
            }
        ]
    },
    {
        path:'content',
        component:() => import('../views/desktop/home/content/Index.vue'),
        children:[
            {
                path: '',
                component:() => import('../views/desktop/home/content/Home.vue'),
            },
            {
                path: 'category/:categoryId',
                meta:{
                    keepAlive: true,
                },
                component:() => import('../views/desktop/home/content/Category.vue'),
            },
            {
                path: 'content/:contentId',
                component:() => import('../views/desktop/home/content/Content.vue'),
            }
        ]
    },
    {
        path:'cert',
        component:() => import('../views/desktop/home/cert/Index.vue'),
        children:[
            {
                path: '',
                component:() => import('../views/desktop/home/cert/Home.vue'),
            },
        ]
    },
    {
        path:'course',
        component:() => import('../views/desktop/home/course/Index.vue'),
        children:[
            {
                path: '',
                component:() => import('../views/desktop/home/course/Home.vue'),
            },
            {
                path: 'category/:categoryId',
                component:() => import('../views/desktop/home/course/Category.vue'),
            },
            {
                path:'course',
                component:() => import('../views/desktop/home/course/Course.vue')
            },
            {
                path:'detail/:csId',
                name:'home.course.detail',
                component:() => import('../views/desktop/home/course/CourseDetail.vue')
            }
        ]
    },
    {
        path:'exam',
        meta: {requiresAuth: false},
        component:() => import('../views/desktop/home/exam/Index.vue'),
        children:[
            {
                path: '',
                name:'examHome',
                meta:{
                    hideMenu:true
                },
                component:() => import('../views/desktop/home/exam/Home.vue')
            },
            {
                path: 'basic',
                component:() => import('../views/desktop/home/exam/Basic.vue')
            },
            {
                path: 'basic/:basicId',
                meta:{
                    hideMenu:true
                },
                component:() => import('../views/desktop/home/exam/ExamDetail.vue')
            },
            {
                path:'exam',
                component:() => import('../views/desktop/home/exam/Exam.vue')
            },
            {
                path:'examhistory',
                component:() => import('../views/desktop/home/exam/ExamHistory.vue'),
                meta:{
                    lm:"2",
                    keepAlive:true,
                }
            },
            {
                path:'exampaper',
                component:() => import('../views/desktop/home/exam/ExamPaper.vue'),
                meta:{lm:"2"}
            },
            {
                path:'result/:ehid',
                component:() => import('../views/desktop/home/exam/Result.vue'),
                meta:{
                    lm:"2"
                }
            },
            {
                path:'exercise',
                component:() => import('../views/desktop/home/exam/Exercise.vue'),
                meta:{tm:"1",lm:"1"}
            },
            {
                path:'exercise/:pointid',
                component:() => import('../views/desktop/home/exam/ExerciseQuestion.vue'),
                meta:{tm:"1",lm:"1"}
            },
            {
                path:'favor',
                component:() => import('../views/desktop/home/exam/Favor.vue'),
                meta:{
                    lm:"4"
                }
            },
            {
                path:'history',
                component:() => import('../views/desktop/home/exam/History.vue'),
                meta:{
                    lm:"3"
                }
            },
            {
                path:'question',
                component:() => import('../views/desktop/home/exam/Question.vue'),
                meta:{
                    lm:"5"
                }
            }
        ]
    },
    {
        path:'exam/paper/:sessionid',
        component:() => import('../views/desktop/home/exam/Paper.vue'),
        props: true
    },
    {
        path:'exam/historyview/:ehid',
        component:() => import('../views/desktop/home/exam/HistoryView.vue')
    },
];