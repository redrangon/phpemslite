import http from '@/framework/http'
const userApi = {
    getCurrentUser: () => http.get('/user/app/index/data'),
    verifyUser: (params) => http.post('/user/app/index/verify',params),
    modifyProfile: (params) => http.post('/user/app/index/profile', params),
    modifyPassword: (password) => http.post('/user/app/index/password', password),
    cancelVerify: (password) => http.post('/user/app/index/cancel', password),
    getLastStudy: () => http.get('/user/app/index/laststudy'),
    getMyCourse:(params) => http.post('/user/app/index/course', params),
    getMyBasic:(params) => http.post('/user/app/index/basic', params),
    getMyExpense: (params) => http.post('/user/app/index/expense', params),
    getRegisterSetting: () => http.get('/user/app/login/setting'),
    recharge: (amount) => http.post('/user/app/index/recharge', {amount}),
};
export default userApi;