/**
 * Axios is used for every call to the public market API. The portal is
 * read-only, so there is no authentication handling here - only the CSRF
 * header Laravel expects for same-origin requests.
 */

import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

const csrfToken = document.head.querySelector('meta[name="csrf-token"]');

if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
}
