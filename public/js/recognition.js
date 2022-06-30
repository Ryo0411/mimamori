const pulldown = document.getElementById('pulldown');
const rec_img = document.getElementById("rec_img");
const _token = document.getElementById("token");

const speakerRecognation = function (base64data, sex) {
    const options = {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'audio_file': base64data,
            'sex': sex,
            '_token': _token.value
        })
    };
    fetch('/speaker_rcognition', options)
        .then(response => response.json())
        .then(response => {
            console.log(response);
            if (response['status'] === 0) {
                const name = response['wanderer_name'];
                const score = response['confidence'];
                const rrate = Math.floor(score * 100);
                // let recog = "高確率";
                let recog = "";
                if (rrate < 30) {
                    // recog = "低確率";
                    recog = "";
                } else if (rrate < 60) {
                    // recog = "中確率";
                    recog = "";
                }
                document.getElementById("exe_result").innerHTML = "<p>認識結果、「" + name + "」さん<br>である可能性があります。</p>";
                document.getElementById("result_pop").innerText = "認識結果 : " + "成功";
                // document.getElementById("probability").innerText = "認識率：" + (Math.floor(score * 100)) + "%";
                location.href = '#modal_d';
            } else if (response['status'] === 1) {
                document.getElementById("exe_result").innerHTML = "<p>認識結果、該当者なし</p>";
                document.getElementById("result_pop").innerText = "認識結果 : " + "該当者なし";
                document.getElementById("probability").innerText = "";
                location.href = '#modal_d';
            } else {
                document.getElementById("exe_result").innerHTML = "<p>データ取得に失敗しました</p>";
                document.getElementById("errorresult").innerHTML = "データ取得に失敗しました";
                location.href = '#modal_e';
            }
        })
        .catch(err => {
            console.error(err)
            document.getElementById("errorresult").innerHTML = err;
            location.href = '#modal_e';
        });
}

let isRecording = false;
// 録音ボタンをタップした際の処理。
rec_img.addEventListener("click", function () {
    // プルダウンの性別が選択しているか確認。
    if (pulldown.value != "0") {
        // 音声の録音開始。
        if (!isRecording) {
            location.href = '#modal_r';
            console.log("認識用音声録音中...");
            isRecording = true;
            startRecording(
                function () {
                    console.log("音声サンプル録音中...");
                    isRecording = true;
                    rec_img.src = "../../img/rec.gif";
                },
                function (error) {
                    rec_img.src = "../../img/rec_on.png";
                    isRecording = false;
                    document.getElementById("exe_result").innerHTML = "<p>マイクの使用が拒否されました。</p>";
                    location.href = '#modal_me';
                }
            );
        }
    } else {
        rec_img.src = "../../img/rec_off.png";
        console.log("性別を選択してください。");
    }
});

document.getElementById("stop-recording").onclick = function () {
    if (isRecording) {
        // 音声認識開始
        stopRecording(
            function (wavfile, rawfile) {
                console.log("認識用音声録音完了");
                console.log("音声認識中...");

                let reader = new FileReader();
                reader.readAsDataURL(rawfile);
                reader.onloadend = function () {
                    let base64data = reader.result;
                    speakerRecognation(base64data, pulldown.value);
                    rec_img.src = "../../img/rec_on.png";
                    isRecording = false;
                }
            },
            function (error) {
                document.getElementById("exe_result").innerHTML = "<p>" + error + "</p>";
                rec_img.src = "../../img/rec_on.png";
                btn_regist.style.display = 'none';
                isRecording = false;
            }
        );
    }
};
