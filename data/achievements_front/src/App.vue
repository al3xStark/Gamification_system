<template>
    <div class="body">
        <nav class="navbar navbar-expand navbar-dark navbar" v-if="!isUserRoom">
            <a href="/" class="navbar-brand">Stark</a>
            <div class="navbar-nav mr-auto">
                <li v-if="showAdminBoard" class="nav-item">
                    <router-link to="/admin" class="nav-link">Admin Board</router-link>
                </li>
                <li v-if="showModeratorBoard" class="nav-item">
                    <router-link to="/mod" class="nav-link">Moderator Board</router-link>
                </li>
                <li class="nav-item">
                    <router-link v-if="currentUser" to="/user" class="nav-link">User</router-link>
                </li>
            </div>

            <div v-if="!currentUser" class="navbar-nav ml-auto">
                <li class="nav-item">
                    <router-link to="/register" class="nav-link">
                        <font-awesome-icon icon="user-plus"/>
                        Sign Up
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/login" class="nav-link">
                        <font-awesome-icon icon="sign-in-alt"/>
                        Login
                    </router-link>
                </li>
            </div>

            <div v-if="currentUser" class="navbar-nav ml-auto">
                <li class="nav-item">
                    <router-link to="/profile" class="nav-link">
                        <font-awesome-icon icon="user"/>
                        {{ currentUser.name }}
                    </router-link>
                </li>
                <li class="nav-item">
                    <a class="nav-link" @click.prevent="logOut">
                        <font-awesome-icon icon="sign-out-alt"/>
                        LogOut
                    </a>
                </li>
            </div>
        </nav>

        <div class="container-fluid" id="content">
            <div class="row">
                <div class="col-2 admin-menu" v-if="isAdminDirectory">
                    <nav id="sidebar">
                        <ul class="nav flex-column">
                            <li class="nav-item" v-if="isSuperUser">
                                <router-link :to="{ name: 'users' }" class="nav-link">Users</router-link>
                            </li>
                            <li class="nav-item">
                                <router-link :to="{ name: 'projects' }" class="nav-link">Projects</router-link>
                            </li>
                            <li class="nav-item">
                                <router-link :to="{ name: 'room-items' }" class="nav-link">Room items</router-link>
                            </li>
                            <li class="nav-item">
                                <router-link :to="{ name: 'room-item-templates' }" class="nav-link">Room items templates</router-link>
                            </li>
                            <li class="nav-item">
                                <router-link :to="{ name: 'events' }" class="nav-link">Events</router-link>
                            </li>
                            <li class="nav-item">
                                <router-link :to="{ name: 'achievements' }" class="nav-link">Achievements</router-link>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div :class="[isAdminDirectory ? 'col-9' : 'col-12']">
                    <div id="route-content" class="row">
                        <router-view :key="$route.fullPath"/>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style lang="scss">
@import "@/assets/styles/global.scss";
@import "@/assets/styles/app.scss";
</style>

<script>

export default {
    computed: {
        isSuperUser() {
            return !!(
                this.currentUser
                && this.currentUser['groups']
                && this.currentUser['groups'].includes('SuperUser')
            )
        },
        isProjectAdmin() {
            return !!(
                this.currentUser
                && this.currentUser['projects']
                && this.currentUser['projects'].filter(project => project['group'].includes('ProjectAdmin'))
            )
        },
        isProjectManager() {
            return !!(
                this.currentUser
                && this.currentUser['projects']
                && this.currentUser['projects'].filter(project => project['group'].includes('ProjectManager'))
            )
        },
        isAdminDirectory() {
            return this.$route.path.startsWith('/admin')
                && this.showAdminBoard
        },
        isUserRoom() {
            return this.$route.path.startsWith('/user-room')
        },
        currentUser() {
            return this.$store.state.auth.user
        },
        showAdminBoard() {
            return this.isSuperUser || this.isProjectAdmin || this.isProjectManager;
        },
        showModeratorBoard() {
            if (this.currentUser && this.currentUser['groups']) {
                return this.currentUser['groups'].includes('ROLE_MODERATOR')
            }

            return false
        },
    },
    methods: {
        logOut() {
            this.$store.dispatch('auth/logout')
            this.$router.push('/login')
        },
    },
}
</script>