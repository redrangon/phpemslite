import http from '@/framework/http'
const utilsApi = {
    getRandCode: () => http.get('/core/utils/index/captcha'),
};
export default utilsApi;