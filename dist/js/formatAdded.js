
function formatNumber(value, maxDecimals, locale = "en-US", prefix = "", allowMinus = false) {
    if (value === null || value === undefined) return "";

    value = value.toString();

    // ======================================
    // KUNCI UTAMA:
    // Jika minus TIDAK diizinkan → HAPUS SEMUA
    // ======================================
    if (!allowMinus) {
        value = value.replace(/-/g, "");
    }

    const formatter = new Intl.NumberFormat(locale);
    const decimalSeparator = (1.1).toLocaleString(locale).substring(1, 2);

    // ===============================
    // DETEKSI MINUS DI DEPAN SAJA
    // ===============================
    let isNegative = false;
    if (allowMinus && value.startsWith("-")) {
        isNegative = true;
    }

    // buang semua kecuali angka & decimal
    let regex = new RegExp(`[^0-9${decimalSeparator}]`, "g");
    let clean = value.replace(regex, "");

    // locale decimal → "."
    if (decimalSeparator !== ".") {
        clean = clean.replace(decimalSeparator, ".");
    }

    // hilangkan titik lebih dari satu
    let firstDot = clean.indexOf(".");
    if (firstDot !== -1) {
        clean =
            clean.substring(0, firstDot + 1) +
            clean.substring(firstDot + 1).replace(/\./g, "");
    }

    // ===============================
    // KASUS USER BARU KETIK "-"
    // ===============================
    if (allowMinus && isNegative && clean === "") {
        return prefix + "-";
    }

    // user ketik "."
    if (clean === ".") {
        return prefix + (isNegative ? "-" : "") + "0" + decimalSeparator;
    }

    // user ketik "12."
    if (clean.endsWith(".")) {
        let intPart = clean.slice(0, -1) || "0";
        let formatted = formatter.format(parseInt(intPart));
        return prefix + (isNegative ? "-" : "") + formatted + decimalSeparator;
    }

    let numberValue = parseFloat(clean);
    if (isNaN(numberValue)) return prefix;

    let parts = clean.split(".");
    let intPart = formatter.format(parseInt(parts[0] || 0));

    if (parts[1] !== undefined) {
        let decPart = parts[1].substring(0, maxDecimals);
        return prefix + (isNegative ? "-" : "") + intPart + decimalSeparator + decPart;
    }

    return prefix + (isNegative ? "-" : "") + intPart;
}



// ===============================================================
//             BIND INPUT FORMAT ANGKA
// ===============================================================
function bindFormatAngka(context = document) {

    context.querySelectorAll(".formatAngka").forEach(input => {

        const allowMinus = input.dataset.allowMinus === "true";
        const maxDecimals = parseInt(input.dataset.decimal || 2);
        const locale = input.dataset.locale || "en-US";
        const prefix = input.dataset.prefix && input.dataset.prefix !== "false"
            ? input.dataset.prefix + " "
            : "";

        // format awal jika sudah ada value
        if (input.value) {
            input.value = formatNumber(input.value, maxDecimals, locale, prefix, allowMinus);

            // 🔥 khusus input readonly → langsung paksa tampil decimal lengkap
            if (input.readOnly) {
                let num = parseFloat(unformatNumber(input.value));
                if (!isNaN(num)) {
                    input.value = formatNumber(num.toFixed(maxDecimals), maxDecimals, locale, prefix, allowMinus);
                }
            }
        }

        // realtime format saat mengetik
        input.addEventListener("input", function () {
            let start = this.selectionStart;
            let oldLength = this.value.length;

            this.value = formatNumber(this.value, maxDecimals, locale, prefix, allowMinus);

            let newLength = this.value.length;
            this.setSelectionRange(start + (newLength - oldLength), start + (newLength - oldLength));
        });

        // jika paste angka
        input.addEventListener("paste", function (e) {
            e.preventDefault();
            let pasted = (e.clipboardData || window.clipboardData).getData("text");
            this.value = formatNumber(pasted, maxDecimals, locale, prefix, allowMinus);
        });

        // 🔥 saat blur baru dipaksa 2 decimal
        input.addEventListener("blur", function () {

            let raw = unformatNumber(this.value);
            let num = parseFloat(raw);

            if (isNaN(num)) return;

            this.value = parseFloat(num).toFixed(maxDecimals); // paksa trailing zero
            this.value = formatNumber(this.value, maxDecimals, locale, prefix, allowMinus);
        });
    });
}

// ===============================================================
document.addEventListener("DOMContentLoaded", () => bindFormatAngka());
document.addEventListener("shown.bs.modal", e => bindFormatAngka(e.target));
// ===============================================================

function unformatNumber(value) {
    if (!value) return "";
    return value.replace(/[^0-9.-]/g, "");
}
