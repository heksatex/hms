$(function () {

    // fungsi lain

    updateClock();
    setInterval(updateClock, 1000);


    // Fullscreen
    // if (!document.fullscreenElement) {
    //     document.documentElement.requestFullscreen().catch(function(err){
    //         console.log(err);
    //     });
    // }

});

function updateClock() {

    var now = new Date();

    $("#clock").text(
        now.toLocaleTimeString("id-ID", {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        }).replace(/\./g, ":")
    );

    // var tanggal = now.toLocaleDateString("id-ID", {
    //     weekday: "long",
    //     day: "2-digit",
    //     month: "long",
    //     year: "numeric"
    // });

    var optionsDate;

    if (window.innerWidth < 768) {
        // Mobile: tanpa hari
        optionsDate = {
            day: "2-digit",
            month: "long",
            year: "numeric"
        };
    } else {
        // Desktop: dengan hari
        optionsDate = {
            weekday: "long",
            day: "2-digit",
            month: "long",
            year: "numeric"
        };
    }

    var tanggal = now.toLocaleDateString("id-ID", optionsDate);

    // $("#tanggal").text(capitalizeWords(tanggal));
    $("#tanggal").text(tanggal.toUpperCase());

}

function updateClock2() {
    const now = new Date();

    // const optionsDate = { day: '2-digit', month: 'long', year: 'numeric' };

    var optionsDate;
    if (window.innerWidth < 991) {
        // Mobile: tanpa hari
        optionsDate = {
            day: "2-digit",
            month: "long",
            year: "numeric"
        };
    } else {
        // Desktop: dengan hari
        optionsDate = {
            weekday: "long",
            day: "2-digit",
            month: "long",
            year: "numeric"
        };
    }

    const dateStr = now.toLocaleDateString('id-ID', optionsDate).toUpperCase();


    const timeStr = now.getHours().toString().padStart(2, '0') + ':' +
        now.getMinutes().toString().padStart(2, '0') + ':' +
        now.getSeconds().toString().padStart(2, '0');

    document.getElementById('live-date').innerText = dateStr;


    document.getElementById('live-time').innerText = timeStr;
}


function capitalizeWords(str) {
    return str.replace(/\b\w/g, function (c) {
        return c.toUpperCase();
    });
}



function alert_notify(icon, message, type, callback) {
    $.notify({
        icon: icon,
        message: message,
    }, {
        type: type,
        allow_dismiss: true,
        newest_on_top: false,
        showProgressbar: false,
        placement: {
            from: "top",
            align: "right"
        },
        z_index: 2000,
        //delay: 500,
        timer: 500,
    });
    callback();
}


