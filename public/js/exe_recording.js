exe_recording = document.getElementById("exe_recording");
enrollmentDownload = document.getElementById("enrollmentDownload");
voiceprint_flg = document.getElementById("voiceprint_flg");
voiceprint_btn = document.getElementById("voiceprint_btn");
rec_img = document.getElementById("rec_img");
button = document.getElementById("button");


// AzureSpeech APIキー
var subscriptionKey = "b3b1966bbaf6480cb49c0e296b6f74f6";
var serviceRegion = "westus";

// AzureSpeech APIに接続
speechConfig = SpeechSDK.SpeechConfig.fromSubscription(subscriptionKey, serviceRegion);
speechConfig.setProperty(SpeechSDK.PropertyId.SpeechServiceConnection_TranslationVoice, "ja-JP");
client = new SpeechSDK.VoiceProfileClient(speechConfig);

document.getElementById("exe_recording").onclick = function () {
    if (!isRecording) {
        console.log("音声サンプル録音中...");
        startRecording(function () {
            rec_img.src = "./img/rec.gif";
        });
    } else {
        stopRecording(function (file) {
            // 音声登録開始、画像差し替え
            rec_img.src = "./img/rec_on.png";
            console.log("音声サンプル録音終了");
            console.log("音声サンプル学習確認中...");

            console.log(profile_id.value);

            // 録音した音声データの再生ボタン
            var myURL = window.URL || window.webkitURL;
            enrollmentDownload.innerHTML = "<a href='" + myURL.createObjectURL(file) + "' target='_blank'>再生</a>";

            profile_id = document.getElementById("profile_id");
            // APIに投げて処理するときにJSON形式の為、privProfileTypeは２で固定。
            profile_information = { "privId": profile_id.value, "privProfileType": 2, "profileId": profile_id.value };
            //ポップアップウィンドウの表示
            location.href = '#modal_a';

            // ポップアップを学習した場合。
            document.getElementById("study").onclick = function () {
                if (file) {
                    console.log(file);
                    client.enrollProfileAsync(
                        profile_information,
                        SpeechSDK.AudioConfig.fromWavFileInput(file),
                        function (result) {
                            console.log("(音声サンプル登録) Reason: " + SpeechSDK.ResultReason[result.reason]);
                            // 録音した音声データがの結果がCanceled(無音じゃない)場合に処理を実行。
                            if (SpeechSDK.ResultReason[result.reason] != "Canceled") {
                                // voiceprint_flgのカウントをアップ(録音回数のカウント)
                                voiceprintcount = Number(voiceprint_flg.value);
                                voiceprintcount++
                                console.log("voiceprint_flg 登録回数 =　" + voiceprintcount);
                                voiceprint_flg.value = voiceprintcount;

                            } else {
                                window.console.log("無音のためキャンセル");
                                window.console.log("ERROR(音声サンプル登録):無音 ");
                                alert("(音声サンプル登録):無音のためキャンセルしました。");
                            }
                        },
                        function (err) {
                            window.console.log(err);
                            window.console.log("ERROR(音声サンプル登録): " + err);
                            alert("ERROR(音声サンプル登録): " + err);
                        });
                }
            },
                //ポップアップウィンドウキャンセルの場合
                document.getElementById("cancel").onclick = function () {
                    console.log("音声サンプル登録キャンセル");
                }
        });
    };
};

// document.getElementById("button").onclick = function () {
//     if (fileaa) {
//         client.enrollProfileAsync(
//             profile_information,
//             SpeechSDK.AudioConfig.fromWavFileInput(fileaa),
//             function (result) {
//                 console.log("(音声サンプル登録) Reason: " + SpeechSDK.ResultReason[result.reason]);
//                 // 録音した音声データがの結果がCanceled(無音じゃない)場合に処理を実行。
//                 if (SpeechSDK.ResultReason[result.reason] != "Canceled") {
//                     // voiceprint_flgのカウントをアップ(録音回数のカウント)
//                     voiceprintcount = Number(voiceprint_flg.value);
//                     voiceprintcount++
//                     console.log("voiceprint_flg 登録回数 =　" + voiceprintcount);
//                     voiceprint_flg.value = voiceprintcount;

//                 } else {
//                     window.console.log("無音のためキャンセル");
//                     window.console.log("ERROR(音声サンプル登録):無音 ");
//                     alert("(音声サンプル登録):無音のためキャンセルしました。");
//                 }
//             },
//             function (err) {
//                 window.console.log(err);
//                 window.console.log("ERROR(音声サンプル登録): " + err);
//                 alert("ERROR(音声サンプル登録): " + err);
//             });
//     }
// }




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
    });
};