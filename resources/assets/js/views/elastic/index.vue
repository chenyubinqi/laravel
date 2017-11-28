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
                      v-model="listQuery.product_category">
            </el-input>

            <el-date-picker
                    v-model="create_time"
                    type="datetimerange"
                    :picker-options="pickerOptions2"
                    placeholder="选择时间范围"
                    @change="handleTimeChange"
                    align="right">
            </el-date-picker>

            <el-button class="filter-item" type="primary" icon="search" @click="handleFilter">搜索</el-button>
        </div>

        <el-table :key='tableKey' :data="list" v-loading="listLoading" element-loading-text="给我一点时间" border fit
                  highlight-current-row style="width: 100%">

            <el-table-column align="center" label="产品ID" min-width="5%">
                <template scope="scope">
                    <span>{{scope.row._id}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="产品名称" min-width="5%">
                <template scope="scope">
                    <span>{{scope.row.product_name}}</span>
                </template>
            </el-table-column>

            <el-table-column label="产品英文名称" min-width="5%">
                <template scope="scope">
                    <span>{{scope.row.product_name_en}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="来源" min-width="3%">
                <template scope="scope">
                    <span>{{scope.row.source}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="主分类" min-width="3%">
                <template scope="scope">
                    <span>{{scope.row.main_category}}</span>
                </template>
            </el-table-column>

            <el-table-column label="分子式" min-width="3%">
                <template scope="scope">
                    <span>{{scope.row.formula}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="状态" min-width="5%">
                <template scope="scope">
                    <span>{{statusOptions[scope.row.status]}}</span>
                </template>
            </el-table-column>

            <el-table-column class-name="status-col" label="CAS号" min-width="5%">
                <template scope="scope">
                    <span>{{scope.row.cas_no}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="产品编码" min-width="5%">
                <template scope="scope">
                    <span>{{scope.row.product_code}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="中文别名" min-width="10%">
                <template scope="scope">
                    <span>{{scope.row.zh_synonyms}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="英文别名" min-width="10%">
                <template scope="scope">
                    <span>{{scope.row.en_synonyms}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="产品分类" min-width="5%">
                <template scope="scope">
                    <span>{{scope.row.product_category | categoryFilter}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="排序" min-width="3%">
                <template scope="scope">
                    <span>{{scope.row.sort}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="创建时间" min-width="5%">
                <template scope="scope">
                    <span>{{scope.row.create_time}}</span>
                </template>
            </el-table-column>


        </el-table>

        <div v-show="!listLoading" class="pagination-container">
            <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange"
                           :current-page.sync="listQuery.page"
                           :page-sizes="[10,20,30, 50]" :page-size="listQuery.page_size"
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
                    page_size: 10,
                    id: undefined,
                    product_name: undefined,
                    cas_no: undefined,
                    status: undefined,
                    source: undefined,
                    product_category: undefined
                },
                create_time: [new Date().getTime() - 3600 * 1000 * 24 * 30, new Date()],
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
                }
            }
        },

        computed: {
        },
        filters: {
            categoryFilter(type) {
                return type.join(',')
            }
        },
        created() {
            this.getList()
        },
        methods: {
            getList() {
                this.listLoading = true
                this.$http.get('api/search', {params: this.listQuery}).then((response) => {
                    console.log(response.body);
                    this.list = response.body.data
                    this.total = response.body.total
                    this.listLoading = false
                })
            },
            handleFilter() {
                this.listQuery.page = 1
                this.getList()
            },
            handleSizeChange(val) {
                this.listQuery.page_size = val
                this.getList()
            },
            handleCurrentChange(val) {
                this.listQuery.page = val
                this.getList()
            },
            handleTimeChange(val){
                console.log(val);
                this.listQuery.start = undefined
                this.listQuery.end = undefined
                if (val) {
                    const time = val.split(' - ', 2);
                    this.listQuery.start = time[0].trim();
                    this.listQuery.end = time[1].trim();
                }
            }

        }
    }
</script>
