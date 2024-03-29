import { useAuthStore } from '@/stores/auth';
import { router } from '@/router';

export const fetchWrapper = {
    get: request('GET'),
    post: request('POST'),
    put: request('PUT'),
    delete: request('DELETE')
};

const baseUrl = `${import.meta.env.VITE_API_LINK}`;

function request(method: string) {
    return (url: any, body?: any) => {
        const apiUrl = `${baseUrl}${url}`
        const requestOptions: any = {
            method,
            headers: authHeader(url)
        };
        if (body) {
            requestOptions.headers['Content-Type'] = 'application/json';
            requestOptions.body = JSON.stringify(body);
        }
        return fetch(apiUrl, requestOptions).then(handleResponse);
    };
}

// helper functions

function authHeader(url: any) {
    // return auth header with jwt if user is logged in and request is to the api url
    const user = useAuthStore();
    const isLoggedIn = !!user?.accessToken;
    let token = user?.accessToken;
    // const isApiUrl = url.startsWith(import.meta.env.VITE_API_URL);
    console.log(url, 'from wraper');
    console.log(isLoggedIn, 'from wraper');
    // const isApiUrl = url.startsWith(baseUrl);
    if (url == 'auth/reset/complete') {
        token = localStorage.getItem('hse_reset_tok_passer')
    }
    if (isLoggedIn) {
        return {
            "Authorization": `Bearer ${user.accessToken}`,
            "Accept": "Application/json",
            "Content-Type": "Application/json"
        };
    } else {
        return {
            "Authorization": `Bearer ${token}`,
            "Accept": "Application/json",
            "Content-Type": "Application/json"
        };
    }
}

function handleResponse(response: any) {
    return response.text().then((text: any) => {
        const data = text && JSON.parse(text);

        if (!response.ok) {
            const { user, logout, clearSession } = useAuthStore();
            if ([401, 403].includes(response.status) && user) {
                // auto logout if 401 Unauthorized or 403 Forbidden response returned from api
                // logout();
                clearSession()
            }

            const error = (data && data.message) || response.statusText;
            return Promise.reject(error);
        }

        return data;
    });
}
