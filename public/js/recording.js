const enrollmentDownload = document.getElementById("enrollmentDownload");
const rec_img = document.getElementById("rec_img");
const btn_regist = document.getElementById("btn_regist");
const audio_file = document.getElementById("audio_file");
let isRecording = false;
const fixed_text = [
    '本日息子と二人で釣り船に乗り、大きなアジを3尾釣ることができました。<br/>家に帰ってアジフライにして、美味しく頂きました。',
    '本日家族が一人増えました、名前はポチです。<br/>産まれてまだ一か月の子犬で、色は黒でとっても甘えん坊です。',
    '私の趣味は家庭菜園です、この間初めてナスを育てました。<br/>大きく立派に育ったので夏野菜カレーにして頂きました。',
    'この間散歩に出かけた際に雨が降ってきてしまいました。<br/>傘を持っていなかった為、1時間ほど雨宿りをしました。',
    '今日の夕飯はカレーです。<br/>私の家では隠し味にチョコレートを入れます。<br/>甘さとコクが増しとても美味しいです。',
];

document.getElementById("exe_recording").onclick = function () {
    if (!isRecording) {
        const index = Math.floor(Math.random() * fixed_text.length);
        document.getElementById("text_pop").innerHTML = fixed_text[index];
        location.href = '#modal_d';
        console.log("音声サンプル録音中...");
        btn_regist.style.display = 'none';
        isRecording = true;
        startRecording(
            function () {
                rec_img.src = "./img/rec.gif";
            },
            function (error) {
                rec_img.src = "./img/rec_on.png";
                btn_regist.style.display = '';
                isRecording = false;
                if ($.remodal) {
                    var modal = $.remodal.lookup[$('[data-remodal-id=modal_d]').data('remodal')];
                    modal.close();
                }
                document.getElementById("exe_result").innerHTML = "<p>マイクの使用が拒否されました。</p>";
                location.href = '#modal_me';
            }
        );
    }
};

document.getElementById("stop-recording").onclick = function () {
    if (isRecording) {
        stopRecording(
            function (wavfile, rawfile) {
                // 音声登録開始、画像差し替え
                rec_img.src = "./img/rec_on.png";
                console.log("音声サンプル録音終了");

                let reader = new FileReader();
                reader.readAsDataURL(rawfile);
                reader.onloadend = function () {
                    // 録音した音声データの再生ボタン
                    let myURL = window.URL || window.webkitURL;
                    enrollmentDownload.innerHTML = "<a href='" + myURL.createObjectURL(wavfile) + "' target='_blank'>再生</a>";

                    let base64data = reader.result;
                    audio_file.value = base64data;
                    btn_regist.style.display = '';
                    isRecording = false;

                    /* raw ファイルダウンロード
                    const link = document.createElement('a');
                    link.download = 'audio.raw';
                    link.href = myURL.createObjectURL(rawfile);
                    link.click();
                    */
                }
                document.getElementById("result").innerHTML = "録音した音声を登録するには、<br>登録ボタンをタップしてください。";
            },
            function (error) {
                rec_img.src = "./img/rec_on.png";
                btn_regist.style.display = '';
                isRecording = false;
            }
        );
    };
};
