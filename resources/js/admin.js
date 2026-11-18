import axios from 'axios';
import $ from 'jquery';
import { hideLoader, showLoader } from './components/loader';

window.$ = window.jQuery = $;

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

export async function get(
    url,
    loaderText = 'Cargando...',
    params = {},
    headers = {},
    loader = true,
    timeout = 15000,
) {
    if (loader) {
        showLoader(loaderText);
    }

    try {
        const response = await axios.get(url, {
            params,
            headers,
            timeout,
        });

        return response.data;
    } finally {
        if (loader) {
            hideLoader();
        }
    }
}

export async function post(
    url,
    data = {},
    loaderText = 'Guardando...',
    { params = {}, headers = {}, loader = true, timeout = 15000 } = {},
) {
    if (loader) {
        showLoader(loaderText);
    }

    try {
        const response = await axios.post(url, data, {
            params,
            headers,
            timeout,
        });

        return response.data;
    } finally {
        if (loader) {
            hideLoader();
        }
    }
}

export async function put(
    url,
    data = {},
    loaderText = 'Guardando...',
    { params = {}, headers = {}, loader = true, timeout = 15000 } = {},
) {
    if (loader) {
        showLoader(loaderText);
    }

    try {
        const response = await axios.put(url, data, {
            params,
            headers,
            timeout,
        });

        return response.data;
    } finally {
        if (loader) {
            hideLoader();
        }
    }
}

window.BanHttp = { get, post, put };

export function showToast(type, text) {
    const safeType = type === 'danger' ? 'danger' : 'success';
    const title = safeType === 'success' ? 'Éxito' : 'Error';
    const toast = document.createElement('div');
    toast.className = `toast show bg-${safeType} text-white`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `<div class="toast-body"><strong>${title}.</strong> ${text}</div>`;
    document.querySelector('.toast-container')?.append(toast);
    window.setTimeout(() => toast.remove(), 5000);
}

window.showToast = showToast;
