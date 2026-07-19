// utils/device.js
export const getDeviceType = () => {
    const width = window.innerWidth
    const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0

    if (width <= 720 && isTouch) {
        return 'mobile'
    }
    return 'desktop'
}

export const isMobile = () => {
    return getDeviceType() === 'mobile'
}