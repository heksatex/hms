<script>

    function CustomPrompt() {
        this.render = function (dialog, fctn) {
            var winW = window.innerWidth;
            var winH = window.innerHeight;
            var dialogoverlay = document.getElementById('dialogoverlay');
            var dialogbox = document.getElementById('dialogbox');
            dialogoverlay.style.display = "block";
            dialogoverlay.style.height = winH + "px";
            dialogbox.style.left = (winW / 2) - (560 * .5) + "px";
            dialogbox.style.top = "5px";
            dialogbox.style.display = "block";
            document.getElementById('dialogboxhead').innerHTML = "A value is required";
            document.getElementById('dialogboxbody').innerHTML = dialog;
            document.getElementById('dialogboxbody').innerHTML += '<br><textarea id="prompt_value1"></textarea>';
            document.getElementById('dialogboxfoot').innerHTML = '<button onclick="promot.ok(\'' + fctn + '\')">OK</button> <button onclick="promot.cancel()">Cancel</button>';
        }
        this.cancel = function () {
            document.getElementById('dialogoverlay').style.display = "none";
            document.getElementById('dialogbox').style.display = "none";
        }
        this.ok = function (fctn) {
            var prompt_value1 = document.getElementById('prompt_value1').value;
            window[fctn](prompt_value1);
            document.getElementById('dialogoverlay').style.display = "none";
            document.getElementById('dialogbox').style.display = "none";
        }
    }
    var promot = new CustomPrompt();

    /*Geting Value*/
    function changeText(val) {
        document.getElementById('catatan').innerHTML = val;
    }
</script>