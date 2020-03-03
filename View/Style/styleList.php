<extend name="../../Admin/View/Common/element_layout"/>

<block name="content">
    <div id="app" style="padding: 8px;" v-cloak>
        <el-card>
            <div class="filter-container">

                <el-input size="small" v-model="listQuery.title" placeholder="标题" style="width: 200px;" class="filter-item"></el-input>

                <el-button @click="doSearch" size="small" type="primary" icon="el-icon-search">
                    搜索
                </el-button>

                <el-button class="filter-item" style="margin-left: 10px;" size="small" type="primary" @click="getAddEditGoods(0)">
                    添加
                </el-button>
            </div>

            <el-table
                :key="tableKey"
                :data="list"
                border
                fit
                highlight-current-row
                style="width: 100%;"
            >
                <el-table-column label="ID" align="center">
                    <template slot-scope="{row}">
                        <span>{{ row.id }}</span>
                    </template>
                </el-table-column>

                <el-table-column label="标题" align="center">
                    <template slot-scope="{row}">
                        <span>{{ row.title }}</span>
                    </template>
                </el-table-column>

                <el-table-column label="是否开启" align="center">
                    <template slot-scope="{row}">
                        <span v-if="row.watermarkenable == '1'">已开启</span>
                        <span v-if="row.watermarkenable == '0'">已关闭</span>
                    </template>
                </el-table-column>

                <el-table-column label="排序" width="150px" align="center">
                    <template slot-scope="{row}">
                        {{ row.listorder }}
                        <i @click="updateSort(row.id, row.listorder)" class="el-icon-edit update-sort"></i>
                    </template>
                </el-table-column>

                <el-table-column label="管理" align="center" width="230" class-name="small-padding fixed-width">
                    <template slot-scope="{row}">
                        <el-button type="primary" size="mini" @click="getAddEditGoods(row.id)">编辑</el-button>
                        <el-button size="mini" type="danger" @click="deleteGoods(row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div class="pagination-container">
                <el-pagination
                    background
                    layout="prev, pager, next, jumper"
                    :total="total"
                    v-show="total>0"
                    :current-page.sync="listQuery.page"
                    :page-size.sync="listQuery.limit"
                    @current-change="getList"
                >
                </el-pagination>
            </div>
        </el-card>
    </div>

    <style>
        .filter-container {
            padding-bottom: 10px;
        }
        .pagination-container {
            padding: 32px 16px;
        }
    </style>

    <script>
        $(document).ready(function () {
            new Vue({
                el: '#app',
                data: {
                    form: {},
                    tableKey: 0,
                    list: [],
                    total: 0,
                    listQuery: {
                        page: 1,
                        limit: 20
                    }
                },
                watch: {

                },
                filters: {

                },
                computed:{

                },
                methods: {
                    doSearch: function(){
                        var that = this;
                        that.listQuery.page = 1;
                        that.getList();
                    },
                    getAddEditGoods:function (id) {
                        var that = this;
                        var url = "{:U('AliyunOss/Style/styleDetails')}";
                        if(id) url += "&id="+id;
                        layer.open({
                            type: 2,
                            title: ['管理'],
                            content: url,
                            area: ['100%', '100%'],
                            end:function(){
                                that.getList();
                            }
                        })
                    },
                    getList: function() {
                        var that = this;
                        var url = '{:U("AliyunOss/Style/styleList")}';
                        var data = that.listQuery;
                        that.httpPost(url, data, function(res){
                            if(res.status){
                                that.list = res.data.items;
                                that.page = res.data.page;
                                that.total = parseInt(res.data.total_items);
                                that.page_count = res.data.total_pages;
                                that.postData = res.data.postData;
                            }else{
                                layer.msg(res.msg, {time: 1000});
                            }
                        });
                    },
                    deleteGoods: function(id) {
                        var that = this;
                        var url = '{:U("AliyunOss/Base/updateTable")}';
                        layer.confirm('您确定需要删除？', {
                            btn: ['确定','取消'] //按钮
                        }, function(){
                            var data = {
                                table: 'aliyun_oss_style', field: 'is_delete',where_name: 'id',
                                value: 1, where_value: id
                            };
                            that.httpPost(url, data, function(res){
                                if(res.status){
                                    that.getList();
                                }
                                layer.msg('操作成功', {icon: 1});
                            });
                        });
                    },
                    updateShow: function(id, value){
                        var that = this;
                        var url = '{:U("AliyunOss/Base/updateTable")}';
                        var data = {
                            table: 'aliyun_oss_style', field: 'is_display',where_name: 'id',
                            value: value, where_value: id
                        };
                        that.httpPost(url, data, function(res){
                            if(res.status){
                                that.$message.success('修改成功');
                                that.getList();
                            }
                        });
                    },
                    updateSort: function(id, sort){
                        var that = this;
                        that.$prompt('请输入排序', {
                            confirmButtonText: '保存',
                            cancelButtonText: '取消',
                            inputValue: sort,
                            roundButton: true,
                            closeOnClickModal: false,
                            beforeClose: function(action, instance, done){
                                if(action == 'confirm'){
                                    var url = '{:U("AliyunOss/Base/updateTable")}';
                                    var data = {table: 'aliyun_oss_style', field: 'listorder',where_name: 'id',
                                        value: instance.inputValue, where_value: id
                                    };
                                    that.httpPost(url, data, function(res){
                                        if(res.status){
                                            that.$message.success('修改成功');
                                            that.getList();
                                            done();
                                        }
                                    });
                                }else{
                                    done();
                                }
                            }
                        }).then(function(e){}).catch(function(){});
                    }
                },
                mounted: function () {
                    this.getList();
                }
            })
        })
    </script>
</block>
