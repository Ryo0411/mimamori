var pulldown = document.getElementById('pulldown');
rec_img = document.getElementById("rec_img");

// AzureSpeech APIキー
var subscriptionKey = "b5f85c4e633a4b2489425abf8c6db446";
var serviceRegion = "japaneast";

profile_id = document.querySelectorAll(".profile_id");
profile_count = profile_id.length;
// 音声認識をかける件数
console.log("検索対象" + (profile_id.length + 1) + "人");
var SpeechSDK;
var speechConfig;
var ansrate = 0;
var profile = new Array(profile_count);

verificationVoiceRecordButton = document.getElementById("verificationVoiceRecordButton");
verificationDownload = document.getElementById("verificationDownload");

// 録音ボタンをタップした際の処理。
rec_img.addEventListener("click", function () {
    high_score = 0;

    // プルダウンの性別が選択しているか確認。
    if (pulldown.value != "0" && profile_id.length != 0) {
        // 音声の録音開始。
        if (!isRecording) {
            console.log("認識用音声録音中...");
            startRecording(function () {
                rec_img.src = "../../img/rec.gif";
            });
        } else {
            // 音声認識開始
            stopRecording(function (file) {
                console.log("認識用音声録音完了");
                console.log("音声認識中...");

                if (file) {
                    var onsei_score = new Array(profile_count);
                    var result_count = 0;

                    for (let i = 0; i < profile_count; i++) {

                        console.log(i);

                        // profile = 'profile' + [i];
                        // window.console.log("lcnt no=" + SpeechSDK);
                        profile[i] = { "privId": profile_id[i].defaultValue, "privProfileType": 2, "profileId": profile_id[i].defaultValue };
                        console.log("lcnt no=" + profile);

                        // AzureSpeech APIに接続
                        speechConfig = SpeechSDK.SpeechConfig.fromSubscription(subscriptionKey, serviceRegion);
                        speechConfig.setProperty(SpeechSDK.PropertyId.SpeechServiceConnection_TranslationVoice, "ja-JP");

                        let testAudioConfig = SpeechSDK.AudioConfig.fromWavFileInput(file);
                        let recognizer = new SpeechSDK.SpeakerRecognizer(speechConfig, testAudioConfig);
                        let model = SpeechSDK.SpeakerVerificationModel.fromProfile(profile[i]);

                        // console.log("認識profile=" + profile_id[i].defaultValue);
                        // window.console.log("認識profile=" + profile[i]);

                        recognizer.recognizeOnceAsync(
                            model,
                            function (result) {
                                result_count++;
                                window.console.log(result);
                                let reason = result.reason;
                                console.log("(認識結果) : " + SpeechSDK.ResultReason[reason]);

                                if (reason === SpeechSDK.ResultReason.Canceled) {
                                    let cancellationDetails = SpeechSDK.SpeakerRecognitionCancellationDetails.fromResult(result);
                                    window.console.log(cancellationDetails);
                                    console.log("(認識キャンセル)" + "ユーザ:" + (i + 1) + "の音声認識に失敗しました。: " + cancellationDetails.errorDetails);

                                    location.href = '#modal_e';
                                    document.getElementById("errorresult").innerHTML = "(認識キャンセル)" + "ユーザ:" + (i + 1) + "の音声認識に失敗しました。";
                                    // alert("(認識キャンセル)" + "ユーザ:" + (i+1) + "の音声認識に失敗しました。");

                                } else {
                                    console.log("(認識結果) ユーザ: " + (i + 1) + "人目");
                                    console.log("(認識結果) Profile Id: " + result.profileId);
                                    console.log("(認識結果) スコア: " + result.score);
                                    onsei_score[i] = result.score;
                                    if (high_score < onsei_score[i]) {
                                        console.log("(判別) hiスコア: " + high_score + "getscore:" + onsei_score[i]);

                                        // 認識率の高いユーザーを保持
                                        profileId = result.profileId;
                                        high_score = onsei_score[i];
                                        high_score_no = i + 1;
                                    }
                                    ansspno = high_score_no;
                                    ansrate = high_score;
                                }
                                select_user = i;
                                console.log("ナンバー" + result_count);
                                console.log("音声ナンバー" + onsei_score);
                                console.log("ハイナンバー" + high_score);
                                console.log("ユーザー名" + select_user);
                                // 一番最後、結果の表示処理
                                if (result_count >= profile_count) {
                                    // 最後に結果を表示
                                    console.log((Math.floor(high_score * 100)) + "%の確率でユーザ" + high_score_no + "と一致しました。");
                                    console.log(profileId + "一番認識率の高いユーザー");

                                    if (onsei_score[select_user - 1] != null) {
                                        userscore = (Math.floor(onsei_score[select_user - 1] * 100)) + "%";
                                        var profileId_exe = document.getElementById(profileId);
                                        console.log(profileId_exe.value);
                                        document.getElementById("exe_result").innerHTML = "<p>認識結果、" + (Math.floor(high_score * 100)) + "%<br>の確率で" + profileId_exe.value + "である可能性が高いです。";
                                        location.href = '#modal_d';
                                        document.getElementById("result_pop").innerText = "認識結果 : " + "成功";
                                        document.getElementById("probability").innerText = "認識率：" + (Math.floor(high_score * 100)) + "%";
                                    } else {
                                        document.getElementById('exe_result').innerHTML = "<p>音声認識に失敗しました。</p>";
                                        userscore = "スコアなし";
                                        location.href = '#modal_d';
                                        document.getElementById("result_pop").innerText = "認識結果 : " + "失敗";;
                                        document.getElementById("probability").innerText = "認識率：" + "音声認識に失敗しました。";
                                        // alert("認識結果 : " + "失敗" + "\n" + "認識率：" + userscore);
                                    }

                                    //ポップアップメニュー個別ユーザーリセット
                                    // if (high_score_no == select_user) {
                                    //     // ログインユーザーの迷子者だ合った場合。
                                    //     // alert("認識結果 : " + "正解" + "\n" + "認識率："　+ (Math.floor(high_score * 100)) + "%");
                                    //     location.href = '#modal_d';
                                    //     document.getElementById("result_pop").innerText = "認識結果 : " + "成功";
                                    //     document.getElementById("probability").innerText = "認識率：" + (Math.floor(high_score * 100)) + "%";
                                    // } else {
                                    //     // ログインユーザー以外迷子者だ合った場合。
                                    //     if (onsei_score[select_user - 1] != null) {
                                    //         userscore = (Math.floor(onsei_score[select_user - 1] * 100)) + "%";
                                    //         location.href = '#modal_d';
                                    //         document.getElementById("result_pop").innerText = "認識結果 : " + "成功";
                                    //         document.getElementById("probability").innerText = "認識率：" + userscore;;
                                    //     } else {
                                    //         userscore = "スコアなし";
                                    //         location.href = '#modal_d';
                                    //         document.getElementById("result_pop").innerText = "認識結果 : " + "失敗";;
                                    //         document.getElementById("probability").innerText = "認識率：" + "音声認識に失敗しました。";
                                    //         // alert("認識結果 : " + "失敗" + "\n" + "認識率：" + userscore);
                                    //     }
                                    // }
                                }
                            },
                            function (err) {
                                result_count++;
                                window.console.log(err);
                                console.log("ERROR: " + err);
                                location.href = '#modal_e';
                                document.getElementById("errorresult").innerHTML = "ERROR: " + err;
                                // alert("ERROR: " + err);
                            });
                        rec_img.src = "../../img/rec_on.png";
                        var myURL = window.URL || window.webkitURL;
                        // verificationDownload.innerHTML = "<a href='" + myURL.createObjectURL(file) + "' target='_blank'>ダウンロード</a>";
                    }
                }
            });
        }
    } else {
        rec_img.src = "../../img/rec_off.png";
        console.log("性別を選択してください。");
    }
});

