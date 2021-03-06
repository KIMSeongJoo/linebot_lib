<?php
require_once __DIR__ . '/linebot.php';

use \LINE\LINEBot\Constant\HTTPHeader;
use Carbon\Carbon;

$bot = new LineBotClass();
$jsonBasePath = "json/proto2/";

// default richmenu
$richMenu1 = "richmenu-2bd72247bd2ad3e0570c5fb5a271615e";
// exception Error string
$exceptionStringList = [
    "3ヶ月以内"
    , "半年以内"
    , "1年以内"
    , "1年以上先"
    , "具体的なイメージはない"
    , "OK！次へ"
    , "年月を変更する"
    , "OK！次はお金について"
    , "もっと詳しく"
    , "もっと見る"
    ,"見学した"
    ,"見学してない"
    ,"出会えた！"
    ,"出会えなかった"
    ,"申し込みたい"
    ,"もっと見学する"
    ,"マンション"
    ,"一戸建て"
    ,"契約した"
    ,"もっと考えたい"
    ,"購入予算の計算方法"
    ,"住宅ローン"
    ,"相場金額を知りたい"
    ,"何のお金が必要？"
    ,"その他"
    ,"お金について"
    ,"条件整理の方法"
    ,"家の買い時"
    ,"住み替えの流れ・スケジュール"
    ,"街の選び方"
    ,"その他"
    ,"物件情報収集の方法"
    ,"不動産の選び方"
    ,"家の種類"
    ,"詳しい人の話が聞きたい"
    ,"もっと色んな情報が見たい"
    ,"戻る"
    , "1 : 3日目"
    , "2：見学予定日前日"
    , "3：見学予定日翌日"
    , "4：見学14日後"
    , "5：契約済み翌日"
    , "6：契約済み2日後"
    , "7：契約済み16日後"
    , "予定日が決まっていない"
    , "何から始めたらいい？"
    , "全体の流れ"
    , "申込～入居までの流れ"
    , "その他"
    , "詳細を見る"
    , "経験者の声"
    , "専門家の声"
    , "家の種類の決め方"
    , "注文住宅も検討できる？"
    , "自分に合う種類を診断"
];

$richMenuList = array(
    50 => array(
        0 => 'richmenu-2bd72247bd2ad3e0570c5fb5a271615e',
        1 => 'richmenu-b88df54a05d305aafcd494e57a5b605e',
        2 => 'richmenu-2eb71a998704fc3ae9167e0611f8285a'
    ),
    101 => array(
        0 => 'richmenu-e529d223d43db14dece9876f56c86778',
        1 => 'richmenu-dcbb8be07ee29bd5549fbe34e3b7e320',
        2 => 'richmenu-9bd7ad0683037ad8a0ac9af223f939a6'
    ),
    200 => array(
        0 => 'richmenu-44ef9f12f17f273712e8f9ed14c35672',
        1 => 'richmenu-7a105096138f3ba31de9055715c68e57',
        2 => 'richmenu-6c456e409046cf58983aa46373d4ca9c'
    ),
    300 => array(
        0 => 'richmenu-956ba685f137a7a3d2e03049919c91d4',
        1 => 'richmenu-b4606c576099e135f3c7037e20c0a433',
        2 => 'richmenu-f4ab007907fe6d686b0f45f1df667333'
    )
);

