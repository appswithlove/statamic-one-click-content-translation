export function translateMeRequest(payload) {
  const cpRoot = Statamic.$config.get('cpRoot') || '/cp'
  const urlTranslate = cpRoot + '/one-click-content-translation'

  return Statamic.$axios.post(urlTranslate, payload)
    .then(response => {
      Statamic.$toast.success(__('Done'));
      return response
    })
    .catch((error) => {
      Statamic.$toast.error(error?.response?.data.message || error.message);
      return { data: payload };
    })
}