//--------------------------------------------------------------------------------
// API処理関係（下記コピペ）
//--------------------------------------------------------------------------------

// for audio
let audioSampleRate = null;
let scriptProcessor = null;
let audioContext = null;
let isRecording = false;
let mediastreamsource = null;
let audioData = [];
let bufferSize = 1024;
let localMediaStream;

var startRecording = function (callback) {
    var constraints = { audio: true };
    audioData = [];
    navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
        audioContext = new AudioContext();
        audioSampleRate = audioContext.sampleRate;
        //   audioSampleRate = 16000;
        window.console.log('audioSampleRate: ' + audioSampleRate);
        scriptProcessor = audioContext.createScriptProcessor(bufferSize, 1, 1);
        mediastreamsource = audioContext.createMediaStreamSource(stream);
        mediastreamsource.connect(scriptProcessor);
        scriptProcessor.onaudioprocess = function (e) {
            var input = e.inputBuffer.getChannelData(0);
            var bufferData = new Float32Array(bufferSize);
            for (var i = 0; i < bufferSize; i++) {
                bufferData[i] = input[i];
            }
            audioData.push(bufferData);
        };
        scriptProcessor.connect(audioContext.destination);
        window.console.log('Recording Start...');
        isRecording = true;
        localMediaStream = stream;
        if (typeof callback === "function") {
            callback();
        }
    },
        function (error) {
            window.console.log(error);
        }
    );
};

