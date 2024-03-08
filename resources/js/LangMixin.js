export default {

  computed: {
    sites() {
      return Statamic.$config.get('sites') || [];
    },
    enabled() {
      return this.sites && this.sites.length > 1;
    },
    disabled() {
      return false;
    },
    currentLangHandle() {
      return this.$store.state?.publish?.base?.site;
    },
    currentLang() {
      const lang = this.sites.find(lang => lang.handle === this.currentLangHandle);
      return lang || this.sites[0];
    },
    titleCurentLang() {
      return this.currentLang?.name;
    },
    tooltipText() {
      return 'Translate';
    },
    defaultLang() {
      return this.sites.find(lang => lang.handle === 'default') || this.sites[0];
    },
  },

}
