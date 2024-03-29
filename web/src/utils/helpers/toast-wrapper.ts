import { toast, type ToastOptions } from 'vue3-toastify';



export const toastWrapper = {
    error: action('error'),
    success: action('success')
};


function action(status: string ) {
    return (message: string, data?: any, option?: ToastOptions) => {
        
        const toastComponent = toast 

        if (status == 'error') {

            toastComponent.error(message);
            return {
                message: "error",
                error: data,
            };
        }

        if (status == 'success') {
            toastComponent.success(message);
            return {
                message: "success",
                data,
            };
        }

        
    };
}




