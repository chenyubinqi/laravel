<template>
    <el-menu :default-active="activeIndex" :router="true" class="el-menu-demo" mode="horizontal" @select="handleSelect">
        <el-submenu index="elasticsearch">
            <template slot="title">elasticsearch</template>
            <el-menu-item index="/search">搜索</el-menu-item>
            <el-menu-item index="/search">折叠分页</el-menu-item>
        </el-submenu>
        <!--<el-submenu index="2">-->
            <!--<template slot="title">我的工作台</template>-->
        <!--</el-submenu>-->
        <!--<el-menu-item index="3"><a href="https://www.ele.me" target="_blank">订单管理</a></el-menu-item>-->
    </el-menu>
</template>
<script>
    export default {
        data() {
            return {
                activeIndex: 'elasticsearch',
            };
        },
        methods: {
            handleSelect(key, keyPath) {
                console.log(key, keyPath);
            }
        }
    }
</script>