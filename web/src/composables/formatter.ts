import { ref, onMounted } from 'vue'
import moment from 'moment'

export function useFormatter() {
    

    onMounted(() => {
        
    })

    const formatDate = (value: string) => {
        return moment(value).format(`MMMM Do YYYY, h:mm a`)
    }

    return {
        formatDate,
    }
}