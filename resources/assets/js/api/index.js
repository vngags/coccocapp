import axios from 'axios'
// import {clientId, clientSecret} from '../../config/env'
// import { getHeader } from '../../config/config'

export function post(url, data) {
   return axios({
      method: 'POST',
      url: url,
      data: data,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   })
}

export function get(url) {
   return axios({
      method: 'GET',
      url: url,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   })
}
