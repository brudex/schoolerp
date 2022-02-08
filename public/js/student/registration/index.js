(window.webpackJsonp=window.webpackJsonp||[]).push([[343],{wrVt:function(t,e,s){"use strict";s.r(e);var i={components:{registrationForm:s("qJaT").a},data:function(){return{registrations:{total:0,data:[]},filter:{sort_by:"created_at",order:"desc",course_id:[],previous_institute_id:[],status:null,registration_type:null,date_of_registration_start_date:"",date_of_registration_end_date:"",page_length:helper.getConfig("page_length")},orderByOptions:[{value:"created_at",translation:i18n.general.created_at},{value:"date_of_registration",translation:i18n.student.date_of_registration}],statuses:[{text:i18n.student.registration_status_pending,value:"pending"},{text:i18n.student.registration_status_rejected,value:"rejected"},{text:i18n.student.registration_status_allotted,value:"allotted"}],courses:[],registration_types:[],selected_courses:null,previous_institutes:[],selected_institutes:null,showCreatePanel:!1,showFilterPanel:!1,help_topic:""}},mounted:function(){helper.hasPermission("list-registration")||helper.hasPermission("new-registration")||(helper.notAccessibleMsg(),this.$router.push("/dashboard")),helper.hasPermission("list-registration")&&this.getRegistrations(),helper.showDemoNotification(["student"])},methods:{hasPermission:function(t){return helper.hasPermission(t)},getConfig:function(t){return helper.getConfig(t)},getRegistrations:function(t){var e=this,s=this.$loading.show();"number"!=typeof t&&(t=1),this.filter.date_of_registration_start_date=helper.toDate(this.filter.date_of_registration_start_date),this.filter.date_of_registration_end_date=helper.toDate(this.filter.date_of_registration_end_date);var i=helper.getFilterURL(this.filter);axios.get("/api/registration?page="+t+i).then((function(t){e.registrations=t.registrations,e.courses=t.filters.courses,e.previous_institutes=t.filters.previous_institutes,e.registration_types=t.filters.registration_types,s.hide()})).catch((function(t){s.hide(),helper.showErrorMsg(t)}))},getStudentName:function(t){return helper.getStudentName(t)},formatCurrency:function(t){return helper.formatCurrency(t)},print:function(){var t=this.$loading.show();axios.post("/api/registration/print",{filter:this.filter}).then((function(e){var s=window.open("/print");t.hide(),s.document.write(e)})).catch((function(e){t.hide(),helper.showErrorMsg(e)}))},pdf:function(){var t=this,e=this.$loading.show();axios.post("/api/registration/pdf",{filter:this.filter}).then((function(s){e.hide(),window.open("/download/report/"+s+"?token="+t.authToken)})).catch((function(t){e.hide(),helper.showErrorMsg(t)}))},onCourseSelect:function(t){this.filter.course_id.push(t.id)},onCourseRemove:function(t){this.filter.course_id.splice(this.filter.course_id.indexOf(t.id),1)},onPreviousInstituteSelect:function(t){this.filter.previous_institute_id.push(t.id)},onPreviousInstituteRemove:function(t){this.filter.previous_institute_id.splice(this.filter.previous_institute_id.indexOf(t.id),1)},getRegistrationStatus:function(t){return helper.getRegistrationStatus(t)},confirmDelete:function(t){var e=this;return function(s){return e.deleteRegistration(t)}},deleteRegistration:function(t){var e=this,s=this.$loading.show();axios.delete("/api/registration/"+t.id).then((function(t){toastr.success(t.message),e.getRegistrations(),s.hide()})).catch((function(t){s.hide(),helper.showErrorMsg(t)}))}},computed:{getSession:function(){return helper.getDefaultAcademicSession().name},authToken:function(){return helper.getAuthToken()}},filters:{moment:function(t){return helper.formatDate(t)},momentDateTime:function(t){return helper.formatDateTime(t)}},watch:{"filter.sort_by":function(t){this.getRegistrations()},"filter.order":function(t){this.getRegistrations()},"filter.page_length":function(t){this.getRegistrations()}}},n=s("KHd+"),a=Object(n.a)(i,(function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",[s("div",{staticClass:"page-titles"},[s("div",{staticClass:"row"},[s("div",{staticClass:"col-12 col-sm-6"},[s("h3",{staticClass:"text-themecolor"},[t._v(t._s(t.trans("student.registration"))+" ("+t._s(t.getSession)+")\n                    "),t.registrations.total?s("span",{staticClass:"card-subtitle d-none d-sm-inline"},[t._v(t._s(t.trans("general.total_result_found",{count:t.registrations.total,from:t.registrations.from,to:t.registrations.to})))]):s("span",{staticClass:"card-subtitle d-none d-sm-inline"},[t._v(t._s(t.trans("general.no_result_found")))])])]),t._v(" "),s("div",{staticClass:"col-12 col-sm-6"},[s("div",{staticClass:"action-buttons pull-right"},[s("button",{directives:[{name:"tooltip",rawName:"v-tooltip",value:t.trans("general.card_view"),expression:"trans('general.card_view')"}],staticClass:"btn btn-info btn-sm",on:{click:function(e){return t.$router.push("/student/registration/card-view")}}},[s("i",{staticClass:"fas fa-th"}),t._v(" "),s("span",{staticClass:"d-none d-sm-inline"},[t._v(t._s(t.trans("general.card_view")))])]),t._v(" "),t.registrations.total&&!t.showCreatePanel&&t.hasPermission("new-registration")?s("button",{directives:[{name:"tooltip",rawName:"v-tooltip",value:t.trans("general.add_new"),expression:"trans('general.add_new')"}],staticClass:"btn btn-info btn-sm",on:{click:function(e){t.showCreatePanel=!t.showCreatePanel}}},[s("i",{staticClass:"fas fa-plus"}),t._v(" "),s("span",{staticClass:"d-none d-sm-inline"},[t._v(t._s(t.trans("student.add_new_registration")))])]):t._e(),t._v(" "),t.showFilterPanel?t._e():s("button",{staticClass:"btn btn-info btn-sm",on:{click:function(e){t.showFilterPanel=!t.showFilterPanel}}},[s("i",{staticClass:"fas fa-filter"}),t._v(" "),s("span",{staticClass:"d-none d-sm-inline"},[t._v(t._s(t.trans("general.filter")))])]),t._v(" "),s("sort-by",{attrs:{"order-by-options":t.orderByOptions,"sort-by":t.filter.sort_by,order:t.filter.order},on:{updateSortBy:function(e){t.filter.sort_by=e},updateOrder:function(e){t.filter.order=e}}}),t._v(" "),s("div",{staticClass:"btn-group"},[s("button",{directives:[{name:"tooltip",rawName:"v-tooltip",value:t.trans("general.more_option"),expression:"trans('general.more_option')"}],staticClass:"btn btn-info btn-sm dropdown-toggle no-caret ",attrs:{type:"button",role:"menu",id:"moreOption","data-toggle":"dropdown","aria-haspopup":"true","aria-expanded":"false"}},[s("i",{staticClass:"fas fa-ellipsis-h"}),t._v(" "),s("span",{staticClass:"d-none d-sm-inline"})]),t._v(" "),s("div",{class:["dropdown-menu","ltr"==t.getConfig("direction")?"dropdown-menu-right":""],attrs:{"aria-labelledby":"moreOption"}},[s("button",{staticClass:"dropdown-item custom-dropdown",on:{click:t.print}},[s("i",{staticClass:"fas fa-print"}),t._v(" "+t._s(t.trans("general.print")))]),t._v(" "),s("button",{staticClass:"dropdown-item custom-dropdown",on:{click:t.pdf}},[s("i",{staticClass:"fas fa-file-pdf"}),t._v(" "+t._s(t.trans("general.generate_pdf")))])])]),t._v(" "),s("help-button",{on:{clicked:function(e){t.help_topic="student-registration"}}})],1)])])]),t._v(" "),s("div",{staticClass:"container-fluid"},[s("transition",{attrs:{name:"fade"}},[t.showFilterPanel?s("div",{staticClass:"card card-form"},[s("div",{staticClass:"card-body"},[s("h4",{staticClass:"card-title"},[t._v(t._s(t.trans("general.filter")))]),t._v(" "),s("div",{staticClass:"row"},[s("div",{staticClass:"col-12 col-sm-2"},[s("div",{staticClass:"form-group"},[s("label",{attrs:{for:""}},[t._v(t._s(t.trans("academic.course")))]),t._v(" "),s("v-select",{attrs:{label:"name","track-by":"id","group-values":"courses","group-label":"course_group","group-select":!1,name:"course_id",id:"course_id",options:t.courses,placeholder:t.trans("academic.select_course"),multiple:!0,"close-on-select":!1,"clear-on-select":!1,"hide-selected":!0,selected:t.selected_courses},on:{select:t.onCourseSelect,remove:t.onCourseRemove},model:{value:t.selected_courses,callback:function(e){t.selected_courses=e},expression:"selected_courses"}},[t.courses.length?t._e():s("div",{staticClass:"multiselect__option",attrs:{slot:"afterList"},slot:"afterList"},[t._v("\n                                        "+t._s(t.trans("general.no_option_found"))+"\n                                    ")])])],1)]),t._v(" "),s("div",{staticClass:"col-12 col-sm-2"},[s("div",{staticClass:"form-group"},[s("label",{attrs:{for:""}},[t._v(t._s(t.trans("student.previous_institute")))]),t._v(" "),s("v-select",{attrs:{label:"name","track-by":"id",name:"previous_institute_id",id:"previous_institute_id",options:t.previous_institutes,placeholder:t.trans("academic.select_institute"),multiple:!0,"close-on-select":!1,"clear-on-select":!1,"hide-selected":!0,selected:t.selected_institutes},on:{select:t.onPreviousInstituteSelect,remove:t.onPreviousInstituteRemove},model:{value:t.selected_institutes,callback:function(e){t.selected_institutes=e},expression:"selected_institutes"}},[t.previous_institutes.length?t._e():s("div",{staticClass:"multiselect__option",attrs:{slot:"afterList"},slot:"afterList"},[t._v("\n                                        "+t._s(t.trans("general.no_option_found"))+"\n                                    ")])])],1)]),t._v(" "),s("div",{staticClass:"col-12 col-sm-2"},[s("div",{staticClass:"form-group"},[s("label",{attrs:{for:""}},[t._v(t._s(t.trans("student.registration_status")))]),t._v(" "),s("select",{directives:[{name:"model",rawName:"v-model",value:t.filter.status,expression:"filter.status"}],staticClass:"custom-select col-12",on:{change:function(e){var s=Array.prototype.filter.call(e.target.options,(function(t){return t.selected})).map((function(t){return"_value"in t?t._value:t.value}));t.$set(t.filter,"status",e.target.multiple?s:s[0])}}},[s("option",{attrs:{value:"null",selected:""}},[t._v(t._s(t.trans("general.select_one")))]),t._v(" "),t._l(t.statuses,(function(e){return s("option",{domProps:{value:e.value}},[t._v("\n                                    "+t._s(e.text)+"\n                                  ")])}))],2)])]),t._v(" "),s("div",{staticClass:"col-12 col-sm-2"},[s("div",{staticClass:"form-group"},[s("label",{attrs:{for:""}},[t._v(t._s(t.trans("student.registration_type")))]),t._v(" "),s("select",{directives:[{name:"model",rawName:"v-model",value:t.filter.registration_type,expression:"filter.registration_type"}],staticClass:"custom-select col-12",on:{change:function(e){var s=Array.prototype.filter.call(e.target.options,(function(t){return t.selected})).map((function(t){return"_value"in t?t._value:t.value}));t.$set(t.filter,"registration_type",e.target.multiple?s:s[0])}}},[s("option",{attrs:{value:"null",selected:""}},[t._v(t._s(t.trans("general.select_one")))]),t._v(" "),t._l(t.registration_types,(function(e){return s("option",{domProps:{value:e.value}},[t._v("\n                                    "+t._s(e.text)+"\n                                  ")])}))],2)])]),t._v(" "),s("div",{staticClass:"col-12 col-sm-6"},[s("date-range-picker",{attrs:{"start-date":t.filter.date_of_registration_start_date,"end-date":t.filter.date_of_registration_end_date,label:t.trans("transport.date_of_registration_between")},on:{"update:startDate":function(e){return t.$set(t.filter,"date_of_registration_start_date",e)},"update:start-date":function(e){return t.$set(t.filter,"date_of_registration_start_date",e)},"update:endDate":function(e){return t.$set(t.filter,"date_of_registration_end_date",e)},"update:end-date":function(e){return t.$set(t.filter,"date_of_registration_end_date",e)}}})],1)]),t._v(" "),s("div",{staticClass:"card-footer text-right"},[s("button",{staticClass:"btn btn-danger",attrs:{type:"button"},on:{click:function(e){t.showFilterPanel=!1}}},[t._v(t._s(t.trans("general.cancel")))]),t._v(" "),s("button",{staticClass:"btn btn-info waves-effect waves-light",attrs:{type:"button"},on:{click:t.getRegistrations}},[t._v(t._s(t.trans("general.filter")))])])])]):t._e()]),t._v(" "),t.hasPermission("new-registration")?s("transition",{attrs:{name:"fade"}},[t.showCreatePanel?s("div",{staticClass:"card card-form"},[s("div",{staticClass:"card-body"},[s("h4",{staticClass:"card-title"},[t._v(t._s(t.trans("student.add_new_registration")))]),t._v(" "),s("registration-form",{on:{completed:t.getRegistrations,cancel:function(e){t.showCreatePanel=!t.showCreatePanel}}})],1)]):t._e()]):t._e(),t._v(" "),t.hasPermission("list-registration")?s("div",{staticClass:"card"},[s("div",{staticClass:"card-body"},[t.registrations.total?s("div",{staticClass:"table-responsive"},[s("table",{staticClass:"table table-sm"},[s("thead",[s("tr",[s("th",[t._v(t._s(t.trans("student.name")))]),t._v(" "),s("th",[t._v(t._s(t.trans("student.first_guardian_name")))]),t._v(" "),s("th",[t._v(t._s(t.trans("student.date_of_birth")))]),t._v(" "),s("th",[t._v(t._s(t.trans("student.contact_number")))]),t._v(" "),s("th",[t._v(t._s(t.trans("academic.course")))]),t._v(" "),s("th",[t._v(t._s(t.trans("student.registration_status")))]),t._v(" "),s("th",[t._v(t._s(t.trans("student.date_of_registration")))]),t._v(" "),s("th",[t._v(t._s(t.trans("student.registration_fee")))]),t._v(" "),s("th",{staticClass:"table-option"},[t._v(t._s(t.trans("general.action")))])])]),t._v(" "),s("tbody",t._l(t.registrations.data,(function(e){return s("tr",[s("td",[t._v("\n                                    "+t._s(t.getStudentName(e.student))+"\n                                    "),e.is_online?s("span",[s("span",{staticClass:"label label-info"},[t._v(t._s(t.trans("student.online_registration")))])]):t._e()]),t._v(" "),s("td",{domProps:{textContent:t._s(e.student.parent?e.student.parent.first_guardian_name:"")}}),t._v(" "),s("td",[t._v(t._s(t._f("moment")(e.student.date_of_birth)))]),t._v(" "),s("td",{domProps:{textContent:t._s(e.student.contact_number)}}),t._v(" "),s("td",{domProps:{textContent:t._s(e.course.name)}}),t._v(" "),s("td",t._l(t.getRegistrationStatus(e),(function(e){return s("span",{class:["label","label-"+e.color,"m-r-5"]},[t._v(t._s(e.label))])})),0),t._v(" "),s("td",[t._v(t._s(t._f("moment")(e.date_of_registration)))]),t._v(" "),s("td",[e.registration_fee?s("span",[t._v("\n                                        "+t._s(t.formatCurrency(e.registration_fee))+"\n                                        "),"paid"==e.registration_fee_status?s("span",{staticClass:"label label-success"},[t._v(t._s(t.trans("student.registration_fee_status_paid")))]):s("span",{staticClass:"label label-danger"},[t._v(t._s(t.trans("student.registration_fee_status_unpaid")))])]):s("span",[t._v("-")])]),t._v(" "),s("td",{staticClass:"table-option"},[s("div",{staticClass:"btn-group"},[s("router-link",{directives:[{name:"tooltip",rawName:"v-tooltip",value:t.trans("student.view_detail"),expression:"trans('student.view_detail')"}],staticClass:"btn btn-info btn-sm",attrs:{to:"/student/registration/"+e.id}},[s("i",{staticClass:"fas fa-arrow-circle-right"})]),t._v(" "),t.hasPermission("delete-registration")?s("button",{directives:[{name:"confirm",rawName:"v-confirm",value:{ok:t.confirmDelete(e)},expression:"{ok: confirmDelete(registration)}"},{name:"tooltip",rawName:"v-tooltip",value:t.trans("student.delete_registration"),expression:"trans('student.delete_registration')"}],key:e.id,staticClass:"btn btn-danger btn-sm"},[s("i",{staticClass:"fas fa-trash"})]):t._e()],1)])])})),0)])]):t._e(),t._v(" "),t.registrations.total?t._e():s("module-info",{attrs:{module:"student",title:"registration_module_title",description:"registration_module_description",icon:"check-circle"}},[s("div",{attrs:{slot:"btn"},slot:"btn"},[t.showCreatePanel?t._e():s("button",{staticClass:"btn btn-info btn-md",on:{click:function(e){t.showCreatePanel=!t.showCreatePanel}}},[s("i",{staticClass:"fas fa-plus"}),t._v(" "+t._s(t.trans("general.add_new")))])])]),t._v(" "),s("pagination-record",{attrs:{"page-length":t.filter.page_length,records:t.registrations},on:{"update:pageLength":function(e){return t.$set(t.filter,"page_length",e)},"update:page-length":function(e){return t.$set(t.filter,"page_length",e)},updateRecords:t.getRegistrations}})],1)]):t._e()],1),t._v(" "),s("right-panel",{attrs:{topic:t.help_topic}})],1)}),[],!1,null,null,null);e.default=a.exports}}]);
//# sourceMappingURL=index.js.map?id=4ab087c0e5c7e1c5d897