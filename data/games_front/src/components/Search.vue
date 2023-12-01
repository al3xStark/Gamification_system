<template>
    <div class="search">
        <input class="search-line" type="text" v-model="name" @input="getUserList()" @blur="hideResults()">
        <div class="find-list" v-if="users.length">
            <template v-for="(user) in users" :key="user">
                <router-link :to="{name: 'search-user-room', params: { propUserName: user.name }}" class="nav-link" @click="hideResults()">
                    {{ user.name }}
                </router-link>
            </template>
        </div>
    </div>
</template>

<style>
.search-line{
    width: 250px;
    border: 1px solid #606060;
    padding: 5px 10px;
    background: #000000eb;
    color: white;
}

.find-list {
    position: absolute;
    background: black;
    padding: 5px 10px;
    border: 1px solid white;
    border-top: none;
    width: 250px;
}
</style>

<script>

import UserService from '@/services/user.service'

export default {
    name: 'user-search',
    components: {
    },
    data() {
        return {
            loading: false,
            name: '',
            users: [],
        }
    },
    methods: {
        getUserList() {
            let self = this
            if (this.name.length > 2) {
                UserService.getAutocompleteList(this.name).then(
                    (response) => {
                        self.users = response.data
                    },
                    (error) => {
                        console.log(error)
                    },
                )
            } else {
                self.users = []
            }
        },
        hideResults() {
            let self = this
            setTimeout(function () {
                self.name = ''
                self.users = []
            },300)
        }
    }
}
</script>
