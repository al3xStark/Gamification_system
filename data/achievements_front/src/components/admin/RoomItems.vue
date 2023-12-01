<template>
    <div class="container-fluid">
        <h3 class="mt-3">
            <template v-if="activeView === 'list'">
                <h3 class="mt-3">Room items</h3>
            </template>
            <template v-else>
                <template v-if="issetRoomItem">
                    Edit room item
                </template>
                <template v-else>
                    Create room item
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
                        <th scope="col">Object</th>
                        <th scope="col">Project</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(roomItem,index) in roomItems" :key="roomItem">
                        <tr>
                            <th scope="row">{{ index + 1 }}</th>
                            <td>
                                <template v-if="roomItem.active">
                                    <i class="fa-xl bi bi-check-circle-fill text-success"></i>
                                </template>
                                <template v-else>
                                    <i class="fa-xl bi bi-x-circle-fill text-danger"></i>
                                </template>
                            </td>
                            <td>{{ roomItem.name }}</td>
                            <td><admin-object-view :propObject="getObject(roomItem)"></admin-object-view></td>
                            <td>
                                <router-link v-if="roomItem.project?.id" :to="{ name: 'edit-project', params: { propProjectId: roomItem.project.id }}">{{ roomItem.project.name }}</router-link>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary mr-3" v-on:click="edit(roomItem.id)">Edit</button>
                                <button type="button" class="btn btn-warning mr-3" v-on:click="getTemplates(roomItem.id)">Templates</button>
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
                            <input class="form-check-input" type="checkbox" v-model="currentRoomItem.active">
                            <label class="form-check-label" for="activeCheckBox">
                                Active
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-12">
                        <label>Name</label>
                        <input type="text" class="form-control" placeholder="Item name" v-model="currentRoomItem.name">
                    </div>
                    <div class="form-group col-12" v-if="activeView === 'create'">
                        <label>Object</label>
                        <select class="form-control" v-model="currentRoomItem.default_room_item_id">
                            <option v-for="item in defaultRoomItems" :key="item" :value="item.id">{{ item.code }}</option>
                        </select>
                    </div>
                    <div class="form-group col-4" v-if="setObject">
                        <admin-object-view :propObject="getDefaultObject" :key="currentRoomItem.default_room_item_id"></admin-object-view>
                    </div>
                    <div class="form-group col-8" v-if="setObject">
                        <a target="_blank" :href="getDefaultObject.template">Download template</a>
                    </div>
                    <div class="form-group col-12">
                        <label>Coordinates (x,y,z)</label>
                        <input type="text" class="form-control" placeholder="0,20,0" v-model="currentRoomItem.coordinates">
                    </div>
                    <div class="form-group col-12">
                        <label>Rotation (x,y,z)</label>
                        <input type="text" class="form-control" placeholder="0,90,0" v-model="currentRoomItem.rotation">
                    </div>
                    <template v-if="activeView === 'create'">
                        <div class="form-group col-12">
                            <label>Project</label>
                            <select class="form-control" v-model="currentRoomItem.project_id">
                                <option v-for="(project, index) in projects" :key="project" :value="index">{{ project }}</option>
                            </select>
                        </div>
                    </template>
                    <div class="form-group col-12">
                        <label>Template</label>
                        <input type="file" @change="handleFileUpload( $event )"/>
                    </div>
                    <div class="form-group col-12">
                        <label>Link</label>
                        <input type="text" class="form-control" placeholder="http://example.com" v-model="currentRoomItem.link">
                    </div>
                </div>
                <div>
                    <div class="btn btn-success mr-3" v-on:click="save()">
                        <template v-if="issetRoomItem">
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
import ProjectService from '@/services/admin/project.service'
import RoomItemService from '../../services/admin/roomItem.service'
import Pagination from '@/components/Pagination.vue'
import AdminObjectView from '@/components/modules/ObjectView.vue'

export default {
    components: {
        AdminObjectView,
        Pagination,
    },
    name: 'admin-room-items',
    data() {
        return {
            roomItems: Array,
            pagination: Array,
            activeView: this.propActiveView,
            roomItemId: this.propRoomItemId,
            currentRoomItem: {},
            projects: [],
            defaultRoomItems: [],
        }
    },
    props: {
        propActiveView: {
            type: String,
            default: 'list',
            required: false,
        },
        propRoomItemId: {
            type: String,
            default: '',
            required: false,
        },
    },
    computed: {
        issetRoomItem() {
            return typeof this.currentRoomItem.id !== 'undefined' && this.currentRoomItem.id > 0
        },
        setObject() {
            return typeof this.currentRoomItem.default_room_item_id !== 'undefined' && this.currentRoomItem.default_room_item_id > 0
        },
        getDefaultObject() {
            let self = this
            return self.defaultRoomItems.find((elem) => elem.id === this.currentRoomItem.default_room_item_id)
        },
    },
    methods: {
        changePage(page) {
            if (this.pagination.currentPage === page) {
                return
            }
            this.$router.push({ name: 'room-items', query: { page: page } })
        },
        goToList() {
            this.$router.push({ path: this.$router.options.history.state.back })
        },
        edit(id) {
            this.$router.push({ name: 'edit-room-item', params: { propRoomItemId: id } })
        },
        getTemplates(id) {
            this.$router.push('/admin/room-item-templates/template/' + id)
        },
        create() {
            this.$router.push({ name: 'create-room-item' })
        },
        getRoomItem(id) {
            let self = this

            RoomItemService.get(id).then(
                (response) => {
                    self.currentRoomItem = response.data
                    self.currentRoomItem.file =  null
                    self.activeView = 'edit'
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        getDefaultRoomItem() {
            let self = this

            RoomItemService.getDefault().then(
                (response) => {
                    self.defaultRoomItems = response.data
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        getList() {
            let self = this

            RoomItemService.all(this.$route.query?.page).then(
                (response) => {
                    self.roomItems = response.data.items
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

            RoomItemService.save(this.currentRoomItem).then(
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
                this.getRoomItem(this.roomItemId)
                this.getDefaultRoomItem(this.roomItemId)
            }
            if (this.activeView === 'create') {
                this.currentRoomItem = {
                    id: 0,
                    active: true,
                    name: '',
                    project_id: 0,
                    default_room_item_id: 0,
                    coordinates: '0,0,0',
                    rotation: '0,0,0',
                    file: null,
                    link: '',
                }
                await this.getAllowProjects()
                this.getDefaultRoomItem(this.roomItemId)
            }
        },
        async getAllowProjects() {
            let self = this
            await ProjectService.allowProjectList().then(
                (response) => {
                    self.projects = response.data

                    if (self.currentRoomItem.project_id === 0) {
                        self.currentRoomItem.project_id = Object.keys(self.projects)[0]
                    }
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        handleFileUpload(event) {
            this.currentRoomItem.file = event.target.files[0]
        },
        getObject(item) {
            let object = item.default_item
            object.template = item.template
            return object
        },
    },
    mounted() {
        this.updatePage()
    },
}
</script>
