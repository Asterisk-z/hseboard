import { toast, type ToastOptions } from 'vue3-toastify';



export const toastWrapper = {
    error: action('error'),
    success: action('success')
};


function action(status: string ) {
    return (message: string, option?: ToastOptions) => {
        
        const toastComponent = toast 
        

        
        return toastComponent.success(message);
    };
}




