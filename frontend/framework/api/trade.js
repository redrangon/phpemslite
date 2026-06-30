import http from '@/framework/http'
const tradeApi = {
    getMyOrders: () => http.post('/trade/app/order/data'),
    getOrder:(orderSn) => http.post('/trade/app/order/',{orderSn}),
    payOrder: (params) => http.post('/trade/app/order/pay',params),
    cancelOrder: (orderSn) => http.post('/trade/app/order/cancel',{orderSn}),
};
export default tradeApi;