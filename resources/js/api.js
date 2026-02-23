import axios from 'axios'

export async function translateMeRequest (payload) {
  const toast = Statamic.$toast;
  const config = Statamic.$config;
  const cpRoot = config?.get('cpRoot') || '/cp';
  const urlTranslate = `${cpRoot}/one-click-content-translation`;
  try {
    const response = await axios.post(urlTranslate, payload);
    toast?.success?.(__('Done'));
    return response;
  } catch (error) {
    toast?.error?.(error?.response?.data?.message || error.message);
    return { data: payload };
  }
}
