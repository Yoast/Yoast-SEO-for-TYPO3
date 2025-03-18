// Temporary fix for missing type definitions for TYPO3 modules.
declare module "@typo3/core/ajax/ajax-request.js" {
  let TYPO3: any;
  export default TYPO3;
}

declare module "@typo3/core/document-service.js" {
  let TYPO3: any;
  export default TYPO3;
}

declare module "@typo3/backend/modal.js" {
  let TYPO3: any;
  export default TYPO3;
}

declare module "jquery" {
  let $: any;
  export default $;
}
