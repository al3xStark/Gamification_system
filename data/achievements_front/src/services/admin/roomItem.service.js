import axios from 'axios'
import authHeader from '../auth-header'

const API_URL = process.env.VUE_APP_API_URL

class RoomItemService
{
    all(page = 0) {
        return axios.get(API_URL + '/admin/room-items?page=' + page, { headers: authHeader() })
    }

    get(id) {
        return axios.get(API_URL + '/admin/room-items/' + id, { headers: authHeader() })
    }

    getDefault() {
        return axios.get(API_URL + '/admin/room-items/allowList', { headers: authHeader() })
    }


    getAllow(item) {
        return axios.get(API_URL + '/admin/room-items/allowListForTemplate/' + item, { headers: authHeader() })
    }

    save(item) {
        let formData = new FormData()
        for (let key in item) {
            if (typeof item[key] !== 'undefined' && item[key] !== null) {
                if (key === 'active') {
                    item[key] = +item[key]
                }
                formData.append(key, item[key])
            }
        }
        return axios.post(API_URL + '/admin/room-items/' + (item.id > 0 ? item.id : 'create'), formData, { headers: authHeader() })
    }
}

export default new RoomItemService()
