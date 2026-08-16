/**
 * 任务调度系统 API 模块
 * 包含任务计划（task_schedules）和任务队列（x2_task）的 mock 数据
 */
import http from "@/framework/http/index.js";

const taskApi = {
    // ========== 任务计划 CRUD ==========
    getEventList: () => http.post('/task/master/index/event'),
    getScheduleList: (params) => http.post('/task/master/schedule/data',params),

    addSchedule: (params) => http.post('/task/master/schedule/add', params),

    modifySchedule: (params) => http.post('/task/master/schedule/modify', params),

    delSchedule: (ids) => http.post('/task/master/schedule/delete', {ids}),

    getScheduleDetail: (id) => http.post('/task/master/schedule', {id}),

    // ========== 任务队列 ==========

    getTaskList: (params) => http.post('/task/master/task/data',params),

    getTaskDetail: (taskId) => http.post('/task/master/task', {taskId}),

    delTask:(ids) => http.post('/task/master/task/delete', {ids}),

    retryTask: (id) => http.post('/task/master/task/retry', {id}),
};

export default taskApi;
