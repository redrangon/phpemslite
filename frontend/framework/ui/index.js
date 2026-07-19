import { isMobile } from '@/framework/utils/device.js'
const loadUI = async (app) => {
    if (isMobile()) {
        // 加载移动端 UI（如 Vant）
        const vant = await import('vant')
        app.use(vant)
        // 可选：加载 Vant 样式（Vite 支持按需）
        await import('vant/lib/index.css')
        await import('@/assets/css/mobile/style.css')
    } else {
        const layui = await import('@layui/layui-vue')
        app.use(layui)
        await import('@layui/layui-vue/lib/index.css')
        await import('@/assets/css/desktop/style.css')
    }
};
// 动态加载对应 UI
export default loadUI;