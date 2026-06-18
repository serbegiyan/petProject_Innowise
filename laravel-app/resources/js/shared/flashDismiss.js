export const FLASH_DISMISS_MS = 2000;

export function dismissElement(el) {
    setTimeout(() => {
        el.style.transition = 'opacity 0.5s ease';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    }, FLASH_DISMISS_MS);
}
