<template>
    <div class="container-fluid">
        <h3 class="mt-3">
            <template v-if="activeView === 'list'">
                <h3 class="mt-3">Projects</h3>
            </template>
            <template v-else-if="activeView === 'view'">
                <h3 class="mt-3">View project</h3>
            </template>
            <template v-else-if="activeView === 'getApiKey'">
                Get api key for project
            </template>
            <template v-else>
                <h3 class="mt-3">
                    <template v-if="issetProject">
                        Edit project
                    </template>
                    <template v-else>
                        Create project
                    </template>
                </h3>
            </template>
        </h3>
        <template v-if="activeView === 'list'">
            <div class="btn btn-primary mb-3 mr-3" v-on:click="create()">Create new</div>
        </template>
        <template v-if="activeView === 'list'">
            <div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Users count</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(project,index) in projects" :key="project">
                        <tr>
                            <th scope="row">{{ index + 1 }}</th>
                            <td>{{ project.name }}</td>
                            <td>{{ project.users_count }}</td>
                            <td>
                                <button type="button" class="btn btn-primary mr-3" v-on:click="view(project.id)">View</button>
                                <button type="button" class="btn btn-warning mr-3" v-on:click="edit(project.id)">Edit</button>
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
        <template v-if="(activeView === 'edit' || activeView === 'create')">
            <div>
                <div class="row" v-if="activeView === 'create'">
                    <div class="form-group col-12">
                        <label>Name</label>
                        <input type="text" class="form-control" placeholder="Project name" v-model="currentProject.name">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12">
                        <label>Callback url</label>
                        <input type="text" class="form-control" placeholder="http://example.com" v-model="currentProject.callback_url">
                    </div>
                </div>
                <div>
                    <div class="btn btn-success mr-3" v-on:click="save()">
                        <template v-if="issetProject">
                            Update
                        </template>
                        <template v-else>
                            Create
                        </template>
                    </div>
                    <div class="btn btn-primary mr-3" v-on:click="goToList()">Back</div>
                </div>
            </div>
        </template>
        <template v-if="activeView === 'view'">
            <div>
                <div class="row">
                    <div class="form-group col-12">
                        <h4>Id</h4>
                        <div class="font-weight-bold">{{ currentProject.id }}</div>
                    </div>
                    <div class="form-group col-12">
                        <h4>Name</h4>
                        <div class="font-weight-bold">{{ currentProject.name }}</div>
                    </div>
                    <div class="form-group col-12">
                        <h4>Callback url</h4>
                        <div class="font-weight-bold">{{ currentProject.callback_url }}</div>
                    </div>
                    <div class="form-group col-12">
                        <h4>apiKey</h4>
                        <button @click="getApiKey()">Get api key</button>
                    </div>
                    <component-room v-if="currentProject.room_items" :prop-items="currentProject.room_items"></component-room>
                </div>
                <div>
                    <div class="btn btn-primary mr-3" v-on:click="goToList()">Back</div>
                </div>
            </div>
        </template>
        <template v-if="activeView === 'getApiKey'">
            <div>
                <div class="row" v-if="emptyApiKey">
                    <div class="form-group col-12">
                        <label>Login</label>
                        <input type="text" class="form-control" placeholder="Login" v-model="login">
                    </div>
                    <div class="form-group col-12">
                        <label>Password</label>
                        <input type="password" class="form-control" placeholder="Password" v-model="password">
                    </div>
                    <div class="form-group col-12">
                        <button class="btn btn-success" @click="viewApiKey()">Get key</button>
                    </div>
                </div>
                <div v-else>
                    Key sent to email
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import ProjectService from '@/services/admin/project.service'
import Pagination from '@/components/Pagination.vue'
import ComponentRoom from '@/components/modules/Room.vue'

export default {
    components: {
        ComponentRoom,
        Pagination,
    },
    name: 'admin-projects',
    data() {
        return {
            projects: Array,
            pagination: Array,
            activeView: this.propActiveView,
            projectId: this.propProjectId,
            currentProject: {},
            login: '',
            password: '',
            apiKey: '',
        }
    },
    props: {
        propActiveView: {
            type: String,
            default: 'list',
            required: false,
        },
        propProjectId: {
            type: String,
            default: '',
            required: false,
        },
    },
    computed: {
        emptyApiKey() {
            return this.apiKey === ''
        },
        currentUser() {
            return this.$store.state.auth.user
        },
        isSuperUser() {
            return !!(
                this.currentUser
                && this.currentUser['groups']
                && this.currentUser['groups'].includes('SuperUser')
            )
        },
        issetProject() {
            return typeof this.currentProject.id !== 'undefined' && this.currentProject.id > 0
        },
    },
    methods: {
        getApiKey() {
            this.activeView = 'getApiKey'
        },
        viewApiKey() {
            let self = this

            ProjectService.getApiKey(self.login, self.password, self.currentProject.id).then(
                (response) => {
                    self.apiKey = response.data
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        changePage(page) {
            if (this.pagination.currentPage === page) {
                return
            }
            this.$router.push({ name: 'projects', query: { page: page } })
        },
        goToList() {
            this.$router.push({ path: this.$router.options.history.state.back })
        },
        view(id) {
            this.$router.push({ name: 'view-project', params: { propProjectId: id } })
        },
        edit(id) {
            this.$router.push({ name: 'edit-project', params: { propProjectId: id } })
        },
        create() {
            this.$router.push({ name: 'create-project' })
        },
        getProject(id) {
            let self = this

            ProjectService.get(id).then(
                (response) => {
                    self.currentProject = response.data
                    if (self.activeView === 'list') {
                        self.activeView = 'edit'
                    }
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        getList() {
            let self = this

            ProjectService.all(this.$route.query?.page).then(
                (response) => {
                    self.projects = response.data.items
                    self.pagination = response.data.pagination
                    self.activeView = 'list'
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        save() {
            let self = this
            ProjectService.save(this.currentProject).then(
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
            if (this.activeView === 'edit' || this.activeView === 'view') {
                this.getProject(this.projectId)
            }
        },
    },
    mounted() {
        this.updatePage()
    },
}
</script>