// 音声登録をしていない時のみ音声登録ボタンの表示。
var voiceprint_flg = document.getElementById("voiceprint_flg");

// 初回表示のみ空なので０を代入。
if (voiceprint_flg.value == "" ) {
    voiceprint_flg.value = "0";
};
if (voiceprint_flg.value != "0") {
    document.querySelector('.block_rec').style.display = 'none';
};

// AzureSpeech APIキー
var subscriptionKey = "b3b1966bbaf6480cb49c0e296b6f74f6";
var serviceRegion = "westus";
// HTMLのprofile_idを取得
var value_profile_id = document.getElementById("profile_id");

// DBのprofile_id(未設定、０の場合に新規作成)
if (value_profile_id.value == 0) {

    var speechConfig = SpeechSDK.SpeechConfig.fromSubscription(subscriptionKey, serviceRegion);
    speechConfig.setProperty(SpeechSDK.PropertyId.SpeechServiceConnection_TranslationVoice, "ja-JP");
    client = new SpeechSDK.VoiceProfileClient(speechConfig);
    console.log(client);

    // 参考
    // profile = client.createProfileAsync(SpeechSDK.VoiceProfileType.TextIndependentIdentification, "ja-JP");
    // window.console.log(profile);

    // profile_idを作成し、変数resultに代入。
    client.createProfileAsync(
        SpeechSDK.VoiceProfileType.TextIndependentVerification,
        "ja-JP",
        function (result) {
            new_profile_id = result;
            // profileId作成時にERRORがないか確認、あった場合はERRORの表示
            if ( result.profileId )	{

                // HTMLに新しいprofile_idをセット
                value_profile_id.value = result.profileId;

                console.log("プロファイル作成 ProfileId: " + result.profileId);
                console.log(result.profileId);
                profileId = result.profileId;

                // //profileを保存
                // localStorage.setItem("profile1", JSON.stringify(result));
                // localStorage.setItem("profileId1", JSON.stringify(result.profileId));
                // localStorage.setItem("profile_lcnt1", profile_lcnt[0]);
                // profile_lcnt[0] = 0;
            } else {
            window.console.log("ERROR(プロファイル作成)");
            console.log("ERROR(プロファイル作成): ProfileIDエラー");
            console.log("サーバに負荷が掛かっています。1分ほど経ってから、ユーザを選択くしてください。");
            alert("サーバに負荷が掛かっています。1分ほど経ってから、ユーザを選択くしてください。");
            window.location.href = '/home'; // 通常の遷移
        }
    },
    function (err) {
        window.console.log(err);
        console.log("ERROR(プロファイル作成): " + err);
        alert("ERROR(プロファイル作成): " + err);
        window.location.href = '/home'; // 通常の遷移
    });
}
