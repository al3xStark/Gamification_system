<template>
    <div>
        <ul class="pagination">
            <li :class="{ disabled: currentPage === 1 }">
                <a @click="prevPage">&laquo;</a>
            </li>
            <li v-for="page in pages" :key="page" :class="{ active: currentPage === page }">
                <a @click="changePage(page)">{{ page }}</a>
            </li>
            <li :class="{ disabled: currentPage === totalPages }">
                <a @click="nextPage">&raquo;</a>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "components-pagination",
    props: {
        totalItems: {
            type: Number,
            required: true
        },
        itemsPerPage: {
            type: Number,
            default: 10
        },
        currentPage: {
            type: Number,
            default: 1
        }
    },
    computed: {
        totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
        },
        pages() {
            let pages = [];
            for (let i = 1; i <= this.totalPages; i++) {
                pages.push(i);
            }
            return pages;
        }
    },
    methods: {
        changePage(page) {
            this.$emit("page-changed", page);
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.changePage(this.currentPage - 1);
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.changePage(this.currentPage + 1);
            }
        }
    }
};
</script>

<style>
.pagination {
    display: inline-block;
}
.pagination li {
    display: inline-block;
    margin: 0 2px;
    border-radius: 3px;
    background-color: #fff;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}
.pagination li.disabled {
    color: #ccc;
}
.pagination li a {
    color: #337ab7;
    text-decoration: none;
    padding: 5px 10px;
    cursor: pointer;
}
.pagination li.active a,
.pagination li.disabled a {
    cursor: default;
}
.pagination li.active {
    background-color: #337ab7;
    color: #fff;
}
</style>