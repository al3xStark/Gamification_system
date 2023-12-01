import axios from 'axios'
import authHeader from '../auth-header'

const API_URL = process.env.VUE_APP_API_URL

class ProjectService
{
    all(page = 0) {
        return axios.get(API_URL + '/admin/projects?page=' + page, { headers: authHeader() })
    }

    allowProjectList() {
        return axios.get(API_URL + '/admin/projects/allowList', { headers: authHeader() })
    }

    get(id) {
        return axios.get(API_URL + '/admin/projects/' + id, { headers: authHeader() })
    }

    getApiKey(login, password, projectId) {
        return axios.post(API_URL + '/admin/projects/getApiKey', {login: login, password: password, project_id: projectId}, { headers: authHeader() })
    }

    save(item) {
        return axios.post(API_URL + '/admin/projects/' + (item.id > 0 ? item.id : 'create'), item, { headers: authHeader() })
    }
}

export default new ProjectService()
