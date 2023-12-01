<template>
    <div class="container-fluid">
        <h3 class="mt-3">
            <template v-if="activeView === 'list'">
                <h3 class="mt-3">Events</h3>
            </template>
            <template v-else>
                <template v-if="issetAchievement">
                    Edit event
                </template>
                <template v-else>
                    Create event
                </template>
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
                        <th scope="col">Active</th>
                        <th scope="col">Name</th>
                        <th scope="col">Code</th>
                        <th scope="col">Fields</th>
                        <th scope="col">Project</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(event,index) in event" :key="event">
                        <tr>
                            <th scope="row">{{ index + 1 }}</th>
                            <td>
                                <template v-if="event.active">
                                    <i class="fa-xl bi bi-check-circle-fill text-success"></i>
                                </template>
                                <template v-else>
                                    <i class="fa-xl bi bi-x-circle-fill text-danger"></i>
                                </template>
                            </td>
                            <td>{{ event.name }}</td>
                            <td>{{ event.code }}</td>
                            <td>
                                <div v-for="field in event.fields" :key="field">{{ field }}</div>
                            </td>
                            <td>
                                <router-link :to="{ name: 'edit-project', params: { propProjectId: event.project.id }}">{{ event.project.name }}</router-link>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary mr-3" v-on:click="edit(event.id)">Edit</button>
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
        <template v-if="activeView === 'edit' || activeView === 'create'">
            <div>
                <div class="row">
                    <div class="form-group col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="currentAchievement.active">
                            <label class="form-check-label" for="activeCheckBox">
                                Active
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-12">
                        <label>Name</label>
                        <input type="text" class="form-control" placeholder="Event name" v-model="currentAchievement.name">
                    </div>
                    <template v-if="activeView === 'create'">
                        <div class="form-group col-12">
                            <label>Code</label>
                            <input type="text" class="form-control" placeholder="Event code" v-model="currentAchievement.code">
                        </div>
                        <div class="form-group col-12">
                            <label>Fields</label>
                            <input type="text" class="form-control" placeholder="game_name,game_id" v-model="currentAchievement.fields">
                        </div>
                        <div class="form-group col-12">
                            <label>Project</label>
                            <select class="form-control" v-model="currentAchievement.project_id" @change="getAllowItemTemplates()">
                                <option v-for="(project, index) in projects" :key="project" :value="index">{{ project }}</option>
                            </select>
                        </div>
                    </template>
                </div>
                <div>
                    <div class="btn btn-success mr-3" v-on:click="save()">
                        <template v-if="issetAchievement">
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
    </div>
</template>

<script>
import EventService from '../../services/admin/event.service'
import ProjectService from '@/services/admin/project.service'
import Pagination from '@/components/Pagination.vue'
import ItemTemplateService from '@/services/admin/item-template.service'

export default {
    components: {
        Pagination,
    },
    name: 'admin-event',
    data() {
        return {
            event: Array,
            pagination: Array,
            activeView: this.propActiveView,
            eventId: this.propEventId,
            currentAchievement: {},
            projects: [],
            itemTemplates: [],
        }
    },
    props: {
        propActiveView: {
            type: String,
            default: 'list',
            required: false,
        },
        propEventId: {
            type: String,
            default: '',
            required: false,
        },
    },
    computed: {
        issetAchievement() {
            return typeof this.currentAchievement.id !== 'undefined' && this.currentAchievement.id > 0
        },
    },
    methods: {
        changePage(page) {
            if (this.pagination.currentPage === page) {
                return
            }
            this.$router.push({ name: 'events', query: { page: page } })
        },
        goToList() {
            this.$router.push({ path: this.$router.options.history.state.back })
        },
        edit(id) {
            this.$router.push({ name: 'edit-event', params: { propEventId: id } })
        },
        create() {
            this.$router.push({ name: 'create-event' })
        },
        async getAllowProjects() {
            let self = this
            await ProjectService.allowProjectList().then(
                (response) => {
                    self.projects = response.data
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        getAllowItemTemplates() {
            let self = this
            ItemTemplateService.allowListForProject(self.currentAchievement.project_id).then(
                (response) => {
                    self.itemTemplates = response.data
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        getAchievement(id) {
            let self = this
            EventService.get(id).then(
                (response) => {
                    self.currentAchievement = response.data
                    self.activeView = 'edit'
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        getList() {
            let self = this

            EventService.all(this.$route.query?.page).then(
                (response) => {
                    self.event = response.data.items
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
            EventService.save(this.currentAchievement).then(
                () => {
                    self.goToList()
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        async updatePage() {
            if (this.activeView === 'list') {
                this.getList()
            }
            if (this.activeView === 'edit') {
                this.getAchievement(this.eventId)
            }
            if (this.activeView === 'create') {

                await this.getAllowProjects()
                this.currentAchievement = {
                    id: 0,
                    active: true,
                    name: '',
                    code: '',
                    project_id: 0,
                    fields: null,
                }
            }
        },
    },
    mounted() {
        this.updatePage()
    },
}
</script>
