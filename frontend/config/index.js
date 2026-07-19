const Config = {
    hashData:false,
    hashKey:'1234567812345678',
    hashIv:'1234567812345678',
    devUrl:"/api/phpemsvue/index.php/",
    buildUrl:"/phpemsvue/index.php/",
    loginPage:'/login',
    wechatAppid:''
};
Config.url = import.meta.env.DEV ? Config.devUrl : Config.buildUrl;
export default Config;