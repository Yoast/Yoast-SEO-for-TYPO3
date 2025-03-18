export function setAttributes(el, attrs) {
    Object.entries(attrs).forEach(([name, value]) => {
        if (value != null) {
            el.setAttribute(name, value);
        }
    });
}
