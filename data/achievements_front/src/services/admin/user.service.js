import axios from 'axios'
import authHeader from '../auth-header'

const API_URL = process.env.VUE_APP_API_URL

class UserService
{
    all(page = 0) {
        return axios.get(API_URL + '/admin/user?page=' + page, { headers: authHeader() })
    }

    get(id) {
        return axios.get(API_URL + '/admin/user/' + id, { headers: authHeader() })
    }

    save(user) {
        return axios.post(API_URL + '/admin/user/' + user.id, user, { headers: authHeader() })
    }
}

export default new UserService()
