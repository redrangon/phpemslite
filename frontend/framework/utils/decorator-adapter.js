import { isMobile } from '@/framework/utils/device.js';

// 直接判断并导出对应的模块内容
const decorators = isMobile()?await import("@/framework/utils/mobile/decorators.js"):await import("@/framework/utils/decorators.js");
export const { withLayer, withConfirm } = decorators;