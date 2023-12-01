import axios from 'axios';
import authHeader from './auth-header';

const API_URL = process.env.VUE_APP_API_URL;

class UserService {
  getPublicContent() {
    return axios.get(API_URL + '/all', { headers: authHeader() });
  }

  getUserBoard(user) {
    return axios.get(API_URL + '/user-page/items/' + user, { headers: authHeader() });
  }

  getStatsProgress(user) {
    return axios.get(API_URL + '/user-page/stats/' + user, { headers: authHeader() });
  }

  getAchievements(user) {
    return axios.get(API_URL + '/user-page/achievements/' + user, { headers: authHeader() });
  }

  setAchievementTemplate(user, achievementId) {
    return axios.post(API_URL + '/user-page/achievements/' + user, {'achievementId': achievementId},{ headers: authHeader() });
  }

  getModeratorBoard() {
    return axios.get(API_URL + '/mod', { headers: authHeader() });
  }

  getAdminBoard() {
    return axios.get(API_URL + '/admin', { headers: authHeader() });
  }
}

export default new UserService();
