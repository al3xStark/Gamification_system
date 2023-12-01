<template>
    <div class="container-fluid">
        <h3 class="mt-3">
            <template v-if="activeView === 'list'">
                <h3 class="mt-3">Users</h3>
            </template>
        </h3>
        <template v-if="activeView === 'list'">
            <div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Active</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Groups</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(user,index) in users" :key="user">
                        <tr>
                            <th scope="row">{{ index + 1 }}</th>
                            <td>
                                <template v-if="!!user.active">
                                    <i class="fa-xl bi bi-check-circle-fill text-success"></i>
                                </template>
                                <template v-else>
                                    <i class="fa-xl bi bi-x-circle-fill text-danger"></i>
                                </template>
                            </td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>
                                <div v-for="group in user.groups" :key="group">
                                    {{ group }}
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary mr-3" v-on:click="goToProfile(user.id)">Profile</button>
                            </td>
                        </tr>
                    </template>
                    <pagination
                            v-if="pagination.total"
                            :total-items="pagination.total"
                            :items-per-page="pagination.perPage"
                            :current-page="pagination.currentPage"
                            @page-changed="changePage"
                    />
                    </tbody>
                </table>
            </div>
        </template>
        <template v-if="activeView === 'edit'">
            <div>
                <div class="row">
                    <div class="form-group col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="currentUser.active">
                            <label class="form-check-label" for="activeCheckBox">
                                Active
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-12">
                        <label>Name</label>
                        <input type="text" class="form-control" placeholder="User name" v-model="currentUser.name">
                    </div>
                    <div class="form-group col-12">
                        <label>Email</label>
                        <input type="email" class="form-control" placeholder="User email" v-model="currentUser.email">
                    </div>
                </div>
                <div>
                    <div class="btn btn-success mr-3" v-on:click="saveUser()">
                        Update
                    </div>
                    <div class="btn btn-primary mr-3" v-on:click="goToList()">Back</div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import UserService from '@/services/admin/user.service'
import Pagination from '@/components/Pagination.vue'

export default {
    components: {
        Pagination,
    },
    name: 'component-user',
    data() {
        return {
            users: Array,
            pagination: Array,
            activeView: this.propActiveView,
            userId: this.propUserId,
            currentUser: {},
        }
    },
    props: {
        propActiveView: {
            type: String,
            default: 'list',
            required: false,
        },
        propUserId: {
            type: String,
            default: '',
            required: false,
        },
    },
    methods: {
        changePage(page) {
            if (this.pagination.currentPage === page) {
                return
            }
            this.$router.push({ name: 'users', query: { page: page } })
        },
        goToList() {
            this.$router.push({ path: this.$router.options.history.state.back })
        },
        goToProfile(id) {
            this.$router.push({ name: 'user-profile', params: { propUserId: id } })
        },
        getUser(id) {
            let self = this

            UserService.get(id).then(
                (response) => {
                    self.currentUser = response.data
                    self.activeView = 'edit'
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        getList() {
            let self = this

            UserService.all(this.$route.query?.page).then(
                (response) => {
                    self.users = response.data.items
                    self.pagination = response.data.pagination
                    self.activeView = 'list'
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        saveUser() {
            let self = this
            UserService.save(this.currentUser).then(
                () => {
                    self.goToList()
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        updatePage() {
            if (this.activeView === 'list') {
                this.getList()
            }
            if (this.activeView === 'edit') {
                this.getUser(this.userId)
            }
        },
    },
    mounted() {
        this.updatePage()
    },
}
</script>
