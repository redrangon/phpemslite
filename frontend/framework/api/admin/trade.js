import http from '@/framework/http'
const tradeApi = {
    getOrderList: (params) => http.post('/trade/master/order/data',params),
    deleteOrder: (ids) => http.post('/trade/master/order/delete',{ids}),
    cancelOrder: (ids) => http.post('/trade/master/order/cancel',{ids}),
    payOrder: (ids) => http.post('/trade/master/order/pay',{ids}),
};
export default tradeApi;