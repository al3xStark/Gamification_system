<template>
    <div class="container-fluid game-page">
        <div class="row search-game" v-if="activeView === 'list'">
            <div class="form-group col-12">
                <input class="search-line" type="text" v-model="gameName" @input="getGameList()">
                <div class="find-list" v-if="games.length">
                    <template v-for="(game) in games" :key="game">
                        <router-link :to="{name: 'page-games', params: { propGameId: game.id }}" class="nav-link">
                            {{ game.name }}
                        </router-link>
                    </template>
                </div>
            </div>
        </div>
        <div class="row game" v-if="activeView === 'game'">
            <div class="game-panel" v-if="gameLoad">
                <div class="game-background vignette" :style="{'background': 'url('+currentGame.background_image+')'}">
                    <div class="title">{{ currentGame.name }}</div>
                    <comments :prop-game-id="currentGame.id"></comments>
                    <viewer :images="currentGame.short_screenshots"></viewer>
                    <div class="rating-area" @mouseover="setRating = true" @mouseleave="setRating = false">
                        <input type="radio" id="star-5" name="rating" value="5" :class="{current: calcRating(5)}" v-model="rating" @change="onChange($event)">
                        <label for="star-5" title="Оценка «5»"></label>
                        <input type="radio" id="star-4" name="rating" value="4" :class="{current: calcRating(4)}" v-model="rating" @change="onChange($event)">
                        <label for="star-4" title="Оценка «4»"></label>
                        <input type="radio" id="star-3" name="rating" value="3" :class="{current: calcRating(3)}" v-model="rating" @change="onChange($event)">
                        <label for="star-3" title="Оценка «3»"></label>
                        <input type="radio" id="star-2" name="rating" value="2" :class="{current: calcRating(2)}" v-model="rating" @change="onChange($event)">
                        <label for="star-2" title="Оценка «2»"></label>
                        <input type="radio" id="star-1" name="rating" value="1" :class="{current: calcRating(1)}" v-model="rating" @change="onChange($event)">
                        <label for="star-1" title="Оценка «1»"></label>
                    </div>
                </div>
            </div>
            <div>
                <div class="description">{{ currentGame.description }}</div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
@import "@/assets/styles/games.scss";
</style>

<script>

import GamesService from '@/services/game.service'
import Viewer from '@/components/modules/Viewer.vue'
import Comments from '@/components/modules/Comments.vue'

export default {
    name: 'page-games',
    components: {
        Viewer,
        Comments
    },
    props: {
        propActiveView: {
            type: String,
            default: 'list',
            required: false,
        },
        propGameId: {
            type: Number,
            default: 0,
            required: false,
        },
    },
    data() {
        return {
            activeView: this.propActiveView,
            gameId: this.propGameId,
            gameName: '',
            setRating: false,
            rating: 0,
            allowSetRate: true,
            games: [],
            currentGame: [],
        }
    },
    mounted() {
        this.updatePage()
    },
    computed: {
        gameLoad() {
            return typeof this.currentGame.name !== "undefined"
        }
    },
    methods: {
        onChange(event) {
            GamesService.setRate(this.gameId, event.target.value).then(
                (response) => {
                    console.log(response)
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        calcRating(num) {
            return Math.round(this.currentGame.rating) === num && !this.setRating && !this.rating
        },
        getGameList() {
            let self = this
            if (this.gameName.length > 2) {
                GamesService.getAutocompleteList(this.gameName).then(
                    (response) => {
                        self.games = response.data
                    },
                    (error) => {
                        console.log(error)
                    },
                )
            } else {
                self.games = []
            }
        },
        getGameInfo() {
            let self = this
            GamesService.get(this.gameId).then(
                (response) => {
                    self.currentGame = response.data
                },
                (error) => {
                    console.log(error)
                },
            )
        },
        async updatePage() {
            if (this.activeView === 'game') {
                this.getGameInfo()
            }
        },
    },
}
</script>
