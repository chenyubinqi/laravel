<template>
    <div class="main-container">
        <navbar></navbar>
        <div class="content-container">
            <router-view></router-view>
        </div>

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

<style>

    .main-container {
        min-height: 100%;
        transition: margin-left 0.28s ease-out;
    }

    .content-container {
        margin-top: 10px;
    }

</style>
