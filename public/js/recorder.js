// for audio
const audioSampleRate = 16000;
let scriptProcessor = null;
let audioContext = null;
let mediastreamsource = null;
let audioData = [];
let bufferSize = 1024;
let localMediaStream;

const startRecording = function (success, failure) {
    let constraints = { audio: true };
    audioData = [];
    navigator.mediaDevices.getUserMedia(constraints).then(
        function (stream) {
            audioContext = new AudioContext({ sampleRate: audioSampleRate });
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
        function (error) {
            window.console.log(error);
            if (typeof failure === "function") {
                failure(error);
            }
            // if ($.remodal) {
            //     var modal = $.remodal.lookup[$('[data-remodal-id=modal_d]').data('remodal')];
            //     modal.close();
            // }
            // document.getElementById("exe_result").innerHTML = "<p>マイクの使用が拒否されました。</p>";
            // location.href = '#modal_me';
        }
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
    let audioBlob = new Blob([dataview], { type: 'application/octet-stream;name=audio.raw' });
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

    let dataview = encodeWav(mergeBuffers(audioData), audioSampleRate);
    let audioBlob = new Blob([dataview], { type: 'audio/wav' });
    window.console.log(dataview);

    return audioBlob;
};

const stopRecording = function (success, failure) {
    mediastreamsource.disconnect(scriptProcessor);
    scriptProcessor.disconnect(audioContext.destination)
    audioContext.close().then(function () {
        window.console.log('Recording End');

        if (typeof success === "function") {
            success(exportWav(audioData), exportRaw(audioData));
        }
    }).catch(function (error) {
        window.console.log(error);
        if (typeof failure === "function") {
            failure(error);
        }
    });
};
