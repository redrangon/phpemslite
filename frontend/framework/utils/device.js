// utils/device.js
export const getDeviceType = () => {
    const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0

    if (isTouch) {
        return 'mobile'
    }
    return 'desktop'
}

export const isMobile = () => {
    return getDeviceType() === 'mobile'
}