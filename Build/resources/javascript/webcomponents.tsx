import React from "react";
import {createRoot, Root} from 'react-dom/client';
import DescriptionProgressBarTest from "./Components/DescriptionProgressBarTest";

function registerReactComponentAsWebComponent(
    tagName: string,
    Component: React.FC<any>,
    observedAttributes: string[] = []
) {
    class ReactWebComponent extends HTMLElement {
        root: Root | null = null;

        static get observedAttributes(): string[] {
            return observedAttributes;
        }

        connectedCallback() {
            if (!this.root) {
                this.root = createRoot(this);
            }
            this.renderComponent();
        }

        disconnectedCallback() {
            this.root?.unmount();
            this.root = null;
        }

        attributeChangedCallback() {
            this.renderComponent();
        }

        renderComponent() {
            const attributes = (this.constructor as typeof ReactWebComponent).observedAttributes;

            if (!Array.isArray(attributes)) {
                console.error("Error: observedAttributes is not an array", attributes);
                return;
            }

            const props = attributes.reduce<Record<string, string>>((acc, attr) => {
                acc[attr] = this.getAttribute(attr) || "";
                return acc;
            }, {});

            this.root?.render(<Component {...props} />);
        }
    }

    customElements.define(tagName, ReactWebComponent);
}

registerReactComponentAsWebComponent('yoast-description-progress-bar', DescriptionProgressBarTest, ['description', 'date']);
