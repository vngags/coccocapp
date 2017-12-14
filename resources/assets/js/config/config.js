export const getHeader = function() {
    const token_data = JSON.parse(window.localStorage.getItem('authUser'))
    const headers = {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + token_data.access_token
    }
    return headers;
}
