<?php
require_once __DIR__ . '/linebot.php';

use \LINE\LINEBot\Constant\HTTPHeader;
use Carbon\Carbon;

$bot = new LineBotClass();
$jsonBasePath = "json/proto2/";

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

        error_log("=================================== log tracking");
        error_log('signature : ' . $_SERVER['HTTP_' . HTTPHeader::LINE_SIGNATURE] );
        error_log('event_type : ' . $event_type);
        error_log('message_type : ' . $message_type);
        error_log('reply_token : ' . $bot->getReplyToken());
        error_log("log tracking ===================================");

        // オウム返し
        if ($text !== false) {
            $actions = test_quick_action();

            $message = [];
            if ($text === "開始") {
                $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath .'information_01.json'));
                $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath .'information_02.json'));
            } elseif ($text === 'シナリオ') {
                $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents('json/scenario_start.json'));
            }

            $message['replyToken'] = $bot->getReplyToken();
//            $response = $bot->replyMessageCustom($bot->getReplyToken(), $message);
            $response = curlTest($message);
            return true;

//           $bot->add_text_builder($text);
        }

        if ($message_type !== false) {
            // $bot->add_text_builder("メッセージタイプ:" . $message_type);
        }

        // 画像取得
        // if ($message_type === "image") {
        //     file_put_contents("image/test.jpg", $bot->get_content());
        // }

        // ポストバックのイベントなら
        if ($event_type === "postback") {
            $message = [];
            $post_data = $bot->get_post_data();
            $post_params = $bot->get_post_params();
            $post_text = "post_data:" . $post_data . "\n";
            foreach ((array)$post_params as $key => $value) {
                $post_text .= $key . ":" . $value . "\n";
            }

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
//                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_04.json'));
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
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_09.json'));
                    break;
                case 'information_go_money':
                    // お金について見る
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_13.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_14.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_15.json'));
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
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'information_15.json'));
                    break;
                case 'scenario_01':
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_01.json'));
                    break;
                case 'info_schedule_date':
                    // 見学予定日入力
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_06.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_07.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_08.json'));
                    break;
                case 'info_schedule_no':
                    // 予定日決まってない
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_02.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_03.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_04.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_05.json'));
                    break;
                case 'scenario_02':
                    // 予定日前日
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_09.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_10.json'));
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
                    // 見学した
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
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_25.json'));
                    break;
                case 'build':
                    // 一戸建て
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_26.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_27.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_28.json'));
                    break;
                case 'dont_need':
                    // もっと見学する
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_19.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_20.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_22.json'));
                    break;
                case 'build_no_comp':
                    // 出会えなかった
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_15.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_16.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_17.json'));
                    break;
                case 'info_schedule_dont_go':
                    // 見学してない
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_12.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_13.json'));
                    break;
                case 'scenario_04':
                    // 契約した？
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_29.json'));
                    break;
                case 'contract_comp':
                    // 契約した
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_33.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_34.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_35.json'));
                    break;
                case 'dont_contract':
                    // 契約してない
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_30.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_31.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_32.json'));
                    break;
                case 'scenario_05':
                    // 契約したー＞1日後
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_36.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_37.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_38.json'));
                    break;
                case 'scenario_06':
                    // 契約したー＞２日後
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_39.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_40.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_41.json'));
                    break;
                case 'scenario_07':
                    // 契約したー＞16日後
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_42.json'));
                    $message['messages'][] = preg_replace("/\r|\n/", '', file_get_contents( $jsonBasePath . 'info_scenario_43.json'));
                    break;
            }
            error_log(count($message));

            $message['replyToken'] = $bot->getReplyToken();
            $response = curlTest($message);
            return true;

