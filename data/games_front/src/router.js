import { createWebHistory, createRouter } from 'vue-router'
import Games from './components/Games.vue'
import MyGames from './components/MyGames.vue'
import Login from './components/Login.vue'
import Register from './components/Register.vue'
// lazy-loaded
const Profile = () => import('./components/Profile.vue')
const BoardUser = () => import('./components/BoardUser.vue')

const routes = [
    {
        path: '/games/my',
        name: 'page-my-games',
        // lazy-loaded
        component: MyGames,
        props: function (route) {
            return Object.assign({}, route.params, {
                propActiveView: 'game',
            })
        },
    },
    {
        path: '/games/:propGameId',
        name: 'page-games',
        // lazy-loaded
        component: Games,
        props: function (route) {
            return Object.assign({}, route.params, {
                propActiveView: 'game',
            })
        },
    },
    {
        path: '/',
        name: 'home',
        component: Games,
        props: function (route) {
            return Object.assign({}, route.params, {
                propActiveView: 'list',
            })
        },
    },
    {
        path: '/login',
        component: Login,
    },
    {
        path: '/register',
        component: Register,
    },
    {
        path: '/profile',
        name: 'profile',
        // lazy-loaded
        component: Profile,
    },

    {
        path: '/user-room/:propUserName',
        name: 'search-user-room',
        // lazy-loaded
        component: BoardUser,
        props: true
    },
    {
        path: '/user-room',
        name: 'user-room',
        // lazy-loaded
        component: BoardUser,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const publicPages = ['/login', '/register']
    const authRequired = !publicPages.includes(to.path)
    const loggedIn = localStorage.getItem('user')

    // trying to access a restricted page + not logged in
    // redirect to login page
    if (authRequired && !loggedIn) {
        next('/login')
    } else {
        next()
    }
})

export default router