try {
    // メッセージがなくなるまでループ
    while ($bot->check_shift_event()) {
        // 画像url
        $photo_url = "https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcR7qaQn2l1wMGGR2A67kAqXzuFtWtyVcvB_uJTpV66yrcPlRjhA";
        // 動画url
        $video_url = "https://example.com/original.mp4";
        // 音声url
        $audio_url = "https://www.youtube.com/watch?v=60ItHLz5WEA";

        // テキストを取得
        $text = $bot->get_text();
        // メッセージタイプを取得
        $message_type = $bot->get_message_type();
        // イベントタイプを取得
        $event_type = $bot->get_event_type();
        // $bot->add_text_builder("イベントタイプ:" . $event_type);

        // line uid
        $userId = $bot->get_user_id();

        error_log("=================================== log tracking");
        error_log('event_type : ' . $event_type);
        error_log('message_type : ' . $message_type);
        error_log('replyToken : ' . $bot->getReplyToken());
        error_log("log tracking ===================================");

        // text message
        if ($text !== false) {
            $actions = test_quick_action();

            $message = [];
            if ($text === "開始") {
                setRichMenu($userId, $richMenu1);
                $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath .'information_01.json'));
                $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath .'information_02.json'));
                error_log($bot->getReplyToken());
            } elseif ($text === 'シナリオ') {
                $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents('json/scenario_start.json'));
            } elseif ($text === '注文住宅のスケジュール') {
                $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath .'information_20.json'));
            } else {
                preg_match('/^([0-9]{4})年([0-9]{1,2})月$/', $text, $matches);
                if(count($matches) > 0) {
                    setRichMenu($userId, $richMenuList[101][1]);
                    $message['messages'][] = sprintf(preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_18.json')), $text);
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_04.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_05.json'));
                } else {
                    // 에러 안 나게 할 문자 체크
                    if (!in_array($text, $exceptionStringList)) {
                        error_log("=========error text============");
                        error_log($text);
                        error_log("=========error text============");
                        $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath .'message_ng.json'));
                    } else {
                        return true;
                    }
                }
            }

            $message['replyToken'] = $bot->getReplyToken();
            $response = curlTest($message);
            return true;

        }

        // postback action
        if ($event_type === "postback") {
            $message = [];
            $post_data = $bot->get_post_data();
            $post_params = $bot->get_post_params();
            $post_text = "post_data:" . $post_data . "\n";
            foreach ((array)$post_params as $key => $value) {
                $post_text .= $key . ":" . $value . "\n";
            }

            error_log("======== post back type ============");
            error_log($post_data);

            // postback action
            switch ($post_data) {
                case 'info_months':
                    // 3ヶ月以内
                case 'info_half_year':
                    // 半年以内
                case 'info_under_year':
                    // １年以内
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_06.json'));
                    break;
                case 'info_over_year':
                    // １年以上
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_03.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_04.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_05.json'));
                    break;
                case 'information_next':
                    // 次へ
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_07.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_08.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_09.json'));
                    break;
                case 'information_dont_image':
                    // 具体的なイメージはない
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_19.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_04.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_05.json'));
                    break;
                case 'information_go_money':
                    // お金について見る
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_13.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_14.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'information_ng':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_17.json'));
                    break;
                case 'information_ng_detail':
                    // もっと詳しく
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_10.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_11.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_12.json'));
                    break;
                case 'information_money_more':
                    // お金案内＞もっと見る
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_16.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'scenario_01':
                case 'bot_id:3,sc:101,mi:sa0201':
                case 'bot_id:3,rmt:2,now:101':
                    if ($post_data === 'bot_id:3,rmt:2,now:101') {
                        setRichMenu($userId, $richMenuList[101][1]);
                    }
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_01.json'));
                    break;
                case 'info_schedule_date':
                    // 見学予定日入力
                    setRichMenu($userId, $richMenuList[101][1]);
                    $userInfo = null;
                    $date = $bot->get_post_params();
                    if (count($date) > 0) {
                        foreach ($date as $key => $val) {
                            if ( $key === 'date') {
                                $userInfo = $val;
                            }
                        }
                    }
                    if(!is_null($userInfo)) {
                        $carbon = new Carbon($userInfo);
                        $message['messages'][] = sprintf(preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_06.json')), $carbon->format('Y年m月d日'));
//                        $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_06.json'));
                    } else {
                        $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_06.json'));
                    }
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_07.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_2-1.json'));
                    break;
                case 'info_schedule_no':
                    // 予定日決まってない
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_02.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_03.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_04.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-2.json'));
                    break;
                case 'scenario_02':
                    // 予定日前日
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_09.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_10.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_2-1.json'));
                    break;
                case 'info_chk_list':
                    // チェックリスト
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_07.json'));
                    break;
                case 'scenario_03':
                    // 見学翌日
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_11.json'));
                    break;
                case 'info_schedule_comp':
                case 'bot_id:3,rmt:1,sc:200':
                    // 見学した
                    setRichMenu($userId, $richMenuList[200][1]);
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_14.json'));
                    break;
                case 'build_comp':
                    // 出会えた
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_18.json'));
                    break;
                case 'need_build':
                    // 申し込みたい
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_22.json'));
                    break;
                case 'mansion':
                    // マンション
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_23.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_24.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_3-2.json'));
                    break;
                case 'build':
                    // 一戸建て
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_26.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_27.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_3-2.json'));
                    break;
                case 'dont_need':
                    // もっと見学する
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_19.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_20.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_3-1.json'));
                    break;
                case 'build_no_comp':
                    // 出会えなかった
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_15.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_16.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_3-1.json'));
                    break;
                case 'info_schedule_dont_go':
                    // 見学してない
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_12.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_2-1.json'));
                    break;
                case 'scenario_04':
                    // 契約した？
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_29.json'));
                    break;
                case 'bot_id:3,rmt:1,sc:300':
                case 'contract_comp':
                    // 契約した
                    // 리치메뉴 변경
                    setRichMenu($userId, $richMenuList[300][1]);
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_33.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_34.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_4-1.json'));
                    break;
                case 'dont_contract':
                    // 契約してない
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_30.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_31.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_3-2.json'));
                    break;
                case 'scenario_05':
                    // 契約したー＞1日後
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_36.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_37.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_4-1.json'));
                    break;
                case 'scenario_06':
                    // 契約したー＞２日後
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_39.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_40.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_4-1.json'));
                    break;
                case 'scenario_07':
                case 'bot_id:3,rmt:1,sc:700':
                    // 계약 완료
                    setRichMenu($userId, $richMenu1);
                    // 契約したー＞16日後
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_42.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_43.json'));
                    break;
                case 'quick_money':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/money_01.json'));
                    break;
                case 'quick_1-1':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/money_01_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_1-2':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/money_02_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/money_02_02.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_1-3':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/money_03_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_1-4':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/money_04_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/money_04_02.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_1-5':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/money_05_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_condition':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/condition_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/condition_02.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_buy_home':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/buy_home_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/buy_home_02.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_schedule':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/schedule_01.json'));
                    break;
                case 'quick_4-1':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/schedule_01_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_4-2':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/schedule_02_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_4-3':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/schedule_03_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_4-4':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/schedule_04_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_choice_town':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/choice_town_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/choice_town_02.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_build_info':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/build_info_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/build_info_02.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_estate':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/estate_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_home_type':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/home_type_01.json'));
                    break;
                case 'quick_8-1':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/home_type_02_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_8-2':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/home_type_03_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_8-3':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/home_type_04_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_counseling':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/counseling_01.json'));
                    break;
                case 'quick_9-1':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/counseling_02_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_9-2':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/counseling_03_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'quick_more_info':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick/more_info_01.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'msg_quick_1-2':
                    // 퀵 리플라이 1-2
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-2.json'));
                    break;
                case 'msg_quick_1-1':
                    // 퀵 리플라이 1-1
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'msg_quick_2-2':
                    // 퀵 리플라이 2-2
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_2-2.json'));
                    break;
                case 'msg_quick_2-1':
                    // 퀵 리플라이 2-1
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_2-1.json'));
                    break;
                case 'msg_quick_3-2':
                    // 퀵 리플라이 3-2
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_3-2.json'));
                    break;
                case 'msg_quick_3-1':
                    // 퀵 리플라이 3-1
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_3-1.json'));
                    break;

                case 'bot_id:3,rmt:2,mi:qc_01':
                case 'bot_id:3,rmt:1,now:50':
                    // 스테이터스 => 정보 수집
                    setRichMenu($userId, $richMenuList[50][1]);
                    if ($post_data !== 'bot_id:3,rmt:1,now:50') {
                        $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_04.json'));
                        $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'quick_1-2.json'));
                    }
                    break;
                case 'bot_id:3,rmt:1,now:101':
                    // 스테이터스 => 견학 예정
                    setRichMenu($userId, $richMenuList[101][1]);
                    break;
                case 'bot_id:3,rmt:1,now:200':
                    setRichMenu($userId, $richMenuList[200][1]);
                    // 스테이터스 => 견학 완료
                    break;
                case 'bot_id:3,rmt:1,now:300':
                    setRichMenu($userId, $richMenuList[300][1]);
                    // 스테이터스 => 계약 완료
                    break;


                case 'bot_id:3,rmt:0,now:50':
                    // 나의 상태 => 정보 수집
                    setRichMenu($userId, $richMenu1);
                    break;
                case 'bot_id:3,rmt:0,now:101':
                    // 나의 상태 => 견학 예졍
                    setRichMenu($userId, $richMenuList[101][0]);
                    break;
                case 'bot_id:3,rmt:0,now:200':
                    // 나의 상태 => 견학이 끝났다.
                    setRichMenu($userId, $richMenuList[200][0]);
                    break;
                case 'bot_id:3,rmt:0,now:300':
                    // 나의 상태 => 계약 완료
                    setRichMenu($userId, $richMenuList[300][0]);
                    break;


                case 'bot_id:3,rmt:2,now:50':
                    // 도움 되는 정보 ( 정보 수집 )
                    setRichMenu($userId, $richMenuList[50][2]);
                    break;
                case 'market:ry,method:info':
                case 'bot_id:3,rmt:2,now:101':
                    // 도움 되는 정보 ( 견학 예정 )
                    setRichMenu($userId, $richMenuList[101][2]);
                    break;
                case 'bot_id:3,rmt:2,now:200':
                    // 도움 되는 정보 ( 견학 완료 )
                    setRichMenu($userId, $richMenuList[200][2]);
                    break;
                case 'bot_id:3,rmt:2,now:300':
                    // 도움 되는 정보 ( 계약 완료 )
                    setRichMenu($userId, $richMenuList[300][2]);
                    break;

                case 'bot_id:3,rmt:2,mi:qc_01':
                    // 리치 메뉴 도음되는 정보 1
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents($jsonBasePath . 'quick_1-1.json'));
                    break;
                case 'bot_id:3,rmt:2,mi:qc_07':
                    // 계약 완료 -> 도움 정보 -> 퀵 리플라이로 연결
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents($jsonBasePath . 'quick_4-1.json'));
                    break;
            }

            $message['replyToken'] = $bot->getReplyToken();
            $response = curlTest($message);
            return true;

