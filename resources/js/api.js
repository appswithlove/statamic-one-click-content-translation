const URL_TRANSLATE = '/cp/one-click-content-translation';

export function translateMeRequest(payload) {
  return Statamic.$axios.post(URL_TRANSLATE, payload)
    .then(response => {
      Statamic.$toast.success(__('Done'));
      return response
    })
    .catch((error) => {
      Statamic.$toast.error(error?.response?.data.message || error.message);
      return { data: payload };
    })
}
