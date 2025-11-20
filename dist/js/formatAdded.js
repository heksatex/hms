function formatNumber(value, maxDecimals, locale = "en-US", prefix = "") {
    if (!value) return "";

    const formatter = new Intl.NumberFormat(locale);
    const decimalSeparator = (1.1).toLocaleString(locale).substring(1, 2);

    // hanya izinkan angka & decimal
    let regex = new RegExp(`[^0-9${decimalSeparator}-]`, "g");
    let cleanValue = value.replace(regex, "");

    // ganti decimal separator ke titik (.) untuk parsing
    if (decimalSeparator !== ".") {
        cleanValue = cleanValue.replace(decimalSeparator, ".");
    }

    // hilangkan lebih dari 1 titik desimal
    let firstDot = cleanValue.indexOf(".");
    if (firstDot !== -1) {
        cleanValue =
        cleanValue.substring(0, firstDot + 1) +
        cleanValue.substring(firstDot + 1).replace(/\./g, "");
    }

    // kalau baru ketik titik di akhir
    if (cleanValue.endsWith(".")) {
        let parts = cleanValue.split(".");
        let intPart = parts[0] || "0";
        return prefix + formatter.format(parseInt(intPart, 10)) + decimalSeparator;
    }

    // 🔥 PROSES ROUNDING DI SINI
    let numberValue = parseFloat(cleanValue);
    if (!isNaN(numberValue) && maxDecimals >= 0) {
        numberValue = Number(numberValue.toFixed(maxDecimals));
    }

    // sekarang format kembali ke locale
    let [intPart, decPart] = numberValue.toString().split(".");

    intPart = formatter.format(intPart);

    if (decPart && maxDecimals > 0) {
        // pastikan trailing zero tetap tampil sesuai maxDecimals
        decPart = decPart.padEnd(maxDecimals, "0");
        return prefix + intPart + decimalSeparator + decPart;
    }

    return prefix + intPart;
}


function bindFormatAngka(context = document) {
    context.querySelectorAll(".formatAngka").forEach(input => {
        const maxDecimals = parseInt(input.dataset.decimal || 0);
        const locale = input.dataset.locale || "en-US";
        const prefix = input.dataset.prefix && input.dataset.prefix !== "false"
            ? input.dataset.prefix + " "
            : "";

        // 🔹 Format nilai awal jika sudah ada value
        if (input.value) {
            input.value = formatNumber(input.value, maxDecimals, locale, prefix);
        }

        input.addEventListener("input", function () {
            let start = this.selectionStart;
            let oldLength = this.value.length;

            this.value = formatNumber(this.value, maxDecimals, locale, prefix);

            let newLength = this.value.length;
            let diff = newLength - oldLength;

            this.setSelectionRange(start + diff, start + diff);
        });

        input.addEventListener("paste", function (e) {
            e.preventDefault();
            let pastedText = (e.clipboardData || window.clipboardData).getData("text");
            this.value = formatNumber(pastedText, maxDecimals, locale, prefix);
        });

        input.addEventListener("blur", function () {
            if (this.value.endsWith(".") || this.value.endsWith(",")) {
                this.value = this.value.slice(0, -1);
            }
        });
    });
}

// auto-bind di awal
document.addEventListener("DOMContentLoaded", () => {
    bindFormatAngka();
});

// auto-bind kalau modal muncul
document.addEventListener("shown.bs.modal", function (event) {
    bindFormatAngka(event.target);
});

function unformatNumber(value) {
    if (!value) return "";
    return value.replace(/[^0-9.-]/g, "");
}

