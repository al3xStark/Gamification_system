<template>
    <div class="container-fluid my-games-page">
        <div class="col-12">
            <div class="title">My games</div>
            <div class="games-list">
                <template v-for="(game) in games" :key="game">
                    <router-link :to="{name: 'page-games', params: { propGameId: game.id }}" class="nav-link">
                        {{ game.name }}
                    </router-link>
                </template>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
@import "@/assets/styles/my-games.scss";
</style>

<script>

import GamesService from '@/services/game.service'

export default {
    name: 'page-games',
    data() {
        return {
            games: [],
        }
    },
    mounted() {
        this.getMyGames()
    },
    methods: {
        getMyGames() {
            let self = this
            GamesService.myGames().then(
                (response) => {
                    self.games = response.data
                },
                (error) => {
                    console.log(error)
                },
            )
        },
    },
}
</script>
