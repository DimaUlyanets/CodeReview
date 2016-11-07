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
<br><button onClick="sendData();">Suck it!</button>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fetch/1.0.0/fetch.min.js"></script>
<script>

    function sendData() {

        var url = 'http://mitka.local/api/lessons?api_token=2yYuwzRnUF5A3H6Kf84PmTcMNnkgRrJbXNnOALWXhmRCqKTZemNWCYTzeoZ3';
        var data = {
            "name": "ClassPddddsdddddri vateGroup",
            "difficulty": 100,
            "type": 1,
            "tags[0]": "poc",
            "is_collaborative": 1
        };
        var formData  = new FormData();

        for(name in data) {
            formData.append(name, data[name]);
        }

        formData.append('lesson_file', document.getElementById("file").files[0]);
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