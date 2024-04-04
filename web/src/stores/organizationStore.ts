import { defineStore } from 'pinia';
import { router } from '@/router';
import { fetchWrapper } from '@/utils/helpers/fetch-wrapper';
import { toastWrapper } from '@/utils/helpers/toast-wrapper';

const baseUrl = `${import.meta.env.VITE_API_URL}/users`;

type registerType = {
    firstName: string,
    lastName: string,
    phoneNumber: string,
    emailAddress: string,
    accountType: string,
    password: string,
    password_confirmation: string,
    orgName?: string,
    industry?: string,
    country?: string,
    orgBio?: string,
    orgAddress?: string,
};

type resetPasswordInitiateType = {
    email: string,
};
type resetPasswordOtpType = {
    email: string,
    signature: string,
    type: string,
    otp: string,
};
type resetPasswordSetType = {
    email: string,
    password: string
};

export const useOrganizationStore = defineStore({
    id: 'organization',
    state: () => ({
        // initialize state from local storage to enable user to stay logged in
        // @ts-ignore
        list: localStorage.getItem('logger'),
        hse_tok_passer: localStorage.getItem('hse_tok_passer'),
        returnUrl: null
    }),
    getters: {
        loggedUser: (state) => {
            return JSON.parse(atob(state.user))
        },
        accessToken: (state) => {
            return state.hse_tok_passer
        },
    },
    actions: {
        async login(email: string, password: string) {
            try {
                const data = await fetchWrapper.post(`auth/login`, { email, password })
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })
                

                if (data) {

                    localStorage.setItem("hse_tok_passer", data.access_token);
                    localStorage.setItem("logger", btoa(JSON.stringify(data.user)));
                    // localStorage.setItem("role", data.data.user?.role?.name?.split(' ').join(''));
                    localStorage.setItem("user_mail", data.user.email);
                    localStorage.setItem("firstName", data.user.firstName);
                    localStorage.setItem("id", data.user.id);
                    localStorage.removeItem('hse_reset_tok_passer')
                    localStorage.removeItem('hse_reset_email')
                    router.push(this.returnUrl || '/dashboard');
                }

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            } 
        },
        async register(values: registerType) {
            try {
                const data = await fetchWrapper.post(`auth/register`, values)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },
        async resetPasswordInitiate(values: resetPasswordInitiateType) {
            try {
                const data = await fetchWrapper.post(`auth/reset/initiate`, values)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })

                return toastWrapper.success(data?.message, data)


            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },
        async resetPasswordOtp(values: resetPasswordOtpType) {
            try {
                const data = await fetchWrapper.post(`auth/reset/otp`, values)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })
                if (data) {
                            
                    localStorage.setItem("hse_reset_tok_passer", data.data.reset.reset_token);
                    localStorage.setItem("hse_reset_email", data.data.reset.email);
                }
                return toastWrapper.success(data?.message, data.data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },
        async resetPasswordSet(values: resetPasswordSetType) {
            try {
                const data = await fetchWrapper.post(`auth/reset/complete`, values)
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })
                    localStorage.removeItem('hse_reset_email')
                    localStorage.removeItem('hse_reset_tok_passer')

                return toastWrapper.success(data?.message, data)
            } catch (error: any) {
                return toastWrapper.error(error, error)
            }

        },

        async old_login(username: string, password: string) {
            const user = await fetchWrapper.post(`${baseUrl}/authenticate`, { username, password });

            // update pinia state
            this.user = user;
            // store user details and jwt in local storage to keep user logged in between page refreshes
            localStorage.setItem('user', JSON.stringify(user));
            // redirect to previous url or default to home page
            router.push(this.returnUrl || '/dashboard');
        },
        async logout() {

            try {
                const data = await fetchWrapper.post(`auth/logout`, {})
                    .catch((error: any) => {
                        throw error;
                    }).then((response: any) => {
                        return response
                    })
                    this.clearSession()

            } catch (error: any) {

                    this.clearSession()

                return toastWrapper.error(error, error)
            }
        },
        clearSession() {

                this.user = null;
                this.hse_tok_passer = null;
                localStorage.removeItem("hse_tok_passer");
                localStorage.removeItem("logger");
                localStorage.removeItem("user_mail");
                localStorage.removeItem("firstName");
                localStorage.removeItem('hse_reset_email')
                localStorage.removeItem('hse_reset_tok_passer')
                localStorage.removeItem("id");
                router.push('/auth/login');
                
        }
    }
});
