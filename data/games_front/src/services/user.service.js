import axios from 'axios'
import authHeader from './auth-header'

const API_URL = process.env.VUE_APP_API_URL

class UserService
{
    getPublicContent() {
        return axios.get(API_URL + '/all', { headers: authHeader() })
    }

    getUserBoard(userName) {
        return axios.get(API_URL + '/user-board' + (userName !== '' ? '/' + userName : ''), { headers: authHeader() })
    }

    getModeratorBoard() {
        return axios.get(API_URL + '/mod', { headers: authHeader() })
    }

    getAdminBoard() {
        return axios.get(API_URL + '/admin', { headers: authHeader() })
    }

    getAutocompleteList(mask = '') {
        return axios.get(API_URL + '/users/autocomplete-list/' + mask, { headers: authHeader() })
    }
}

export default new UserService()
