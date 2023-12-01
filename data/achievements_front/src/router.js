import { createWebHistory, createRouter } from 'vue-router'
import Home from './components/Home.vue'
import Login from './components/Login.vue'
import ForgotPassword from './components/ForgotPassword.vue'
import Register from './components/Register.vue'
// lazy-loaded
const Profile = () => import('./components/Profile.vue')
const BoardAdmin = () => import('./components/BoardAdmin.vue')
const RoomItems = () => import('./components/admin/RoomItems.vue')
const Achievements = () => import('./components/admin/Achievements.vue')
const Events = () => import('./components/admin/Events.vue')
const Users = () => import('./components/admin/User.vue')
const RoomItemsTemplates = () => import('./components/admin/RoomItemsTemplates.vue')
const Projects = () => import('./components/admin/Projects.vue')
const BoardModerator = () => import('./components/BoardModerator.vue')
const BoardUser = () => import('./components/BoardUser.vue')
const UserRoom = () => import('./components/UserRoom.vue')

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home,
    },
    {
        path: '/home',
        component: Home,
    },
    {
        path: '/user-room/:propUserAuthKey',
        component: UserRoom,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'list'
            })
        }
    },
    {
        path: '/login',
        component: Login,
    },
    {
        path: '/forgot-password',
        component: ForgotPassword,
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
        path: '/admin/users/:propUserId',
        name: 'user-profile',
        // lazy-loaded
        component: Users,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'edit'
            })
        }
    },
    {
        path: '/admin/users',
        name: 'users',
        // lazy-loaded
        component: Users,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'list'
            })
        }
    },


    {
        path: '/admin/projects/create',
        name: 'create-project',
        // lazy-loaded
        component: Projects,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'create'
            })
        }
    },
    {
        path: '/admin/projects/view/:propProjectId',
        name: 'view-project',
        // lazy-loaded
        component: Projects,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'view'
            })
        }
    },
    {
        path: '/admin/projects/:propProjectId',
        name: 'edit-project',
        // lazy-loaded
        component: Projects,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'edit'
            })
        }
    },
    {
        path: '/admin/projects',
        name: 'projects',
        // lazy-loaded
        component: Projects,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'list'
            })
        }
    },


    {
        path: '/admin/room-items/create',
        name: 'create-room-item',
        // lazy-loaded
        component: RoomItems,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'create'
            })
        }
    },
    {
        path: '/admin/room-items/:propRoomItemId',
        name: 'edit-room-item',
        // lazy-loaded
        component: RoomItems,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'edit'
            })
        }
    },
    {
        path: '/admin/room-items',
        name: 'room-items',
        // lazy-loaded
        component: RoomItems,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'list'
            })
        }
    },


    {
        path: '/admin/achievements/create',
        name: 'create-achievement',
        // lazy-loaded
        component: Achievements,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'create'
            })
        }
    },
    {
        path: '/admin/achievements/:propAchievementId',
        name: 'edit-achievement',
        // lazy-loaded
        component: Achievements,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'edit'
            })
        }
    },
    {
        path: '/admin/achievements',
        name: 'achievements',
        // lazy-loaded
        component: Achievements,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'list'
            })
        }
    },


    {
        path: '/admin/events/create',
        name: 'create-event',
        // lazy-loaded
        component: Events,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'create'
            })
        }
    },
    {
        path: '/admin/events/:propEventId',
        name: 'edit-event',
        // lazy-loaded
        component: Events,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'edit'
            })
        }
    },
    {
        path: '/admin/events',
        name: 'events',
        // lazy-loaded
        component: Events,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'list'
            })
        }
    },


    {
        path: '/admin/room-item-templates/template/:propTemplateId/create',
        name: 'create templates for room item',
        // lazy-loaded
        component: RoomItemsTemplates,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'create'
            })
        }
    },
    {
        path: '/admin/room-item-templates/template/:propTemplateId',
        name: 'get templates for room item',
        // lazy-loaded
        component: RoomItemsTemplates,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'listForTemplate'
            })
        }
    },
    {
        path: '/admin/room-item-templates/item/:propItemId',
        name: 'list items for template',
        // lazy-loaded
        component: RoomItemsTemplates,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'listForItem'
            })
        }
    },
    {
        path: '/admin/room-item-templates/create',
        name: 'create template',
        // lazy-loaded
        component: RoomItemsTemplates,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'create'
            })
        }
    },
    {
        path: '/admin/room-item-templates/:propTemplateId',
        name: 'update template',
        // lazy-loaded
        component: RoomItemsTemplates,
        props: function(route) {
            return Object.assign({}, route.params, {
                propActiveView: 'update'
            })
        }
    },
    {
        path: '/admin/room-item-templates',
        name: 'room-item-templates',
        // lazy-loaded
        component: RoomItemsTemplates,
        props: true,
    },


    {
        path: '/admin',
        name: 'admin',
        // lazy-loaded
        component: BoardAdmin,
        // beforeEnter(to, from, next) {
        //     if (!isAuthenticated()) {
        //         next({
        //             name: "home"
        //         });
        //     } else {
        //         next()
        //     }
        // }
    },
    {
        path: '/mod',
        name: 'moderator',
        // lazy-loaded
        component: BoardModerator,
    },
    {
        path: '/user',
        name: 'user',
        // lazy-loaded
        component: BoardUser,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/register', '/home', '/', '/forgot-password'];
  const authRequired = !publicPages.includes(to.path) && !to.path.includes('/user-room/');
  const loggedIn = localStorage.getItem('user');

  // trying to access a restricted page + not logged in
  // redirect to login page
  if (authRequired && !loggedIn) {
    next('/login');
  } else {
    next();
  }
});

export default router