//            $bot->add_text_builder($post_text);
        }

        // スタンプなら
        if ($message_type == "sticker") {
            $stame_id = $bot->get_stamp_id();
            $id_text = "";
            foreach ($stame_id as $key => $value) {
                $id_text .= $key . ":" . $value . "\n";
            }
            $bot->add_text_builder($id_text);
        }

        // 位置情報なら
        if ($message_type == "location") {
            // 位置情報のデータを取得
            $locaation = $bot->get_location();
            $locaation_test = "";
            foreach ($locaation as $key => $value) {
                $locaation_test .= $key . ":" . $value . "\n";
            }
            // メッセージを追加
            $bot->add_text_builder($locaation_test);
        }

        // 画像メッセージの追加
        if ($text == "イメージ") {
            $actions = test_quick_action();
            $bot->add_image_builder($photo_url,$photo_url,$actions);
        }

        // 位置情報メッセージの追加
        if ($text == "位置情報") {
            // 位置情報
            $title = "ゲームフリーク";
            $address = "東京都世田谷区太子堂4丁目1番1号 キャロットタワー22階";
            $lat = 35.643656;
            $lon = 139.669046;
            $bot->add_location_builder($title,$address,$lat,$lon);
        }

        // スタンプメッセージの追加
        if ($text == "スタンプ") {
            $actions = test_quick_action();
            $bot->add_stamp_builder(141,2,$actions);
        }

        // 動画メッセージの追加
        if ($text == "動画") {
            $bot->add_video_builder($video_url,$photo_url);
        }

        // 音声メッセージの追加
        if ($text == "音声") {
            $bot->add_audeo_builder($audio_url,60000);
        }

        // ボタンテンプレート
        if ($text == "ボタン") {
            // アクションボタンの作成
            $action_button = array();
            $action_button[] = $bot->create_text_action_builder("TEXT_TYPE","text_test");
            $action_button[] = $bot->create_post_action_builder("POST_TYPE","post_text");
            $action_button[] = $bot->create_url_action_builder("URL_TYPE","https://developers.line.me/ja/reference/messaging-api/");
            $action_button[] = $bot->create_date_action_builder("DATE_TYPE","date_text","datetime");
            $default_action = $bot->create_text_action_builder("DEFAULT_ACTION","デフォルトアクション");
            $quick_reply_actions = test_quick_action();
            $result = $bot->add_button_template_builder("代替テキスト","アクションボタンのテストもかねて",$action_button,"テンプレートボタンテスト",$photo_url,$default_action,$quick_reply_actions);
        }

        // 確認テンプレート
        if ($text == "確認") {
            // 確認テンプレートの作成
            $action_button = array();
            // アクションの作成
            $action_button[] = $bot->create_text_action_builder("Yes","Yes");
            $action_button[] = $bot->create_text_action_builder("No","No");
            $quick_reply_actions = test_quick_action();
            $result = $bot->add_confirm_template_builder("代替テキスト","確認テンプレートのサンプル",$action_button,$quick_reply_actions);
        }

        // カルーセルテンプレート
        if ($text == "カルーセル") {
            // カルーセルテンプレートの作成
            $column_builders = array();
            for ($i=0; $i < 10; $i++) {
                // アクションボタンの作成 1~3まで有効
                $action_button = array();
                $action_button[] = $bot->create_text_action_builder("TEXT_TYPE","text_test");
                $action_button[] = $bot->create_post_action_builder("POST_TYPE","post_text");
                $action_button[] = $bot->create_date_action_builder("DATETIME_TYPE","date_text","datetime");
                // デフォルトアクションの作成
                $data_text = "デフォルトアクションtest" . ($i+1);
                $default_action = $bot->create_text_action_builder("TEXT_TYPE",$data_text);

                // 本文
                $text = ($i+1) . "ページ";
                // タイトル
                $title = "カルーセルテンプレートテスト";
                // カラムテンプレートビルダーの作成
                $result = $bot->create_carousel_column_template_builder($text,$action_button,$title,$photo_url,$default_action);
                if ($result !== false) {
                    $column_builders[] = $result;
                }
            }
            $quick_reply_actions = test_quick_action();
            // カルーセルテンプレートビルダーの追加
            $bot->add_carousel_template_builder("代替テキスト",$column_builders,$quick_reply_actions);
        }

        // イメージカルーセルテンプレート
        if ($text == "イメージカルーセル") {
            // イメージカルーセルテンプレートの作成
            $image_column_builders = array();
            for ($i=1; $i <= 10; $i++) {
                // アクションビルダーを作成
                $action_builder = $bot->create_text_action_builder("イメージ" . $i,"イメージ" . $i);
                // イメージカラムビルダーを作成
                $image_column_builders[] = $bot->create_image_column_template_builder($photo_url,$action_builder);
            }
            $quick_reply_actions = test_quick_action();
            // イメージカルーセルテンプレートの追加
            $bot->add_image_carousel_template_builder("代替テキスト",$image_column_builders,$quick_reply_actions);
        }

        // イメージマップ
        if ($text == "イメージマップ") {
            // // イメージマップの作成
            $action_area_builders = array();
            // アクションエリアビルダーの作成
            $action_area_builders[] = $bot->create_imagemap_action_area_builder(0,0,520,1040,"text","左");
            $action_area_builders[] = $bot->create_imagemap_action_area_builder(520,0,520,1040,"text","右");
            // ベースurl
            $base_url = "https://hoge.com/hoge";
            // イメージマップビルダーを追加
            $result = $bot->add_imagemap_buildr("代替テキスト",$base_url,1040,$action_area_builders);
            // 追加失敗ならエラーをスロー
            if ($result === false) {
                throw new Exception("イメージマップの追加失敗");
            }
        }

        if ($text == "flex") {
            $flex_box_main = array();
            $flex_components = array();
            $flex_bubble = array();

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
            $flex_bubble[] = $bot->create_bubble_container($bubble_blocks);

            $quick_reply_actions = test_quick_action();
            // flexメッセージを追加
            $bot->add_flex_builder("sample_flex",$flex_bubble,$quick_reply_actions);
        }

        if ($text == "flex2") {
            $flex_box_main = array();
            $flex_components = array();
            $flex_bubble = array();

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
            $flex_bubble[] = $bot->create_bubble_container($bubble_blocks);

            // flexメッセージを追加
            $bot->add_flex_builder("sample_flex",$flex_bubble);
        }

        if ($text == "flex3") {
            $flex_box_main = array();
            $flex_components = array();
            $flex_bubble = array();

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
            $flex_bubble[] = $bot->create_bubble_container($bubble_blocks);

            // flexメッセージを追加
            $bot->add_flex_builder("sample_flex",$flex_bubble);
        }

        if ($text == "flex4") {
            $flex_box_main = array();
            $flex_components = array();
            $flex_bubble = array();

            // flexサンプル
            // タイトル
            $flex_components['body'][] = $bot->create_text_component("flexサンプル一覧",array("size"=>7,"weight"=>"bold"));
            $action = $bot->create_action_builder("post","",["post"=>"そこじゃなくて青文字の一覧の方をタップして"]);
            $flex_components['body'][] = $bot->create_text_component("タップ可能",array("size"=>5,"weight"=>"bold","action"=>$action));

            // アクション作成
            $action = $bot->create_text_action_builder("","flex");
            // 項目作成
            $flex_components['body'][] = $bot->create_text_component("flex1",array("size"=>4,"wrap"=>true,"action"=>$action,"align"=>"center","color"=>"#0000ff"));

            $action = $bot->create_text_action_builder("","flex2");
            $flex_components['body'][] = $bot->create_text_component("flex2",array("size"=>4,"wrap"=>true,"action"=>$action,"align"=>"center","color"=>"#0000ff"));

            $action = $bot->create_text_action_builder("","flex3");
            $flex_components['body'][] = $bot->create_text_component("flex3",array("size"=>4,"wrap"=>true,"action"=>$action,"align"=>"center","color"=>"#0000ff"));

            $action = $bot->create_text_action_builder("","flex4");
            $flex_components['body'][] = $bot->create_text_component("flex4",array("size"=>4,"wrap"=>true,"action"=>$action,"align"=>"center","color"=>"#0000ff"));

            $action = $bot->create_text_action_builder("","flex_all");
            $flex_components['body'][] = $bot->create_text_component("全て表示",array("size"=>4,"wrap"=>true,"action"=>$action,"align"=>"center","color"=>"#0000ff"));


            // ボディメインボックス
            $flex_box_main['body'] = $bot->create_box_component("vertical",$flex_components['body'],array("spacing"=>3));

            // ブロック
            $bubble_blocks = array(
                 "body" => $flex_box_main['body']
            );

            // バブルコンテナを作成追加
            $flex_bubble[] = $bot->create_bubble_container($bubble_blocks);

            // flexメッセージを追加
            $bot->add_flex_builder("sample_flex",$flex_bubble);
        }

        if ($text == "flex_all") {
            $flex_bubble = array();
            $flex_bubble[] = create_sample_flex();
            $flex_bubble[] = create_sample_flex2();
            $flex_bubble[] = create_sample_flex3($photo_url);
            $flex_bubble[] = create_sample_flex4();
            // flexメッセージを追加
            $bot->add_flex_builder("sample_flex",$flex_bubble);
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

    error_log($headers[0]);
    error_log($headers[1]);

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