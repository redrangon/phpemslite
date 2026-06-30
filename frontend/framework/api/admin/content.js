import http from '@/framework/http'
const contentApi = {
    getCategoryList: (params) => http.post('/content/master/category/data',params),
    getCategory: (catid) => http.post('/content/master/category',{catid}),
    getCategroyTree: () => http.post('/content/master/category/tree'),
    delCategory: (ids) => http.post('/content/master/category/delete', {ids}),
    modifyCategory: (category) => http.post('/content/master/category/modify', category),
    addCategory: (category) => http.post('/content/master/category/add', category),
    getContentList: (params) => http.post('/content/master/content/data',params),
    getContent: (contentid) => http.post('/content/master/content',{contentid}),
    delContent: (ids) => http.post('/content/master/content/delete', {ids}),
    modifyContent: (content) => http.post('/content/master/content/modify', content),
    addContent: (content) => http.post('/content/master/content/add', content),
};
export default contentApi;