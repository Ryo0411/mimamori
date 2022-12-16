const audioSampleRate = 16000;
let scriptProcessor = null;
let audioContext = null;
let mediastreamsource = null;
let audioData = [];
let bufferSize = 1024;
let localMediaStream;

const startRecording = function (success, failure) {
    let constraints = {
        audio: true
    };
    audioData = [];
    navigator.mediaDevices.getUserMedia(constraints).then(
        function (stream) {
            audioContext = new AudioContext({
                sampleRate: audioSampleRate
            });
            scriptProcessor = audioContext.createScriptProcessor(bufferSize, 1, 1);
            mediastreamsource = audioContext.createMediaStreamSource(stream);
            mediastreamsource.connect(scriptProcessor);
            scriptProcessor.onaudioprocess = function (e) {
                let input = e.inputBuffer.getChannelData(0);
                let bufferData = new Float32Array(bufferSize);
                for (let i = 0; i < bufferSize; i++) {
                    bufferData[i] = input[i];
                }
                audioData.push(bufferData);
            };
            scriptProcessor.connect(audioContext.destination);
            window.console.log('Recording Start...');
            localMediaStream = stream;
            if (typeof success === "function") {
                success();
            }
        },
    ).catch(function (error) {
        window.console.log(error);
        if (typeof failure === "function") {
            failure(error);
        }
    });
};

const mergeBuffers = function (audioData) {
    let sampleLength = 0;
    for (let i = 0; i < audioData.length; i++) {
        sampleLength += audioData[i].length;
    }
    let samples = new Float32Array(sampleLength);
    let sampleIdx = 0;
    for (let i = 0; i < audioData.length; i++) {
        for (let j = 0; j < audioData[i].length; j++) {
            samples[sampleIdx] = audioData[i][j];
            sampleIdx++;
        }
    }
    return samples;
};

const exportRaw = function (audioData) {
    const encodeRaw = function (samples) {
        let buffer = new ArrayBuffer(samples.length * 2);
        let view = new DataView(buffer);

        const floatTo16BitPCM = function (output, offset, input) {
            for (let i = 0; i < input.length; i++, offset += 2) {
                let s = Math.max(-1, Math.min(1, input[i]));
                output.setInt16(offset, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
            }
        };
        floatTo16BitPCM(view, 0, samples); // 波形データ
        return view;
    };

    let dataview = encodeRaw(mergeBuffers(audioData));
    let audioBlob = new Blob([dataview], {
        type: 'application/octet-stream;name=audio.raw'
    });
    window.console.log(dataview);

    return audioBlob;
};

const exportWav = function (audioData) {
    const encodeWav = function (samples, sampleRate) {
        let buffer = new ArrayBuffer(44 + samples.length * 2);
        let view = new DataView(buffer);

        const writeString = function (view, offset, string) {
            for (let i = 0; i < string.length; i++) {
                view.setUint8(offset + i, string.charCodeAt(i));
            }
        };

        const floatTo16BitPCM = function (output, offset, input) {
            for (let i = 0; i < input.length; i++, offset += 2) {
                let s = Math.max(-1, Math.min(1, input[i]));
                output.setInt16(offset, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
            }
        };

        writeString(view, 0, 'RIFF'); // RIFFヘッダ
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

    let dataview = encodeWav(mergeBuffers(audioData), audioSampleRate);
    let audioBlob = new Blob([dataview], {
        type: 'audio/wav'
    });
    window.console.log(dataview);

    return audioBlob;
};

const stopRecording = function (success, failure) {
    mediastreamsource.disconnect(scriptProcessor);
    scriptProcessor.disconnect(audioContext.destination)
    audioContext.close().then(function () {
        window.console.log('Recording End');
        console.log("audioDataサイズ = " + audioData.length)
        // ここで audioData のサイズを確認（配列がゼロ）
        if (audioData.length === 0) {
            if (typeof failure === "function") {
                // failure で callback させる
                let error = "音声データがありませんでした。";
                failure(error);
            }
        } else {
            if (typeof success === "function") {
                success(exportWav(audioData), exportRaw(audioData));
            }
        }

    }).catch(function (error) {
        window.console.log(error);
        if (typeof failure === "function") {
            failure(error);
        }
    });
};

const enrollmentDownload = document.getElementById("enrollmentDownload");
const rec_img = document.getElementById("rec_img");
let isRecording = false;
const fixed_text = [
    '音声が録音できるかチェックします。本日の日付を声に出してお話しください。<br>話し終わりましたら、「録音終了」をタップしてください。',
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
                const index = Math.floor(Math.random() * fixed_text.length);
                document.getElementById("text_pop").innerHTML = fixed_text[index];
                location.href = '#modal_d';
                console.log("音声サンプル録音中...");
                isRecording = true;
                rec_img.src = "./img/rec.gif";
            },
            function (error) {
                rec_img.src = "./img/rec_on.png";
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
                    enrollmentDownload.innerHTML = "<audio src='" + myURL.createObjectURL(wavfile) + "' preload='metadata' controls autoplay loop></audio>";
                    console.log(wavfile);
                    document.getElementById("exe_result").innerHTML = "<p>音声録音が確認できました。</p>";

                    let base64data = reader.result;
                    isRecording = false;

                    // raw ファイルダウンロード
                    // const link = document.createElement('a');
                    // link.download = 'audio.raw';
                    // link.href = myURL.createObjectURL(rawfile);
                    // link.click();
                    // console.log(link);

                }
            },
            function (error) {
                document.getElementById("exe_result").innerHTML = "<p>" + error + "</p>";
                rec_img.src = "./img/rec_on.png";
                isRecording = false;
            }
        );
    };
};