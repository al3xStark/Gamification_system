import axios from 'axios'
import authHeader from '../auth-header'

const API_URL = process.env.VUE_APP_API_URL

class EventService
{
    all(page = 0) {
        return axios.get(API_URL + '/admin/events?page=' + page, { headers: authHeader() })
    }

    get(id) {
        return axios.get(API_URL + '/admin/events/' + id, { headers: authHeader() })
    }

    save(item) {
        return axios.post(API_URL + '/admin/events/' + (item.id > 0 ? item.id : 'create'), item, { headers: authHeader() })
    }

    allowListForProject(id) {
        return axios.get(API_URL + '/admin/events/allowList/' + id, { headers: authHeader() })
    }
}

export default new EventService()
