import http from '@/framework/http'
const attachApi = {
    upload: (params,config) => http.post('/attach/app/upload', params,config),
    mobileUpload:async (file) => {
        const formData = new FormData();
        formData.append('api','upload');
        formData.append('file', file.file, file.file.name );
        return http.post('/attach/app/upload',formData,{});
    }
};
export default attachApi;