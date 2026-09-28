const SITE_MARKER = '[data-one-click-site]';

function baseSite() {
  return [...document.querySelectorAll(SITE_MARKER)]
    .find(marker => !marker.closest('.stack'))
    ?.dataset.oneClickSite;
}

export function siteFor(el) {
  const stackSite = el.closest('.stack')?.querySelector(SITE_MARKER)?.dataset.oneClickSite;
  return stackSite || baseSite() || null;
}

export function isTranslationNeeded(site) {
  const defaultSite = Statamic.$config.get('oneClickContentTranslation')?.defaultSite;
  return !!site && site !== defaultSite;
}
