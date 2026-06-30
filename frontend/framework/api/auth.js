import http from '@/framework/http'
const authApi = {
    login: (params) => http.post('/user/app/login/login',params),
    logout: () => http.post('/user/app/login/logout').then(() => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
    }),
    register: (params) => http.post('/user/app/login/register',params),
    findpassword: (params) => http.post('/user/app/login/findpassword',params),
    getRegisterCode: (params) => http.post('/user/app/login/regcode',params),
    getFindPasswordCode: (params) => http.post('/user/app/login/findcode',params),
}
export default authApi;