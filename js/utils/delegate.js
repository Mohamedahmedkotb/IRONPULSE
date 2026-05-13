/**
 * @param {Element} root
 * @param {string} selector
 * @param {string} type
 * @param {(ev: Event, target: Element) => void} handler
 */
export function delegate(root, selector, type, handler) {
  root.addEventListener(type, (ev) => {
    const t = ev.target;
    if (!(t instanceof Element)) return;
    const match = t.closest(selector);
    if (match && root.contains(match)) handler(ev, match);
  });
}
