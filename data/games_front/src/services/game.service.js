import axios from 'axios'
import authHeader from './auth-header'

const API_URL = process.env.VUE_APP_API_URL

class GamesService
{
    getAutocompleteList(mask = '') {
        return axios.get(API_URL + '/games/autocomplete-list/' + mask, { headers: authHeader() })
    }

    get(id = 0) {
        return axios.get(API_URL + '/games/' + id, { headers: authHeader() })
    }

    setRate(id = 0, rate = 5) {
        return axios.post(API_URL + '/games/rating/' + id, { rate: +rate }, { headers: authHeader() })
    }

    comments(id = 0) {
        return axios.get(API_URL + '/games/comments/' + id, { headers: authHeader() })
    }

    setComment(id, comment) {
        return axios.post(API_URL + '/games/setComment/' + id, { comment: comment }, { headers: authHeader() })
    }

    myGames() {
        return axios.get(API_URL + '/games/my', { headers: authHeader() })
    }

    getAchievements() {
        return axios.get(API_URL + '/games/achievements', { headers: authHeader() })
    }
}

export default new GamesService()
