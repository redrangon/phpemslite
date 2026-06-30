import http from '@/framework/http'
const orderApi = {
    getOrders: (params) => http.post('/admin/trade',params),
    modifyOrders: (params) => http.post('/admin/trade/modify',params),
    delOrder: (orderId) => http.post('/admin/trade/del',{ids:[orderId]}),
    delOrders: (ids) => http.post('/admin/trade/del',{ids}),

}
export default orderApi;