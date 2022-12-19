const pulldown = document.getElementById('pulldown');
const rec_img = document.getElementById("rec_img");
const _token = document.getElementById("token");

const voiceprint_flg = document.getElementById("voiceprint_flg");
const result = document.getElementById("result");
const sex_select = document.getElementById("sex_select");

// var output = document.getElementById("result");
// if (!navigator.geolocation) { //Geolocation apiがサポートされていない場合
//     var latitude = ""; //緯度
//     var longitude = ""; //経度
// } else {
//     function success(position) {
//         var latitude = position.coords.latitude; //緯度
//         var longitude = position.coords.longitude; //経度
//         output.innerHTML = '<p>緯度 ' + latitude + '° <br>経度 ' + longitude + '°</p>';
//         // 位置情報
//         var latlng = new google.maps.LatLng(latitude, longitude);
//         // Google Mapsに書き出し
//         var map = new google.maps.Map(document.getElementById('map'), {
//             zoom: 15, // ズーム値
//             center: latlng, // 中心座標
//         });
//         // マーカーの新規出力
//         new google.maps.Marker({
//             map: map,
//             position: latlng,
//         });
//     };
//     function error() {
//         //エラーの場合
//         var latitude = ""; //緯度
//         var longitude = ""; //経度
//     };
// }
// navigator.geolocation.getCurrentPosition(success, error); //成功と失敗を判断

const speakerRecognation = function (base64data, sex, latitude, longitude) {
    const options = {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'audio_file': base64data,
            'sex': sex,
            'latitude': latitude,
            'longitude': longitude,
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
                voiceprint_flg.style.display = "none";
                result.style.display = "none";
                sex_select.style.display = "none";
                document.getElementById("exe_result").innerHTML = "<p>認識結果、「" + name + "」さん<br>である可能性があります。</p>";
                document.getElementById("result_pop").innerText = "認識結果 : " + "成功";
                // document.getElementById("probability").innerText = "認識率：" + (Math.floor(score * 100)) + "%";
                location.href = '#modal_d';
                hideLoading();
            } else if (response['status'] === 1) {
                voiceprint_flg.style.display = "none";
                result.style.display = "none";
                sex_select.style.display = "none";
                document.getElementById("exe_result").innerHTML = "<p>認識結果、該当者なし</p>";
                document.getElementById("result_pop").innerText = "認識結果 : " + "該当者なし";
                document.getElementById("probability").innerText = "";
                location.href = '#modal_d';
                hideLoading();
            } else if (response['status'] === 2) {
                document.getElementById("exe_result").innerHTML = "<p>データ取得時に問題が発生しました。</p>";
                document.getElementById("errorrelode").innerHTML = "データ取得時に問題が発生しました。";
                location.href = '#modal_e_relode';
                hideLoading();
            } else {
                voiceprint_flg.style.display = "none";
                result.style.display = "none";
                sex_select.style.display = "none";
                document.getElementById("exe_result").innerHTML = "<p>データ取得に失敗しました</p>";
                document.getElementById("errorresult").innerHTML = "データ取得に失敗しました";
                location.href = '#modal_e';
                hideLoading();
            }

        })
        .catch(err => {
            voiceprint_flg.style.display = "none";
            result.style.display = "none";
            sex_select.style.display = "none";
            console.error(err)
            document.getElementById("errorresult").innerHTML = err;
            location.href = '#modal_e';
            hideLoading();
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
            showLoading();
            startRecording(
                function () {
                    console.log("音声サンプル録音中...");
                    isRecording = true;
                    rec_img.src = "../../img/rec.gif";
                },
                function (error) {
                    rec_img.src = "../../img/rec_on.png";
                    isRecording = false;
                    document.getElementById("exe_result").innerHTML = "<p>音声データがありませんでした。</p>";
                    location.href = '#modal_me';
                    hideLoading();
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
                    var latitude = document.getElementById('latitude').value;
                    var longitude = document.getElementById('longitude').value;
                    console.log("緯度経度取得" + latitude, longitude);
                    speakerRecognation(base64data, pulldown.value, latitude, longitude);
                    rec_img.src = "../../img/rec_on.png";
                    isRecording = false;
                }
            },
            function (error) {
                document.getElementById("exe_result").innerHTML = "<p>" + error + "</p>";
                rec_img.src = "../../img/rec_on.png";
                isRecording = false;
                hideLoading();
                btn_regist.style.display = 'none';
            }
        );
    }
};

function showLoading() {
    document.getElementById('loading').classList.remove('is-hide')
}

function hideLoading() {
    document.getElementById('loading').classList.add('is-hide')
}
