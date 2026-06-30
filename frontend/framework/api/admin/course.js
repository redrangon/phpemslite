import http from '@/framework/http'
const courseApi = {
    getCategoryList: (params) => http.post('/course/master/category/data',params),
    getCategory: (catid) => http.post('/course/master/category',{catid}),
    getCategroyTree: () => http.post('/course/master/category/tree'),
    delCategory: (ids) => http.post('/course/master/category/delete', {ids}),
    modifyCategory: (category) => http.post('/course/master/category/modify', category),
    addCategory: (category) => http.post('/course/master/category/add', category),
	getCourseList: (params) => http.post('/course/master/course/data',params),
    getAllCourse: (csid) => http.post('/course/master/course/all',{csid}),
    getCourse: (courseid) => http.post('/course/master/course',{courseid}),
    delCourse: (ids) => http.post('/course/master/course/delete', {ids}),
    modifyCourse: (course) => http.post('/course/master/course/modify', course),
    addCourse: (course) => http.post('/course/master/course/add', course),
    buildCourseTree: (data) => {
        const items = data.map(item =>{
            return {
                ...item,
                module:item.coursemodule === 'dir' ? 'dirs' : 'course',
                expanded: true,
                disabled: item.coursemodule !== 'dir',
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
	getSubjectList: (params) => http.post('/course/master/subject/data',params),
    getSubject: (csid) => http.post('/course/master/subject',{csid}),
    delSubject: (ids) => http.post('/course/master/subject/delete', {ids}),
    modifySubject: (subject) => http.post('/course/master/subject/modify', subject),
    addSubject: (subject) => http.post('/course/master/subject/add', subject),
    getSubjectPrice: (csId) => http.post('/course/master/price/data',{csId}),
    addSubjectPrice: (params) => http.post('/course/master/price/add',params),
    modifySubjectPrice: (params) => http.post('/course/master/price/modify',params),
    delSubjectPrice: (ids) => http.post('/course/master/price/delete',{ids}),
    getMemberList: (params) => http.post('/course/master/member/data',params),
    addMember: (params) => http.post('/course/master/member/add',params),
    addMemberByPassport: (params) => http.post('/course/master/member/addbypassport',params),
    modifyMember: (params) => http.post('/course/master/member/modify',params),
    deleteMember: (ids) => http.post('/course/master/member/delete',{ids}),
    refreshNumber: (csId) => http.post('/course/master/member/refresh',{csId}),
    refreshMemberStats: (csId) => http.post('/course/master/member/stats',{csId}),
};
export default courseApi;