import http from '@/framework/http'

const certApi = {
    /**
     * 获取证书列表
     * @param {Object} search - 搜索参数
     * @param {number} search.page - 页码
     * @param {number} search.limit - 每页数量
     * @param {number} search.pcceid - 证书教育ID
     * @param {number} search.pcplanid - 计划ID
     * @param {number} search.pcpassport - 通行证ID
     * @param {number} search.pcstatus - 状态
     * @param {string} search.pcsn - 序列号（模糊查询）
     */
    getCertList: (search) => http.post('/cert/master/cert/data', search),

    /**
     * 获取单个证书详情
     * @param {number} certid - 证书ID
     */
    getCert: (certid) => http.post('/cert/master/cert/index', { certid }),

    /**
     * 添加证书
     * @param {Object} cert - 证书数据
     * @param {number} cert.pcceid - 证书教育ID
     * @param {number} cert.pcpassport - 通行证ID
     * @param {number} cert.pcplanid - 计划ID
     * @param {number} cert.pctime - 时间（时间戳）
     * @param {number} cert.pcstatus - 状态
     * @param {string} cert.pcsn - 序列号
     * @param {number} cert.pcexpirytime - 过期时间（时间戳）
     */
    addCert: (cert) => http.post('/cert/master/cert/add', cert),

    /**
     * 修改证书
     * @param {Object} cert - 证书数据
     * @param {number} cert.pcid - 证书ID（必填）
     * @param {number} cert.pcceid - 证书教育ID
     * @param {number} cert.pcpassport - 通行证ID
     * @param {number} cert.pcplanid - 计划ID
     * @param {number} cert.pctime - 时间（时间戳）
     * @param {number} cert.pcstatus - 状态
     * @param {string} cert.pcsn - 序列号
     * @param {number} cert.pcexpirytime - 过期时间（时间戳）
     */
    modifyCert: (cert) => http.post('/cert/master/cert/modify', cert),

    /**
     * 删除证书
     * @param {Array<number>} ids - 证书ID数组
     */
    delCert: (ids) => http.post('/cert/master/cert/delete', { ids }),
    getCertMemberList: (search) => http.post('/cert/master/cert/member', search),
    getMemberList: (params) => http.post('/cert/master/member/data',params),
    addMember: (params) => http.post('/cert/master/member/add',params),
    addMemberByPassport: (params) => http.post('/cert/master/member/addbypassport',params),
    modifyMember: (params) => http.post('/cert/master/member/modify',params),
    deleteMember: (ids) => http.post('/cert/master/member/delete',{ids}),
    refreshNumber: (ceId) => http.post('/cert/master/member/refresh',{ceId}),
    verifyMember: (ids) => http.post('/cert/master/member/verify',{ids}),
};

export default certApi;