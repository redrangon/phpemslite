import http from '@/framework/http'
const courseApi = {
    buildCourseTree: (data) => {
        const items = data.map(item =>{
            return {
                ...item,
                module:item.coursemodule === 'dir' ? 'dirs' : 'course',
                expanded: true,
                isCurrent: item.isCurrent,
                children: []
            };
        });

        // 创建一个以 courseid 为 key 的映射，便于快速查找
        const itemMap = {};
        items.forEach(item => {
            itemMap[item.courseid] = item;
        });

        // 根节点数组
        const roots = [];

        // 遍历所有节点，将其加入父节点的 children 中
        items.forEach(item => {
            if (item.coursedirid === 0) {
                // 根节点
                roots.push(item);
            } else {
                // 找到父节点
                const parent = itemMap[item.coursedirid];
                let thisNode = item;
                if(item.iscurrent)thisNode.expanded = true;
                while(thisNode.expanded)
                {
                    let parentNode = itemMap[thisNode.coursedirid];
                    if(parentNode?.coursedirid >= 0)
                    {
                        parentNode.expanded = true;
                        thisNode = parentNode;
                    }
                    else break;
                }
                if (parent) {
                    parent.children.push(item);
                } else {
                    // 如果父节点不存在（数据异常），也可以选择将其作为根节点或忽略
                    roots.push(item);
                }
            }
        });
        return roots;
    },
    getAllCourse: () => http.post('/course/app/course/data'),
    getCourseSubject: () => http.post('/course/app/course/subject'),
    getCourse: (courseid) => http.post('/course/app/course',{courseid}),
    recordCourseProgress: (params) => http.post('/course/app/course/progress',params),
    finishCourse: (params) => http.post('/course/app/course/finish',params),
    verifyCourseFace: (params) => http.post('/course/app/course/verify',params),
    getSubjectList: (params) => http.post('/course/app/subject/data',params),
    getSubject: (csId) => http.post('/course/app/subject',{csId}),
    getSubjectPrice: (csId) => http.post('/course/app/subject/price',{csId}),
    getSubjectCourses: (csId) => http.post('/course/app/subject/detail',{csId}),
    buySubject: (priceId) => http.post('/course/app/subject/buy',{priceId}),
    setCourseSession: (csId) => http.post('/course/app/index/session',{csId}),
    getCategoryList: (catId) => http.post('/course/app/category/data',{catId}),
    getCategory: (catId) => http.post('/course/app/category',{catId}),
};
export default courseApi;