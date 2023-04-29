import './coursecomponent.js';

var templatedata = document.getElementById("templatedata");
var json = JSON.parse(templatedata.getAttribute("json"));

Vue.component('main-component', {
    props: ['select'],
    data: function() {
        return {
            nav: this.select,
            content: json
        };
    },
    computed: {
        courseSelected: function() {
            return this.nav == 1;
        },
        modulesSelected: function() {
            return this.nav == 2;
        },
        sectionsSelected: function() {
            return this.nav == 3;
        },
        lessonsSelected: function() {
            return this.nav == 4;
        }
    },
    template:
        '<div>'+
        '<ul class="nav nav-tabs">\n' +
        '  <li class="nav-item">\n' +
        '    <a @click="nav = 1" class="nav-link" :class="{active: courseSelected}" aria-current="page" href="#">Curso</a>\n' +
        '  </li>\n' +
        '  <li class="nav-item">\n' +
        '    <a @click="nav = 2" class="nav-link" :class="{active: modulesSelected}" href="#">Modulos</a>\n' +
        '  </li>\n' +
        '  <li class="nav-item">\n' +
        '    <a @click="nav = 3" class="nav-link" :class="{active: sectionsSelected}" href="#">Secciones</a>\n' +
        '  </li>\n' +
        '  <li class="nav-item">\n' +
        '    <a @click="nav = 4" class="nav-link" :class="{active: lessonsSelected}" href="#">Clases</a>\n' +
        '  </li>\n' +
        '</ul>' +
        '<div class="p-4">' +
        '<transition name="fade">' +
        '   <course-component v-show="nav == 1" :students="content.students"></course-component>' +
        '</transition>' +
        '</div>'+
        '</div>'
})