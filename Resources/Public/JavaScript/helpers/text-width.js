const elementId = "yoast-measurement-element";
function createMeasurementElement() {
    const hiddenElement = document.createElement("div");
    hiddenElement.id = elementId;
    hiddenElement.style.position = "absolute";
    hiddenElement.style.left = "-9999em";
    hiddenElement.style.top = '0';
    hiddenElement.style.height = '0';
    hiddenElement.style.overflow = "hidden";
    hiddenElement.style.fontFamily = "Arial";
    hiddenElement.style.fontSize = "18px";
    hiddenElement.style.fontWeight = "400";
    document.body.appendChild(hiddenElement);
    return hiddenElement;
}
export default function measureTextWidth(text) {
    let element = document.getElementById(elementId);
    if (!element) {
        element = createMeasurementElement();
    }
    element.innerText = text;
    return element.offsetWidth;
}
