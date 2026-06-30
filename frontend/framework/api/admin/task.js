import http from '@/framework/http'
const taskApi = {
    getSubjects: () => http.post('/exam/master/task'),
    getTasks: (search) => http.post('/exam/master/task/data', search),
    addTask: (task) => http.post('/exam/master/task/add', task),
    modifyTask: (task) => http.post('/exam/master/task/edit', task),
    delTask: (taskId) => http.post('/exam/master/task/del', { ids: [taskId] }),
    delTasks: (taskIds) => http.post('/exam/master/task/del', { ids: taskIds }),
    uploadData: (params) => http.post('/exam/master/task/import', params),
};
export default taskApi;