import swal from 'sweetalert'
import axios from 'axios'
import flatpickr from 'flatpickr'

window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.swal = swal
window.flatpickr = flatpickr
