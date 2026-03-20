export async function translateMeRequest(payload) {
  const toast = Statamic.$toast;
  const config = Statamic.$config;
  const cpRoot = config?.get('cpRoot') || '/cp';
  const urlTranslate = `${cpRoot}/one-click-content-translation`;
  try {
    const res = await fetch(urlTranslate, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": Statamic.$config.get('csrfToken')
      },
      body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (!res.ok) {
      throw new Error(data?.message || `HTTP ${res.status}`);
    }

    toast?.success?.(__('Done'));
    return { data, status: res.status };
  } catch (error) {
    toast?.error?.(error?.response?.data?.message || error.message);
    return { data: payload };
  }
}

export async function isTranslateNeedRequest(payload) {
  const cpRoot = Statamic.$config?.get('cpRoot') || '/cp';
  const url = new URL(`${cpRoot}/one-click-need-translation`, window.location.origin);

  Object.keys(payload).forEach((key) => url.searchParams.append(key, payload[key]));

  try {
    const response = await fetch(url.toString(), { method: 'GET', headers: { 'Accept': 'application/json' } });
    if (!response.ok) return null;

    const data = await response.json();
    return data.need_translation;
  } catch (error) {
    return false;
  }
}
