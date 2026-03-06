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
      throw { response: { data } };
    }

    toast?.success?.(__('Done'));
    return { data, status: res.status };
  } catch (error) {
    toast?.error?.(error?.response?.data?.message || error.message);
    return { data: payload };
  }
}
