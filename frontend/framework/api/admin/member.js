import http from '@/framework/http'
const memberApi = {
    getMemberList: (search) => http.post('/member/master/member/data',search),
    getMember: (param) => http.post('/member/master/member',param),
    addMember:(plan) => http.post('/member/master/member/add',plan),
    modifyMember:(plan) => http.post('/member/master/member/modify',plan),
    delMember: (ids) =>  http.post('/member/master/member/delete',{ids}),
    importMember: (params) => http.post('/member/master/member/import',params),
};
export default memberApi;