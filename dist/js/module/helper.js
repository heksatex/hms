const searchArray = function (array, searchOnKey = null, valCondition) {

    return new Promise((resolve, reject) => {
        let data = [];
        $.each(array, function (idx, val) {
            if (searchOnKey === null) {
                if (val === valCondition) {
                    data = [1];
                    return;
                }
            } else {
                if (val[searchOnKey] === valCondition) {
                    data = val;
                    return;
                }

            }
        });
        resolve(data);
    });
};

const changeCondition = function (value, check, changed, addTrimVal = false) {
    if (addTrimVal) {
        value = value.trim();
    }
    if (value === check) {
        return changed;
    }
    return value;
};

const checkIsFuncExist = function (func) {
    if (typeof func === "function") {
        return true;
    }
    return false;
};

var statusInput = 0;
var input = "";
const checkScannerInput = function (e, tanda = "*", listVal = {}) {

    if (e.key === "Backspace" || e.key === "]") {
        statusInput = 0;
        input = "";
        return;
    }
    if (e.key === tanda)
        statusInput++;


    switch (statusInput) {
        case 1:
            input += e.key;
            break;
        case 2:
            input += e.key;
            if (checkIsFuncExist(listVal[input])) {
                listVal[input]();
            } else {
                statusInput = 0;
                input = "";
            }

//            searchArray(listVal, null, input).then(dt => {
//                if (dt.length > 0) {
//                    callback();
//                }
//            });
            statusInput = 0;
            input = "";
            break;
        default:
            statusInput = 0;
            input = "";
            break;
}

};

export {searchArray, changeCondition, checkScannerInput}