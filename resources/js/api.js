const URL_TRANSLATE = '/cp/translate-me';

export function translateMeRequest(payload) {
  return Statamic.$axios.post(URL_TRANSLATE, payload)
    .then(response => {
      Statamic.$toast.success(__('Done'));
      return response
    })
    .catch((error) => {
      Statamic.$toast.error(error?.response?.data || error.message);
      return { data: payload };
    })
}
