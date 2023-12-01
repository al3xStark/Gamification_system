import axios from 'axios'
import authHeader from '../auth-header'

const API_URL = process.env.VUE_APP_API_URL

class AchievementService
{
    all(page = 0) {
        return axios.get(API_URL + '/admin/achievements?page=' + page, { headers: authHeader() })
    }

    get(id) {
        return axios.get(API_URL + '/admin/achievements/' + id, { headers: authHeader() })
    }

    save(item) {
        return axios.post(API_URL + '/admin/achievements/' + (item.id > 0 ? item.id : 'create'), item, { headers: authHeader() })
    }
}

export default new AchievementService()
