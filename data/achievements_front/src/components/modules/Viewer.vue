<template>
    <div class="slider row">
        <div class="slider-images">
            <img v-for="(image, index) in images"
                 v-bind:src="image.image"
                 v-bind:key="index"
                 v-bind:class="{active: index === currentIndex}">
        </div>
        <div class="slider-controls">
            <button class="prev" v-on:click="prevImage">Prev</button>
            <button class="next" v-on:click="nextImage">Next</button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'module-viewer',
    props: {
        images: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            currentIndex: 0,
        };
    },
    methods: {
        prevImage() {
            this.currentIndex = (this.currentIndex === 0) ? (this.images.length - 1) : (this.currentIndex - 1);
        },
        nextImage() {
            this.currentIndex = (this.currentIndex === this.images.length - 1) ? 0 : (this.currentIndex + 1);
        }
    }
};
</script>


<style lang="scss">
@import "@/assets/styles/modules/viewer.scss";
</style>