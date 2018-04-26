<template>
    <div class="dv">
        <div class="dv-body">
            <table class="dv-table table table-condensed">
                <thead>
                <tr>
                    <th v-for="column in labels">
                    <span>{{column}}</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in model.data">
                    <td v-for="(k,value) in row">{{value}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    props: {
        source: {
            type: String
        },
        title: {
            type: String
        },
        params: {
            type: Object
        }
    },
    data() {
        return {
            model: {},  //儲存api 回傳的data ;type = json
            labels: ['團號','活動日期','已參加人數'],
            fields: [
                {key: 'gp_activity_id'},
                {key: 'start_date'},
                {key: 'applicants',formatter: 'NumOfApplicants'}
            ]
        }
    },
    created() {
        this.fetchIndexData()
    },
    methods: {
        formatter() {
          return {
              NumOfApplicants(ApplicantsArr) {
                  return ApplicantsArr.length;
              }
          }
        },
        fetchIndexData() {
            var vm = this;

            function getTrColumns(data) {
                var results = [];
                data.forEach(function (val) {
                    var result = [];
                    vm.fields.forEach(function (field) {
                        if(val[field.key] == undefined)return;

                        var resData = val[field.key];
                        if(field.formatter != undefined){
                            resData = vm.formatter()[field.formatter](val[field.key]);
                        }
                        result.push(resData);
                    });
                    results.push(result);
                });
                return results;
            };
            axios.post(this.source,this.params)
                .then(function(response) {
                    Vue.set(vm.$data, 'model',{data:getTrColumns(response.data.data)} )
                })
                .catch(function(response) {
                    //console.log(response)
                })
        }
    }
}
</script>