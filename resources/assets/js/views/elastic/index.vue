<template>
    <div class="app-container calendar-list-container">
        <div class="filter-container">
            <el-input style="width: 200px;" class="filter-item" placeholder="产品ID" v-model="listQuery.id">
            </el-input>
            <el-input style="width: 200px;" class="filter-item" placeholder="产品名称" v-model="listQuery.product_name">
            </el-input>
            <el-input style="width: 200px;" class="filter-item" placeholder="CAS号" v-model="listQuery.cas_no">
            </el-input>

            <el-select clearable style="width: 90px" class="filter-item" v-model="listQuery.status" placeholder="状态">
                <el-option v-for="item,index in statusOptions" :key="index" :label="item" :value="index">
                </el-option>
            </el-select>

            <el-input style="width: 200px;" class="filter-item" placeholder="来源" v-model="listQuery.source">
            </el-input>

            <el-input style="width: 200px;" class="filter-item" placeholder="父分类"
                      v-model="listQuery.product_parent_category">
            </el-input>

            <el-date-picker
                    v-model="create_time"
                    type="datetimerange"
                    :picker-options="pickerOptions2"
                    placeholder="选择时间范围"
                    align="right">
            </el-date-picker>

            <el-button class="filter-item" type="primary" v-waves icon="search" @click="handleFilter">搜索</el-button>
        </div>

        <el-table :key='tableKey' :data="list" v-loading="listLoading" element-loading-text="给我一点时间" border fit
                  highlight-current-row style="width: 100%">

            <el-table-column align="center" label="产品ID" >
                <template scope="scope">
                    <span>{{scope.row.id}}</span>
                </template>
            </el-table-column>

            <el-table-column  align="center" label="产品名称">
                <template scope="scope">
                    <span>{{scope.row.timestamp | parseTime('{y}-{m}-{d} {h}:{i}')}}</span>
                </template>
            </el-table-column>

            <el-table-column  label="产品英文名称">
                <template scope="scope">
                    <span class="link-type" @click="handleUpdate(scope.row)">{{scope.row.title}}</span>
                    <el-tag>{{scope.row.type | typeFilter}}</el-tag>
                </template>
            </el-table-column>

            <el-table-column  align="center" label="来源">
                <template scope="scope">
                    <span>{{scope.row.author}}</span>
                </template>
            </el-table-column>

            <el-table-column  v-if='showAuditor' align="center" label="主分类">
                <template scope="scope">
                    <span style='color:red;'>{{scope.row.auditor}}</span>
                </template>
            </el-table-column>

            <el-table-column  label="分子式">
                <template scope="scope">
                    <icon-svg v-for="n in +scope.row.importance" icon-class="star" class="meta-item__icon"
                              :key="n"></icon-svg>
                </template>
            </el-table-column>

            <el-table-column align="center" label="状态" >
                <template scope="scope">
                    <span class="link-type" @click='handleFetchPv(scope.row.pageviews)'>{{scope.row.pageviews}}</span>
                </template>
            </el-table-column>

            <el-table-column class-name="status-col" label="CAS号" >
                <template scope="scope">
                    <el-tag :type="scope.row.status | statusFilter">{{scope.row.status}}</el-tag>
                </template>
            </el-table-column>

            <el-table-column align="center" label="产品编码" >
                <template scope="scope">
                    <span>{{scope.row.id}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="中文别名" >
                <template scope="scope">
                    <span>{{scope.row.id}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="英文别名" >
                <template scope="scope">
                    <span>{{scope.row.id}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="产品分类" >
                <template scope="scope">
                    <span>{{scope.row.id}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="产品父分类" >
                <template scope="scope">
                    <span>{{scope.row.id}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="排序" >
                <template scope="scope">
                    <span>{{scope.row.id}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="创建时间" >
                <template scope="scope">
                    <span>{{scope.row.id}}</span>
                </template>
            </el-table-column>


        </el-table>

        <div v-show="!listLoading" class="pagination-container">
            <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange"
                           :current-page.sync="listQuery.page"
                           :page-sizes="[10,20,30, 50]" :page-size="listQuery.limit"
                           layout="total, sizes, prev, pager, next, jumper" :total="total">
            </el-pagination>
        </div>

    </div>
</template>

<script>

    export default {
        name: 'elastic',

        data() {
            return {
                list: null,
                total: null,
                listLoading: true,
                listQuery: {
                    page: 1,
                    limit: 20,
                    id: undefined,
                    product_name: undefined,
                    cas_no: undefined,
                    status: undefined,
                    source: undefined,
                    product_parent_category: undefined,
                    create_time: undefined
                },

                statusOptions: {1: '有效', 2: '无效'},
                tableKey: 0,
                pickerOptions2: {
                    shortcuts: [{
                        text: '最近一周',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近一个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近三个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        }
                    }]
                },
                create_time: this.create_time
            }
        },
        filters: {
            statusFilter(status) {
                const statusMap = {
                    published: 'success',
                    draft: 'gray',
                    deleted: 'danger'
                }
                return statusMap[status]
            },
            typeFilter(type) {
                return calendarTypeKeyValue[type]
            }
        },
        created() {
            this.$http.get('api/search',{params: this.listQuery}).then((response)=>{
                console.log(response);
                this.list = response.data.items
                this.total = response.data.total
                this.listLoading = false
            })
        },
        methods: {
            handleFilter() {
                this.listQuery.page = 1
                this.getList()
            },
            handleSizeChange(val) {
                this.listQuery.limit = val
                this.getList()
            },
            handleCurrentChange(val) {
                this.listQuery.page = val
                this.getList()
            },
            timeFilter(time) {
                if (!time[0]) {
                    this.listQuery.start = undefined
                    this.listQuery.end = undefined
                    return
                }
                this.listQuery.start = parseInt(+time[0] / 1000)
                this.listQuery.end = parseInt((+time[1] + 3600 * 1000 * 24) / 1000)
            },




        }
    }
</script>
