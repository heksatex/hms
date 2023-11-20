import {login} from './module/user.js';
import {requests, requestDelete, ConfirmRequest} from './module/request.js';
import {searchArray, changeCondition} from './module/helper.js';
window.loginFunc = login;
window.request = requests;
window.deleteDocument = requestDelete;
window.searchArray = searchArray;
window.changeCondition = changeCondition;
window.confirmRequest = ConfirmRequest;