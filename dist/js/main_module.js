import {login} from './module/user.js'
import {requests,requestDelete} from './module/request.js'
window.loginFunc = login
window.request = requests;
window.deleteDocument = requestDelete;