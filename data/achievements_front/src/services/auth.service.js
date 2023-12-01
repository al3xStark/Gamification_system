import axios from 'axios'

const API_URL = process.env.VUE_APP_API_URL

class AuthService
{
    login(user) {
        return axios.post(API_URL + '/signin', {
            name: user.name,
            password: user.password,
        }).then(response => {
            if (response.data.access_token) {
                localStorage.setItem('user', JSON.stringify(response.data))
            }

            return response.data
        })
    }
    forgot(user) {
        return axios.post(API_URL + '/forgot', {
            email: user.email,
        }).then(response => {
            return response.data
        })
    }
    reset(user) {
        return axios.post(API_URL + '/reset?token=' + user.code, {
            email: user.email,
            password: user.password,
        }).then(response => {
            return response.data
        })
    }

    logout() {
        localStorage.removeItem('user')
    }

    register(user) {
        return axios.post(API_URL + '/signup', {
            name: user.name,
            email: user.email,
            password: user.password,
        }).then(response => {
            if (response.data.access_token) {
                localStorage.setItem('user', JSON.stringify(response.data))
            }

            return response.data
        })
    }
}

export default new AuthService()