//            $bot->add_text_builder($post_text);
        }

        // 返信実行
        $bot->reply();
    }

} catch (Exception $e) {
    $error = $e->getMessage();
    $bot->add_text_builder("エラーキャッチ:" . $error);
    // 返信実行
    $bot->reply();
}

/**
 * 2列のboxの項目を作成
 * @param  [type] $item_name         左に表示するテキスト
 * @param  string $item_value        右に表示するテキスト
 * @param  array  $item_name_options 左テキストのオプション
 * @param  array  $item_value_option 右テキストのオプション
 * @return [type]                    flexのbox_component
 */
function create_item($item_name="",$item_value="",$item_name_options=array(),$item_value_option=array())
{
    global $bot;

    $flex_item_texts = array();
    if (!empty($item_name)) {
        $flex_item_texts[] = $bot->create_text_component(strval($item_name),$item_name_options);
    }
    if (!empty($item_value)) {
        $flex_item_texts[] = $bot->create_text_component(strval($item_value),$item_value_option);
    }
    return $bot->create_box_component("horizontal",$flex_item_texts);
}

function create_sample_flex(){
    global $bot;
    $flex_box_main = array();
    $flex_components = array();

    // flexサンプル
    $flex_components['body'][] = $bot->create_text_component("タイトル",array("size"=>7,"weight"=>"bold"));
    $flex_components['body'][] = $bot->create_text_component("小タイトル",array("size"=>5));
    $flex_components['body'][] = $bot->create_text_component("本文",array("size"=>4,"wrap"=>true));
    // 境界線
    $flex_components['body'][] = $bot->create_separator_container();
    $flex_components['body'][] = $bot->create_text_component("小タイトル2",array("size"=>5));
    $flex_components['body'][] = $bot->create_text_component("本文2",array("size"=>4,"wrap"=>true));


    // ボディメインボックス
    $flex_box_main['body'] = $bot->create_box_component("vertical",$flex_components['body'],array("spacing"=>3));

    // フッターのアクションボタン
    $action = $bot->create_text_action_builder("次のflexSample","flex2");
    $flex_components['footer'][] = $bot->create_button_component($action,array("style"=>"secondary"));
    // フッターメインボックス
    $flex_box_main['footer'] = $bot->create_box_component("vertical",$flex_components['footer'],array("spacing"=>3));

    // ブロック
    $bubble_blocks = array(
         "body" => $flex_box_main['body']
        ,"footer" => $flex_box_main['footer']
    );

    // バブルコンテナを作成追加
    return $bot->create_bubble_container($bubble_blocks);
}

