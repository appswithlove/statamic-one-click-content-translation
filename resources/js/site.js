let current = { site: null, owner: null };

export function getCurrentSite() {
  return current.site;
}

export function setCurrentSite(site, owner) {
  current = { site, owner };
}

export function clearCurrentSite(owner) {
  if (current.owner === owner) current = { site: null, owner: null };
}

export function isTranslationNeeded(site) {
  const defaultSite = Statamic.$config.get('oneClickContentTranslation')?.defaultSite;
  return !!site && site !== defaultSite;
}
