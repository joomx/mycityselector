import Vue from 'vue';
import MultiVue from 'vue-multivue';
import VueI18n from 'vue-i18n';
import McsBaseComponent from './components/McsBaseComponent.vue';
import translations from './translations';

const _html = document.getElementsByTagName('html');
let _language = 'ru';
if (_html && _html.length > 0 && _html[0].lang) {
    _language = (_html[0].lang.split('-').length > 1) ?
        _html[0].lang.split('-')[1] : _html[0].lang;
    if (!translations[_language]) { _language = 'ru'; }
}

window.Vue = Vue;

const i18n = new VueI18n({
    locale: _language,
    messages: translations
});

const apps = new MultiVue('.mcs-app', {
    i18n,
    components: {
        McsBaseComponent
    }
});