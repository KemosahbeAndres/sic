Vue.component('course-component', {
    props: ['students'],
    template: '' +
        '<div>' +
        '<h3>Gestor Curso</h3>\n' +
        '        <table class="table table-striped table-hover">\n' +
        '            <thead>\n' +
        '                <tr>\n' +
        '                    <th>ID</th>\n' +
        '                    <th>Nombre</th>\n' +
        '                </tr>\n' +
        '            </thead>\n' +
        '            <tbody>\n' +
        '                <tr>\n' +
        '                    <td></td>\n' +
        '                    <td></td>\n' +
        '                </tr>\n' +
        '            </tbody>\n' +
        '        </table>\n' +
        '        <h3>Profesores</h3>\n' +
        '        <table class="table table-striped table-hover">\n' +
        '            <thead>\n' +
        '                <tr>\n' +
        '                    <th>ID</th>\n' +
        '                    <th>Nombre</th>\n' +
        '                    <th>Rut</th>\n' +
        '                </tr>\n' +
        '            </thead>\n' +
        '            <tbody>\n' +
        '                <tr>\n' +
        '                    <td></td>\n' +
        '                    <td></td>\n' +
        '                    <td></td>\n' +
        '                </tr>\n' +
        '            </tbody>\n' +
        '        </table>' +
        '        <h3>Alumnos</h3>\n' +
        '        <table class="table table-striped table-hover">\n' +
        '            <thead>\n' +
        '                <tr>\n' +
        '                    <th>ID</th>\n' +
        '                    <th>Nombre</th>\n' +
        '                    <th>Rut</th>\n' +
        '                    <th>Avance</th>\n' +
        '                    <th>Tiempo Conexion</th>\n' +
        '                    <th>Estado</th>\n' +
        '                </tr>\n' +
        '            </thead>\n' +
        '            <tbody>\n' +
        '                <tr v-for="student in students" :key="student.id">\n' +
        '                    <td>{{student.id}}</td>\n' +
        '                    <td>{{student.nombre}}</td>\n' +
        '                    <td>{{student.rut}}</td>\n' +
        '                    <td>{{student.avance}}</td>\n' +
        '                    <td>{{student.tiempo}}</td>\n' +
        '                    <td>{{student.estado}}</td>\n' +
        '                </tr>\n' +
        '            </tbody>\n' +
        '        </table>' +
        '</div>'
})