function create_sample_flex2(){
    global $bot;
    $flex_box_main = array();
    $flex_components = array();

    // ヘッドの情報
    $flex_components['header'][] = $bot->create_text_component("No.1",array("size"=>5,"color"=>"#1DB446"));
    $flex_components['header'][] = $bot->create_text_component("タイトル",array("size"=>7,"wrap"=>true,"weight"=>"bold","color"=>"#e60033"));
    $flex_components['header'][] = $bot->create_text_component("title",array("size"=>3,"color"=>"#939393"));
    // ヘッドメインボックス
    $flex_box_main['header'] = $bot->create_box_component("vertical",$flex_components['header'],array("spacing"=>4));

    // ボディの情報
    $flex_components['body'][] = $bot->create_text_component("小項目",array("size"=>5));
    $flex_components['body'][] = create_item("アイテム","1",array("flex"=>1),array("flex"=>2));
    $flex_components['body'][] = create_item("アイテム２","2",array("flex"=>1),array("flex"=>2));
    // 境界線
    $flex_components['body'][] = $bot->create_separator_container();

    $flex_components['body'][] = $bot->create_text_component("小項目2",array("size"=>5));
    $flex_components['body'][] = create_item("アイテム1","1",array("flex"=>1),array("flex"=>2));
    $flex_components['body'][] = create_item("アイテム2","2",array("flex"=>1),array("flex"=>2));
    $flex_components['body'][] = create_item("アイテム3","3",array("flex"=>1),array("flex"=>2));
    // 境界線
    $flex_components['body'][] = $bot->create_separator_container();

    // ボディメインボックス
    $flex_box_main['body'] = $bot->create_box_component("vertical",$flex_components['body'],array("spacing"=>3));

    // フッターの情報
    // フッターのアクションボタン
    $action = $bot->create_text_action_builder("次のflexSample","flex3");
    $flex_components['footer'][] = $bot->create_button_component($action,array("style"=>"secondary"));
    // フッターメインボックス
    $flex_box_main['footer'] = $bot->create_box_component("vertical",$flex_components['footer'],array("spacing"=>3));

    // ブロック
    $bubble_blocks = array(
         "header" => $flex_box_main['header']
        ,"body" => $flex_box_main['body']
        ,"footer" => $flex_box_main['footer']
    );

    // バブルコンテナを作成追加
    return $bot->create_bubble_container($bubble_blocks);
}


