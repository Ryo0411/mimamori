const enrollmentDownload = document.getElementById("enrollmentDownload");
const rec_img = document.getElementById("rec_img");
const btn_regist = document.getElementById("btn_regist");
const audio_file = document.getElementById("audio_file");
let isRecording = false;
const fixed_text = [
    'サンプルテキスト1',
    'サンプルテキスト2',
    'サンプルテキスト3',
    'サンプルテキスト4',
    'サンプルテキスト5',
];

document.getElementById("exe_recording").onclick = function () {
    if (!isRecording) {
        const index = Math.floor(Math.random() * fixed_text.length) ;
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
                    const modal = $.remodal.lookup[$('[data-remodal-id=modal_d]').data('remodal')];
                    modal.close();
                }
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
                reader.onloadend = function() {
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
