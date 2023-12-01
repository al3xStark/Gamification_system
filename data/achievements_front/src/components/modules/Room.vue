<template>
    <div class="container-fluid">
        <div class="row">
            <div class="room" ref="canvasWrapper">
                <template
                    v-for="item in items"
                    :key="item"
                >
                    <room-item
                        :object="item.object"
                        :material="item.material"
                        :template="item.template"
                        :coordinates="item.coordinates"
                        :rotation="item.rotation"
                        :link="item.link"
                    >
                    </room-item>
                </template>
            </div>
            <div class="stats" v-if="isIssetStats">
                <div>Achievements progress</div>
                <div
                    v-for="stat in stats"
                    :key="stat"
                >
                    {{ stat.achievement }}
                    <progress max="100" :value="stat.progress" :class="'progress_' + stat.progress"></progress>
                </div>
            </div>
            <div class="achievements" v-if="isIssetAchievements">
                <Dropdown @visible-change="change">
                    <template #trigger>
                        <div class="btn btn-success">Rewards</div>
                    </template>
                    <div
                        v-for="achievement in achievements"
                        :key="achievement"
                    >
                        <div class="achievements-item">
                            {{ achievement.achievement }}
                            <span class="button" v-on:click="setTemplate(achievement.id)">Set</span>
                        </div>
                    </div>
                </Dropdown>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
@import "@/assets/styles/user-room.scss";
</style>

<script>
import * as THREE from 'three'
import * as TWEEN from '@tweenjs/tween.js'
import RoomItem from './RoomItem.vue'
import UserService from '@/services/user.service'
import Dropdown from 'v-dropdown'


