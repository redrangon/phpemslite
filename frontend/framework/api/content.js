import http from '@/framework/http'
const contentApi = {
    getContentList: (params) => http.post('/content/app/content/data',params),
    getContent: (contentId) => http.post('/content/app/content/index',{contentId}),
    getCategoryList: (catId) => http.post('/content/app/category/data',{catId}),
    getCategory: (catId) => http.post('/content/app/category',{catId}),
};
export default contentApi;