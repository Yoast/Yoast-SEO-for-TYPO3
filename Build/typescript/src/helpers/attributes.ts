export function setAttributes<K extends keyof HTMLElementTagNameMap>(
  el: HTMLElementTagNameMap[K],
  attrs: Partial<Record<string, string>>
): void {
  Object.entries(attrs).forEach(([name, value]) => {
    if (value != null) {
      el.setAttribute(name, value)
    }
  })
}
