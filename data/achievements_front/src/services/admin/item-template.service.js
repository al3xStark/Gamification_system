import axios from 'axios'
import authHeader from '../auth-header'

const API_URL = process.env.VUE_APP_API_URL

class ItemTemplateService
{
    all() {
        return axios.get(API_URL + '/admin/room-item-templates', { headers: authHeader() })
    }

    allowListForProject(id) {
        return axios.get(API_URL + '/admin/room-item-templates/allowList/' + id, { headers: authHeader() })
    }

    getAllForTemplate(templateId) {
        return axios.get(API_URL + '/admin/room-item-templates/template/' + templateId, { headers: authHeader() })
    }

    get(id) {
        return axios.get(API_URL + '/admin/room-item-templates/' + id, { headers: authHeader() })
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

        return axios.post(API_URL + '/admin/room-item-templates/' + (item.id > 0 ? item.id : 'create'), formData, { headers: authHeader() })
    }
}

export default new ItemTemplateService()
