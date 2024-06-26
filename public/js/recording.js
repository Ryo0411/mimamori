const enrollmentDownload = document.getElementById("enrollmentDownload");
const sampleDownload = document.getElementById("sampleDownload");
const rec_img = document.getElementById("rec_img");
const btn_regist = document.getElementById("btn_regist");
const audio_file = document.getElementById("audio_file");
const audio_base64 = document.getElementById("audio_base64");
const voiceprint_flg = document.getElementById("voiceprint_flg");
let isRecording = false;
const fixed_text = [
    '本日息子と二人で釣り船に乗り、大きなアジを3尾釣ることができました。<br/>家に帰ってアジフライにして、美味しく頂きました。',
    '本日家族が一人増えました。名前はポチです。<br/>産まれてまだ一か月の子犬で、色は黒でとっても甘えん坊です。',
    '私の趣味は家庭菜園です。この間初めてナスを育てました。<br/>大きく立派に育ったので、夏野菜カレーにして頂きました。',
    'この間散歩に出かけた際に、雨が降ってきてしまいました。<br/>傘を持っていなかった為、1時間ほど雨宿りをしました。',
    '今日の夕飯はカレーです。<br/>私の家では隠し味にチョコレートを入れます。<br/>甘さとコクが増しとても美味しいです。',
];

const birth_text = [
    'ご自身の生年月日をお教えてください。'
];

const birth_text2 = [
    '私の生年月日は○○○○年△△月××日です。'
];


document.getElementById("exe_recording").onclick = function () {
    if (!isRecording) {
        // const index = Math.floor(Math.random() * fixed_text.length);
        // document.getElementById("text_pop").innerHTML = fixed_text[index];
        // location.href = '#modal_d';
        // console.log("音声サンプル録音中...");
        // btn_regist.style.display = 'none';
        // isRecording = true;
        startRecording(
            function () {
                // 偶数奇数で読み上げるポップアップの内容を変更する
                // if (Number(voiceprint_flg.value) % 2 !== 0 || Number(voiceprint_flg.value) <= 5) {
                //     const index = Math.floor(Math.random() * birth_text.length);
                //     document.getElementById("text_pop").innerHTML = birth_text[index];
                //     document.getElementById("text_pop").style.fontWeight = "bold";
                //     location.href = '#modal_d';
                // } else {
                //     const index = Math.floor(Math.random() * fixed_text.length);
                //     document.getElementById("text_pop").innerHTML = fixed_text[index];
                //     document.getElementById("text_pop").style.fontWeight = "normal";
                //     location.href = '#modal_d';
                // }

                // 学習時の文章指定
                const index = Math.floor(Math.random() * birth_text2.length);
                document.getElementById("text_pop").innerHTML = birth_text2[index];
                document.getElementById("text_pop").style.fontWeight = "bold";
                location.href = '#modal_d';

                console.log("音声サンプル録音中...");
                btn_regist.style.display = 'none';
                isRecording = true;
                document.getElementById("exe_result").innerHTML = "<p>録音ボタンをタップして、<br>ご自身の生年月日を読み上げて<br>音声を録音してください。</p>";
                rec_img.src = "./img/rec.gif";
            },
            function (error) {
                rec_img.src = "./img/rec_on.png";
                btn_regist.style.display = 'none';
                isRecording = false;
                document.getElementById("exe_result").innerHTML = "<p>音声データがありませんでした。</p>";
                // if ($.remodal) {
                //     var modal = $.remodal.lookup[$('[data-remodal-id=modal_d]').data('remodal')];
                //     modal.close();
                // }
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
                    enrollmentDownload.innerHTML = "<audio src='" + myURL.createObjectURL(wavfile) + "' preload='metadata' controls></audio>";
                    sampleDownload.innerHTML = "<audio src='" + myURL.createObjectURL(wavfile) + "' preload='metadata' controls></audio>";
                    console.log(reader);
                    console.log(wavfile);

                    const blob = wavfile;
                    const fr = new FileReader()
                    fr.readAsDataURL(blob);
                    fr.onload = () => {
                        const r = fr.result;
                        // base64の部分のみをvalue出力
                        audio_base64.value = r.slice(r.indexOf(',') + 1);

                    };
                    console.log(fr);

                    let base64data = reader.result;
                    audio_file.value = base64data;
                    btn_regist.style.display = '';
                    isRecording = false;

                    // raw ファイルダウンロード
                    // const link = document.createElement('a');
                    // link.download = 'audio.raw';
                    // link.href = myURL.createObjectURL(rawfile);
                    // link.click();
                    // console.log(link);
                    location.href = '#modal_confaudio';
                }
                document.getElementById("exe_result").innerHTML = "<p>録音した音声を登録するには、<br>登録ボタンをタップしてください。</p>";
            },
            function (error) {
                document.getElementById("exe_result").innerHTML = "<p>" + error + "</p>";
                rec_img.src = "./img/rec_on.png";
                btn_regist.style.display = 'none';
                isRecording = false;
            }
        );
    };
};