function create_sample_flex3($photo_url){
    global $bot;
    $flex_box_main = array();
    $flex_components = array();

    // ヘッドの情報
    $flex_components['header'][] = $bot->create_text_component("猫系プログラマー",array("size"=>7,"weight"=>"bold","color"=>"#e60033"));
    // ヘッドメインボックス
    $flex_box_main['header'] = $bot->create_box_component("vertical",$flex_components['header'],array("spacing"=>4));

    // ボディの情報
    $flex_components['body'][] = $bot->create_text_component("I am a cat",array("size"=>5));
    $flex_components['body'][] = $bot->create_text_component("吾輩は猫である、名前はまだない\n人間になりたい、この肉球ではタイピングが大変だ",array("size"=>4,"wrap"=>true));

    // ボディメインボックス
    $flex_box_main['body'] = $bot->create_box_component("vertical",$flex_components['body'],array("spacing"=>3));

    // フッターのアクションボタン
    $action = $bot->create_text_action_builder("次のflexSample","flex4");
    $flex_components['footer'][] = $bot->create_button_component($action,array("style"=>"secondary"));
    // フッターメインボックス
    $flex_box_main['footer'] = $bot->create_box_component("vertical",$flex_components['footer'],array("spacing"=>3));

    // ブロック
    $bubble_blocks = array(
         "header" => $flex_box_main['header']
        ,"hero" => $bot->create_image_component($photo_url,array("size"=>11,"aspectRatio"=>"4:3"))
        ,"body" => $flex_box_main['body']
        ,"footer" => $flex_box_main['footer']
    );

    // バブルコンテナを作成追加
    return $bot->create_bubble_container($bubble_blocks);
}

