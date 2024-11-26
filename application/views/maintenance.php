<!doctype html>
<html>
  <head>
    <title>Site Maintenance</title>
    <meta charset="utf-8"/>
    <meta name="robots" content="noindex"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
      body { text-align: center; padding: 20px; font: 20px Helvetica, sans-serif; color: #efe8e8; }
      @media (min-width: 768px){
        body{ padding-top: 150px; }
      }
      h1 { font-size: 50px; }
      article { display: block; text-align: left; max-width: 650px; margin: 0 auto; }
      a { color: #dc8100; text-decoration: none; }
      a:hover { color: #efe8e8; text-decoration: none; }
    </style>
  </head>
  <body bgcolor="2e2929">
    <article>
        <h2 style="text-align: center;">We&rsquo;ll be back soon!</h2>
        <div>
            <p style="text-align: center;">Harap Bersabar</p>
            <?php if($last_day_month == TRUE){?>
              <p style="text-align: center;">Kami sedang memproses Data Akhir Bulan !</p>
           <?php }else {?>
                <p style="text-align: center;">Kami sedang mengerjakan beberapa update didalam HMS !</p>
            <?php }?>
        </div>
        <div style="display: flex; flex-direction: row; justify-content: space-between;">
            <p class="day"></p>
            <p class="hour"></p>
            <p class="minute"></p>
            <p class="second"></p>
        </div>
    </article>
    <script>
        const countDown = () => {
            const countDay = new Date("<?php echo $date_finish_mt; ?>");
            const now = new Date();
            const counter = countDay - now;
            const second = 1000;
            const minute = second * 60;
            const hour = minute * 60;
            const day = hour * 24;
            const textDay = Math.floor(counter / day);
            const textHour = Math.floor((counter % day) / hour);
            const textMinute = Math.floor((counter % hour) / minute);
            const textSecond = Math.floor((counter % minute) / second)
            document.querySelector(".day").innerText = textDay + ' Days';
            document.querySelector(".hour").innerText = textHour + ' Hours';
            document.querySelector(".minute").innerText = textMinute + ' Minutes';
            document.querySelector(".second").innerText = textSecond + ' Seconds';
        }
        setInterval(countDown, 1000);
    </script>
  </body>
</html>