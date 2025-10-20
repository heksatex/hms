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

const setInputFilter = function (textbox, inputFilter, errMsg) {
    ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(function (event) {

        textbox.addEventListener(event, function (e) {
            if (inputFilter(this.value)) {
                // Accepted value.
                if (["keydown", "mousedown", "focusout"].indexOf(e.type) >= 0) {
                    this.classList.remove("input-error");
                    this.setCustomValidity("");
                }

                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                // Rejected value: restore the previous one.
                this.classList.add("input-error");
                this.setCustomValidity(errMsg);
                this.reportValidity();
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                // Rejected value: nothing to restore.
                this.value = "";
            }
        });
    });
};


export {searchArray, changeCondition, checkScannerInput, setInputFilter}