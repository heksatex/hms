function formatNumber(value, maxDecimals, locale = "en-US", prefix = "") {
    if (!value) return "";

    const formatter = new Intl.NumberFormat(locale);
    const decimalSeparator = (1.1).toLocaleString(locale).substring(1, 2);

    // hanya angka / decimal
    let regex = new RegExp(`[^0-9${decimalSeparator}-]`, "g");
    let clean = value.replace(regex, "");

    // ubah separator sesuai locale
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

    // jika user baru ketik titik (contoh "12.")
    if (clean.endsWith(".")) {
        let intPart = clean.slice(0, -1) || "0";
        return prefix + formatter.format(intPart) + decimalSeparator;
    }

    let numberValue = parseFloat(clean);
    if (isNaN(numberValue)) return prefix;

    // pisahkan decimal tanpa rounding dulu
    let parts = clean.split(".");
    let intPart = parts[0];
    let decPart = parts[1];

    intPart = formatter.format(parseInt(intPart || 0));

    // jika ada desimal → batasi jumlah sesuai maxDecimals
    if (decPart !== undefined) {
        decPart = decPart.substring(0, maxDecimals);
        return prefix + intPart + decimalSeparator + decPart;
    }

    // belum ada desimal saat input
    return prefix + intPart;
}


// ===============================================================
//             BIND INPUT FORMAT ANGKA
// ===============================================================
function bindFormatAngka(context = document) {

    context.querySelectorAll(".formatAngka").forEach(input => {

        const maxDecimals = parseInt(input.dataset.decimal || 2);
        const locale = input.dataset.locale || "en-US";
        const prefix = input.dataset.prefix && input.dataset.prefix !== "false"
            ? input.dataset.prefix + " "
            : "";

        // format awal jika sudah ada value
        if (input.value) {
            input.value = formatNumber(input.value, maxDecimals, locale, prefix);

            // 🔥 khusus input readonly → langsung paksa tampil decimal lengkap
            if (input.readOnly) {
                let num = parseFloat(unformatNumber(input.value));
                if (!isNaN(num)) {
                    input.value = formatNumber(num.toFixed(maxDecimals), maxDecimals, locale, prefix);
                }
            }
        }

        // realtime format saat mengetik
        input.addEventListener("input", function () {
            let start = this.selectionStart;
            let oldLength = this.value.length;

            this.value = formatNumber(this.value, maxDecimals, locale, prefix);

            let newLength = this.value.length;
            this.setSelectionRange(start + (newLength - oldLength), start + (newLength - oldLength));
        });

        // jika paste angka
        input.addEventListener("paste", function (e) {
            e.preventDefault();
            let pasted = (e.clipboardData || window.clipboardData).getData("text");
            this.value = formatNumber(pasted, maxDecimals, locale, prefix);
        });

        // 🔥 saat blur baru dipaksa 2 decimal
        input.addEventListener("blur", function () {

            let raw = unformatNumber(this.value);
            let num = parseFloat(raw);

            if (isNaN(num)) return;

            this.value = parseFloat(num).toFixed(maxDecimals); // paksa trailing zero
            this.value = formatNumber(this.value, maxDecimals, locale, prefix);
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