function create_sample_flex4(){
    global $bot;
    $flex_box_main = array();
    $flex_components = array();

    // flexサンプル
    $flex_components['body'][] = $bot->create_text_component("flexサンプル一覧",array("size"=>7,"weight"=>"bold"));
    $action = $bot->create_post_action_builder("","そこじゃなくて青文字の一覧の方をタップして");
    $flex_components['body'][] = $bot->create_text_component("タップ可能",array("size"=>5,"weight"=>"bold","action"=>$action));
    $action = $bot->create_text_action_builder("","flex");
    $flex_components['body'][] = $bot->create_text_component("flex1",array("size"=>4,"wrap"=>true,"action"=>$action,"align"=>"center","color"=>"#0000ff"));
    $action = $bot->create_text_action_builder("","flex2");
    $flex_components['body'][] = $bot->create_text_component("flex2",array("size"=>4,"wrap"=>true,"action"=>$action,"align"=>"center","color"=>"#0000ff"));
    $action = $bot->create_text_action_builder("","flex3");
    $flex_components['body'][] = $bot->create_text_component("flex3",array("size"=>4,"wrap"=>true,"action"=>$action,"align"=>"center","color"=>"#0000ff"));
    $action = $bot->create_text_action_builder("","flex4");
    $flex_components['body'][] = $bot->create_text_component("flex4",array("size"=>4,"wrap"=>true,"action"=>$action,"align"=>"center","color"=>"#0000ff"));


    // ボディメインボックス
    $flex_box_main['body'] = $bot->create_box_component("vertical",$flex_components['body'],array("spacing"=>3));

    // ブロック
    $bubble_blocks = array(
         "body" => $flex_box_main['body']
    );

    // バブルコンテナを作成追加
    return $bot->create_bubble_container($bubble_blocks);
}

function test_quick_action(){
    global $bot;
    $actions = array();
    $actions[] = $bot->create_quick_text_action("test","test_text");
    $actions[] = $bot->create_quick_post_action("TypePost","post_text");
    $actions[] = $bot->create_quick_date_action("TypeDate","date_text","datetime");
    $actions[] = $bot->create_quick_camera_action("camera");
    $actions[] = $bot->create_quick_camera_roll_action("camera_roll");
    $actions[] = $bot->create_quick_location_action("location");
    return $actions;
}

function replaceDoubleQuotationJsonString(string $jsonData) : string
{
    $jsonData = preg_replace('/(\"\{)/m', '{', $jsonData);
    $jsonData = preg_replace('/(\}\")/m', '}', $jsonData);

    return $jsonData;
}

function setRichMenu($uid, $richMenuId)
{
    $headers = [
        'Authorization: Bearer ' . ACCESS_TOKEN,
        'Content-Type: application/json; charset=UTF-8'
    ];

    $requestData = [
        'richMenuId' => $richMenuId,
        'userIds' => [
            $uid
        ]
    ];

    $requestData = json_encode($requestData,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.line.me/v2/bot/richmenu/bulk/link');
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $requestData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADEROPT, true);

    $result = curl_exec($curl);
    $info = curl_getinfo($curl);

    error_log($result);
    error_log($info['http_code']);

    if (!$result && $info['http_code'] !== 200) {
        error_log(curl_error($curl));
        error_log($info);
    }

    curl_close($curl);

    return $info['http_code'];
}

function curlTest($responseData)
{
    $headers = [
        'Authorization: Bearer ' . ACCESS_TOKEN,
        'Content-Type: application/json; charset=UTF-8'
    ];

    $requestData = [
        'replyToken' => $responseData['replyToken'],
        'messages' => $responseData['messages']
    ];

    $requestData = json_encode($requestData,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $requestData = stripslashes($requestData);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.line.me/v2/bot/message/reply');
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, replaceDoubleQuotationJsonString($requestData));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADEROPT, true);

    $result = curl_exec($curl);
    $info = curl_getinfo($curl);

    if (!$result && $info['http_code'] !== 200) {
        error_log(curl_error($curl));
        error_log($info);
    }

    curl_close($curl);

    return $info['http_code'];
}