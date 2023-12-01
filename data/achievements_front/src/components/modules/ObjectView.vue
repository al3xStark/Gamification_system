<template>
    <div class="container-fluid">
        <div class="row">
            <div class="object-view" ref="canvasWrapper">
                <room-item
                    :uid="object.uid"
                    :object="object.object"
                    :material="object.material"
                    :template="object.template"
                    :coordinates="object.coordinates"
                    :rotation="object.rotation"
                >
                </room-item>
            </div>
        </div>
    </div>
</template>

<style>
.object-view {
    width: 300px;
    height: 200px;
}
</style>

<script>
import * as THREE from 'three'
import * as TWEEN from '@tweenjs/tween.js'
import RoomItem from '@/components/modules/RoomItem.vue'

export default {
    name: 'admin-object-view',
    components: { RoomItem },
    props: {
        propObject: {
            type: Object,
            default() {
                return {
                    x: 0,
                    y: 0,
                    z: 0,
                }
            }
        },
    },
    data() {
        return {
            object: this.propObject,
            userAuthKey: this.propUserAuthKey,
            target: new THREE.Vector3(100, 100, 100),
            distance: 1500,
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
            isDragging: false,
            currentObject: false,
            allowMoving: false,
            items: [],
        }
    },
    mounted() {
        this.scene = new THREE.Scene()

        this.camera = new THREE.PerspectiveCamera(45, this.$el.clientWidth / this.$el.clientHeight, 1, 10000)
        this.rotateCamera(75, 30)
        this.camera.lookAt(0, 0, 0)

        this.renderer = new THREE.WebGLRenderer({ antialias: true })
        this.renderer.setSize(this.$el.clientWidth, this.$el.clientHeight)
        this.renderer.setClearColor(0xffffff, 1)
        this.$refs.canvasWrapper.appendChild(this.renderer.domElement)

        let light = new THREE.DirectionalLight(0xFFFFFF, 3)
        light.position.set(900, 600, 900)
        this.scene.add(light)

        this.render()

    },
    beforeUnmount() {
    },
    methods: {
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
    },
}
</script>