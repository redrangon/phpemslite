import http from '@/framework/http'
const userApi = {
    getUsers: (params) => http.post('/user/master/user/data',params),
    getUser: (userId) => http.get('/user/master/user/user/' + userId),
    modifyPassword: (userId,password) => http.post('/user/master/user/password', {userId,password}),
    setUserVerifyStatus: (ids, status) => http.post('/user/master/user/verify', {ids,status}),
    addUser: (user) => http.post('/user/master/user/add', user),
    modifyUser: (user) => http.post('/user/master/user/modify', user),
    deleteUser: (userId) => http.post('/user/master/user/del/', {ids:[userId]}),
    deleteUsers: (ids) => http.post('/user/master/user/del/', {ids}),
    getGroups:(params) => http.post('/user/master/user/groups',params),
    getGroup:(groupId) => http.get('/user/master/user/group/' + groupId),
    addGroup: (group) => http.post('/user/master/user/addgroup', group),
    modifyGroup: (group) => http.post('/user/master/user/modifygroup', group),
    deleteGroup: (groupId) => http.post('/user/master/user/delgroup' , {ids:[groupId]}),
    deleteGroups: (ids) => http.post('/user/master/user/delgroup' , {ids}),
    importUsers: (params) => http.post('/user/master/user/import', params),
    setConfig: (params) => http.post('/user/master/user/setconfig', params),
    getConfig: () => http.get('/user/master/user/getconfig'),
    setDefaultGroup: (groupId) => http.post('/user/master/user/defaultgroup', {groupId}),
};
export default userApi;