export default {
    name: 'component-room',
    components: {
        RoomItem,
        Dropdown,
    },
    props: {
        propUserAuthKey: {
            type: String,
            default: '',
        },
        propProjectId: {
            type: String,
            default: '',
        },
        propItems: {
            type: Object,
            default() {
                return {}
            }
        },
    },
    data() {
        return {
            userAuthKey: this.propUserAuthKey,
            projectId: this.propProjectId,
            items: Object.entries(this.propItems).map((arr) => (arr[1])),
            target: new THREE.Vector3(250, 200, 250),
            distance: 1000,
            phi: Math.PI / 2,
            theta: Math.PI / 2,
            minPhi: 0.1,
            maxPhi: Math.PI / 2,
            minTheta: 0,
            maxTheta: Math.PI / 2,
            loading: true,
            mouseX: 0,
            mouseY: 0,
            move: false,
            mouse: new THREE.Vector2(),
            raycaster: new THREE.Raycaster(),
            isDragging: false,
            currentObject: false,
            allowMoving: false,
            stats: [],
            achievements: [],
        }
    },
    mounted() {
        this.scene = new THREE.Scene()

        this.camera = new THREE.PerspectiveCamera(45, this.$el.clientWidth / this.$el.clientHeight, 1, 10000)
        this.rotateCamera(75, 30)
        this.camera.lookAt(this.target)

        this.renderer = new THREE.WebGLRenderer({ antialias: true })
        this.renderer.setSize(this.$el.clientWidth, this.$el.clientHeight)
        this.renderer.setClearColor(0x3B3B3B, 1)
        this.$refs.canvasWrapper.appendChild(this.renderer.domElement)

        let light = new THREE.DirectionalLight(0xFFFFFF, 1)
        light.position.set(900, 600, 900)
        this.scene.add(light)

        this.$refs.canvasWrapper.addEventListener('mousedown', this.handleMouseDown)
        this.$refs.canvasWrapper.addEventListener('mousemove', this.handleMouseMove)
        this.$refs.canvasWrapper.addEventListener('mouseup', this.handleMouseUp)
        this.$refs.canvasWrapper.addEventListener('click', this.handleMouseClick)

        this.render()
    },
    beforeUnmount() {
        this.$refs.canvasWrapper.removeEventListener('mousedown', this.handleMouseDown)
        this.$refs.canvasWrapper.removeEventListener('mousemove', this.handleMouseMove)
        this.$refs.canvasWrapper.removeEventListener('mouseup', this.handleMouseUp)
        this.$refs.canvasWrapper.removeEventListener('click', this.handleMouseClick)
    },
    methods: {
        handleMouseClick(event) {
            this.mouse = new THREE.Vector2()
            this.mouse.x = (event.clientX / window.innerWidth) * 2 - 1
            this.mouse.y = -(event.clientY / window.innerHeight) * 2 + 1

            this.raycaster.setFromCamera(this.mouse, this.camera)

            let intersects = this.raycaster.intersectObjects(this.scene.children, true)

            if (intersects.length) {
                intersects[0].object.dispatchEvent({ type: 'goToLink' });
            }
        },
        handleMouseDown(event) {
            this.isDragging = true
            this.mouseX = event.clientX
            this.mouseY = event.clientY
        },
        handleMouseMove(event) {
            if (
                this.allowMoving
                && !this.currentObject
            ) {
                console.log('moving')
            }

            if (this.isDragging) {
                if (this.currentObject) {
                    const deltaX = event.clientX - this.mouseX
                    const deltaY = event.clientY - this.mouseY
                    this.moveElement(-deltaX / 2, deltaY / 2)
                    this.mouseX = event.clientX
                    this.mouseY = event.clientY
                } else {
                    const deltaX = event.clientX - this.mouseX
                    const deltaY = event.clientY - this.mouseY
                    this.rotateCamera(-deltaX / 2, deltaY / 2)
                    this.mouseX = event.clientX
                    this.mouseY = event.clientY
                }
            }
        },
        handleMouseUp() {
            this.isDragging = false
            this.currentObject = false
        },
        moveElement(deltaX, deltaY) {
            console.log(deltaX)
            console.log(deltaY)
        },
        rotateCamera(deltaX, deltaY) {
            this.phi -= deltaY * 0.01
            this.theta -= deltaX * 0.01

            this.phi = Math.max(this.minPhi, Math.min(this.maxPhi, this.phi))

            this.theta = Math.min(this.maxTheta, Math.max(this.minTheta, this.theta))

            this.camera.position.x = this.target.x + this.distance * Math.sin(this.phi) * Math.cos(this.theta)
            this.camera.position.y = this.target.y + this.distance * Math.cos(this.phi)
            this.camera.position.z = this.target.z + this.distance * Math.sin(this.phi) * Math.sin(this.theta)

            this.camera.lookAt(this.target)
        },
        render() {
            this.renderer.render(this.scene, this.camera)
            requestAnimationFrame(this.render)
            TWEEN.update()
        },
        getItems() {
            let self = this
            UserService.getUserBoard(self.userAuthKey).then(
                (response) => {
                    self.items = response.data
                    self.loading = false
                },
                (error) => {
                    console.log((error.response &&
                            error.response.data &&
                            error.response.data.message) ||
                        error.message ||
                        error.toString())
                },
            )
        },
        getStats() {
            let self = this
            UserService.getStatsProgress(self.userAuthKey).then(
                (response) => {
                    self.stats = response.data
                    self.loading = false
                },
                (error) => {
                    console.log((error.response &&
                            error.response.data &&
                            error.response.data.message) ||
                        error.message ||
                        error.toString())
                },
            )
        },
        getAchievements() {
            let self = this
            UserService.getAchievements(self.userAuthKey).then(
                (response) => {
                    self.achievements = response.data
                    self.loading = false
                },
                (error) => {
                    console.log((error.response &&
                            error.response.data &&
                            error.response.data.message) ||
                        error.message ||
                        error.toString())
                },
            )
        },
        setTemplate(id) {
            let self = this
            UserService.setAchievementTemplate(self.userAuthKey, id).then(
                () => {
                    self.getItems()
                },
                (error) => {
                    console.log((error.response &&
                            error.response.data &&
                            error.response.data.message) ||
                        error.message ||
                        error.toString())
                },
            )
        },
    },
    created() {
        if (this.userAuthKey.length) {
            this.getItems()
            this.getStats()
            this.getAchievements()
        } else {
            this.loading = false
        }
    },
    computed: {
        isIssetStats() {
            return !!this.stats.length
        },
        isIssetAchievements() {
            return !!this.achievements.length
        },
    },
}
</script>