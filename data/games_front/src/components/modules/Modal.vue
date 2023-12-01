<template>
    <teleport to="body">
        <div class="modal-wrapper">
            <div class="modal-overlay" @click="closeModal"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3>{{ title }}</h3>
                    <button @click="closeModal">X</button>
                </div>
                <div class="modal-body">
                    <slot></slot>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script>
export default {
    name: "component-modal",
    props: {
        title: String,
    },
    methods: {
        closeModal() {
            this.$emit("close");
        },
    },
};
</script>

<style>
.modal-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    z-index: 1;
    width: 80%;
    max-width: 600px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #ccc;
}

.modal-header h3 {
    margin: 0;
}

.modal-header button {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}

.modal-body {
    padding: 10px;
}
</style>