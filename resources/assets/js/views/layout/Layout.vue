<template>
    <div class="main-container">
        <navbar></navbar>
        <router-view></router-view>
    </div>
</template>

<script>
    import  {Navbar} from 'views/layout'

    export default {
        name: 'layout',
        components: {
            Navbar
        }
    }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>

    .main-container {
        min-height: 100%;
        transition: margin-left 0.28s ease-out;
    }

</style>
