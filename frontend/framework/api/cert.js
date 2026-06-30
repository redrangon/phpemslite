import http from '@/framework/http'
const certApi = {
    getCertList: (params) => http.post('/cert/app/index/data',params),
    getMyCertList: (search) => http.post('/cert/app/cert/data',search),
    getMyCertImage: (cemId) => http.post('/cert/app/cert/image',{cemId}),
}
export default certApi;