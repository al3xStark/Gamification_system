<template>
    <div></div>
</template>

<script>
import { OBJLoader } from 'three/addons/loaders/OBJLoader.js'
import { MTLLoader } from 'three/addons/loaders/MTLLoader.js'

export default {
    name: 'room-item',
    props: {
        id: { type: String, default: '' },
        coordinates: {
            type: Object,
            default() {
                return {
                    x: 0,
                    y: 0,
                    z: 0,
                }
            },
        },
        rotation: {
            type: Object,
            default() {
                return {
                    x: 0,
                    y: 0,
                    z: 0,
                }
            },
        },
        object: { type: String, default: '' },
        material: { type: String, default: '' },
        template: { type: String, default: '' },
        link: { type: String, default: '' },
    },
    mounted() {
        let self = this

        let objLoader = new OBJLoader()
        let mtlLoader = new MTLLoader()

        mtlLoader.load(self.material, (mtl) => {
            mtl.materialsInfo[Object.keys(mtl.materialsInfo)[0]].map_kd = self.template
            mtl.preload()
            objLoader.setMaterials(mtl)
            objLoader.load(self.object, (object) => {

                self.currentRender = object
                self.currentRender.position.set(
                    self.coordinates.x,
                    self.coordinates.y,
                    self.coordinates.z,
                )
                self.currentRender.rotation.set(
                    2 * Math.PI * (self.rotation.x / 360),
                    2 * Math.PI * (self.rotation.y / 360),
                    2 * Math.PI * (self.rotation.z / 360),
                )

                self.currentRender.children[0].addEventListener('goToLink', function() {
                    if (self.link !== '') {
                        window.parent.location = self.link
                    }
                });

                self.$parent.scene.add(self.currentRender)
            })
        })
    },
    beforeUnmount() {
        this.$parent.scene.remove(this.currentRender)
    },
}
</script>