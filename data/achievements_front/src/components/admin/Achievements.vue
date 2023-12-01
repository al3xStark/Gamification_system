<template>
    <div class="container-fluid">
        <h3 class="mt-3">
            <template v-if="activeView === 'list'">
                <h3 class="mt-3">Achievements</h3>
            </template>
            <template v-else>
                <template v-if="issetAchievement">
                    Edit achievement
                </template>
                <template v-else>
                    Create achievement
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
                        <th scope="col">Project</th>
                        <th scope="col">Event</th>
                        <th scope="col">Event fields</th>
                        <th scope="col">Count</th>
                        <th scope="col">Reward</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(achievement,index) in achievement" :key="achievement">
                        <tr>
                            <th scope="row">{{ index + 1 }}</th>
                            <td>
                                <template v-if="achievement.active">
                                    <i class="fa-xl bi bi-check-circle-fill text-success"></i>
                                </template>
                                <template v-else>
                                    <i class="fa-xl bi bi-x-circle-fill text-danger"></i>
                                </template>
                            </td>
                            <td>{{ achievement.name }}</td>
                            <td>
                                <router-link :to="{ name: 'edit-project', params: { propProjectId: achievement.project.id }}">{{ achievement.project.name }}</router-link>
                            </td>
                            <td>
                                <router-link :to="{ name: 'edit-event', params: { propEventId: achievement.event.id }}">{{ achievement.event.name }}</router-link>
                            </td>
                            <td>
                                <div v-for="(field, key) in achievement.event_fields" :key="field">{{ key }} = {{ field }}</div>
                            </td>
                            <td>{{ achievement.count }}</td>
                            <td><img style="max-width: 25px;" :src="achievement.item_template.template"></td>
                            <td>
                                <button type="button" class="btn btn-primary mr-3" v-on:click="edit(achievement.id)">Edit</button>
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
                        <input type="text" class="form-control" placeholder="Achievement name" v-model="currentAchievement.name">
                    </div>
                    <div class="form-group col-12">
                        <label>Count</label>
                        <input type="text" class="form-control" placeholder="Achievement count" v-model="currentAchievement.count">
                    </div>
                    <template v-if="activeView === 'create'">
                        <div class="form-group col-12">
                            <label>Project</label>
                            <select class="form-control" v-model="currentAchievement.project_id" @change="getEventsAndTemplates()">
                                <option v-for="(project, index) in projects" :key="project" :value="index">{{ project }}</option>
                            </select>
                        </div>
                        <div class="form-group col-12">
                            <label>Reward</label>
                            <select class="form-control" v-model="currentAchievement.item_template_id">
                                <option v-for="itemTemplate in itemTemplates" :key="itemTemplate.id" :value="itemTemplate.id">
                                    {{ itemTemplate.name }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-12">
                            <label>Event</label>
                            <select class="form-control" v-model="currentAchievement.event_id" @change="clearFields()">
                                <option v-for="event in events" :key="event.id" :value="event.id">
                                    {{ event.name }}
                                </option>
                            </select>
                        </div>
                        <div v-if="currentAchievement.event_id">
                            <div class="form-group col-12" v-for="(field) in getFields()" :key="field">
                                <label>Field {{ field }}</label>
                                <input type="text" class="form-control" :placeholder="field" v-model="currentAchievement.event_fields[field]">
                            </div>
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
import AchievementService from '../../services/admin/achievement.service'
import ProjectService from '@/services/admin/project.service'
import Pagination from '@/components/Pagination.vue'
import ItemTemplateService from '@/services/admin/item-template.service'
import EventService from '@/services/admin/event.service'

export default {
    components: {
        Pagination,
    },
    name: 'admin-achievement',
    data() {
        return {
            achievement: Array,
            pagination: Array,
            activeView: this.propActiveView,
            achievementId: this.propAchievementId,
            currentAchievement: {},
            projects: [],
            itemTemplates: [],
            events: [],
        }
    },
    props: {
        propActiveView: {
            type: String,
            default: 'list',
            required: false,
        },
        propAchievementId: {
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
            this.$router.push({ name: 'achievements', query: { page: page } })
        },
        goToList() {
            this.$router.push({ path: this.$router.options.history.state.back })
        },
        edit(id) {
            this.$router.push({ name: 'edit-achievement', params: { propAchievementId: id } })
        },
        create() {
            this.$router.push({ name: 'create-achievement' })
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
        clearFields() {
            this.currentAchievement.event_fields = {}
        },
        getFields() {
            return this.events.find((event) => event.id === this.currentAchievement.event_id).fields
        },
        getEventsAndTemplates() {
            let self = this
            self.currentAchievement.item_template_id = 0
            self.currentAchievement.event_id = 0
            self.currentAchievement.event_fields = {}

            ItemTemplateService.allowListForProject(self.currentAchievement.project_id).then(
                (response) => {
                    self.itemTemplates = response.data
                },
                (error) => {
                    console.log(error)
                },
            )

            EventService.allowListForProject(self.currentAchievement.project_id).then(
                (response) => {
                    self.events = response.data
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        getAchievement(id) {
            let self = this
            AchievementService.get(id).then(
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

            AchievementService.all(this.$route.query?.page).then(
                (response) => {
                    self.achievement = response.data.items
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
            AchievementService.save(this.currentAchievement).then(
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
                this.getAchievement(this.achievementId)
            }
            if (this.activeView === 'create') {

                await this.getAllowProjects()
                this.currentAchievement = {
                    id: 0,
                    active: true,
                    name: '',
                    project_id: 0,
                    count: 1,
                    item_template_id: 0,
                    event_id: 0,
                    event_fields: {},
                }
            }
        },
    },
    mounted() {
        this.updatePage()
    },
}
</script>