var exportWav = function (audioData) {
    var encodeWav = function (samples, sampleRate) {
        var buffer = new ArrayBuffer(44 + samples.length * 2);
        var view = new DataView(buffer);

        var writeString = function (view, offset, string) {
            for (var i = 0; i < string.length; i++) {
                view.setUint8(offset + i, string.charCodeAt(i));
            }
        };

        var floatTo16BitPCM = function (output, offset, input) {
            for (var i = 0; i < input.length; i++, offset += 2) {
                var s = Math.max(-1, Math.min(1, input[i]));
                output.setInt16(offset, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
            }
        };

        writeString(view, 0, 'RIFF');  // RIFFヘッダ
        view.setUint32(4, 32 + samples.length * 2, true); // これ以降のファイルサイズ
        writeString(view, 8, 'WAVE'); // WAVEヘッダ
        writeString(view, 12, 'fmt '); // fmtチャンク
        view.setUint32(16, 16, true); // fmtチャンクのバイト数
        view.setUint16(20, 1, true); // フォーマットID
        view.setUint16(22, 1, true); // チャンネル数
        view.setUint32(24, sampleRate, true); // サンプリングレート
        view.setUint32(28, sampleRate * 2, true); // データ速度
        view.setUint16(32, 2, true); // ブロックサイズ
        view.setUint16(34, 16, true); // サンプルあたりのビット数
        writeString(view, 36, 'data'); // dataチャンク
        view.setUint32(40, samples.length * 2, true); // 波形データのバイト数
        floatTo16BitPCM(view, 44, samples); // 波形データ

        return view;
    };

    var mergeBuffers = function (audioData) {
        var sampleLength = 0;
        for (var i = 0; i < audioData.length; i++) {
            sampleLength += audioData[i].length;
        }
        var samples = new Float32Array(sampleLength);
        var sampleIdx = 0;
        for (var i = 0; i < audioData.length; i++) {
            for (var j = 0; j < audioData[i].length; j++) {
                samples[sampleIdx] = audioData[i][j];
                sampleIdx++;
            }
        }
        return samples;
    };

    var dataview = encodeWav(mergeBuffers(audioData), audioSampleRate);
    var audioBlob = new Blob([dataview], { type: 'audio/wav' });
    window.console.log(dataview);

    var myURL = window.URL || window.webkitURL;
    return audioBlob;
};

var stopRecording = function (callback) {
    isRecording = false;
    mediastreamsource.disconnect(scriptProcessor);
    scriptProcessor.disconnect(audioContext.destination)
    audioContext.close().then(function () {
        window.console.log('Recording End');

        if (typeof callback === "function") {
            callback(exportWav(audioData));
        }
    }).catch(function (error) {
        window.console.log(error);
        rec_img.src = "../../img/rec_on.png";
    });
};