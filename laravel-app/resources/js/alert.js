import { dismissElement } from './shared/flashDismiss';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.flash-message').forEach(dismissElement);
});
