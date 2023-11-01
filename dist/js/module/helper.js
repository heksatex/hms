const searchArray = function (array, searchOnKey, valCondition) {

    return new Promise((resolve, reject) => {
        let data = [];
        $.each(array, function (idx, val) {
            if (val[searchOnKey] === valCondition) {
                data = val;
                return;
            }
        });
        resolve(data);
    });
};

const changeCondition = function (value, check, changed,addTrimVal = false) {
    if(addTrimVal) {
        value = value.trim();
    }
    if (value === check) {
        return changed;
    }
    return value;
};

export {searchArray,changeCondition}