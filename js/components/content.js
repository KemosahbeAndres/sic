

Vue.component('main-content', {
    props: ['tab'],
    data: function () {
        return {
            nav: this.tab,
            content: json
        };
    },
    template: ''

});