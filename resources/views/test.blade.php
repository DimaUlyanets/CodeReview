<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>JS Bin</title>
</head>
<body>
<br>Choose video file, you can download it here
<br>http://www.sample-videos.com/index.php#sample-mp4-video
<input type="file" id="file" />
<br><button onClick="sendData();">Dylianets it!</button>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fetch/1.0.0/fetch.min.js"></script>
<script>

    function sendData() {

        var url = 'http://mitka.local/api/users/1/profile?api_token=jWFlb99NQgwBGgm7vIfB1SzGEeYJJI92Xl1Yw9Q0XNh3Jonb63JCbAgPhuUI&cover=123';
        var data = {
            "name": "yoyko",
            "username": "stupido"
        }
        var formData  = new FormData();

        for(name in data) {
            formData.append(name, data[name]);
        }
        formData.append('avatar', document.getElementById("file").files[0]);

        fetch(url, {
            method: 'POST',
            body: formData
        })
                .then(function(response) {
                    console.log(response)
                    return response.text()
                }).then(function(body) {
            document.body.innerHTML = body
        });
    }

</script>

</body>
</html>
