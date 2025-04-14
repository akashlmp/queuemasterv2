
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Five</title>
    <style>
        body{
            margin: 0;
        }
        .progress{
            width: 400px; 
        }
        @media (max-width:768px)
        {
            .banner-container .banner .woman{
                display: none;
            }
            .progress{
                width: 285px;
                position: relative;
                bottom: 12px;
            }
        }
    </style>
</head>
<body>
    
    <div class="banner-container container-fluid" style="display: flex; align-items: center; justify-content: center; height: 100vh; width: 100%;">
        <div class="banner" style="background: linear-gradient(-55deg, #ff7675 29%, #d63031 29.1%, #d63031 68%, #ff7675 68.1%); border-radius: 5px;  display: flex; align-items: center; justify-content: center; flex-wrap: wrap; box-shadow: 0 5px 10px #0005; overflow: hidden; width: 100%; height: 100%;">
            <div class="shoe" style="flex:1 1 250px; padding: 15px; text-align: center; ">
                <img src="https://cdn.pixabay.com/photo/2018/06/17/20/35/chain-3481377_1280.jpg" alt="" style="width: 70%;">
            </div>

            <div class="content" style="flex:1 1 250px; text-align: center; padding: 10px; text-transform: uppercase;">
                <span style="color: #eee; font-size: 25px;">Your</span>
                <h3 style="color: #fff; font-size: 30px; margin: 10px;">wait is over</h3>
                <p style="color: #fff; font-size: 20px; padding:10px 0; margin: 9px;">ready to gain your  seat in just.</p>
                <!-- <a href="#" class="btn" style="display: block; height: 40px; width: 150px; line-height: 40px; background:#fff; color: #d63031; margin: 5px auto; text-decoration: none;">view offer</a> -->

                <div class="progress" style="--progress: 0%; height: 20px;
                  border: 1px solid #fff;  padding: 8px 4px; margin-top: 20px;
                   box-shadow: 0 0 10px #aaa;">
                <div  class="bar"	style="width: <?php echo $progress_bar_per; ?>%;  height: 100%;
                background: linear-gradient( rgb(198, 17, 17), pink,  rgb(198, 17, 17));  background-repeat: repeat;
                box-shadow: 0 0 10px 0px white;   animation:  shine 4s ease-in infinite,  end 1s ease-out 1 7s;
                  transition: width 3s ease 3s;--progress: <?php echo $progress_bar_per; ?> %;"></div></div>
             <div style="margin-top: 20px;">
                <div style="color: #fff;  font-size: 20px;">	Your number in line : <span
                    style="color: black; font-weight: 600; font-size: 25px;"> <?php echo $number_in_line; ?> </span></div>
                <div style="color: #fff; margin-top: 4px; font-size: 20px;">Estimated wait time : <span
                    style="color: black; font-weight: 600; font-size: 25px;"> <?php echo $waiting_time; ?> </span></div>
            </div>
            </div>
            <div class="woman" style="position: relative; bottom: -33px; padding: 10px; flex: 1 1 250px;">
                <img src="../public/asset/img/women.png" alt="" style="width: 100%;">
            </div>
        </div>
    </div>
</body>
</html>