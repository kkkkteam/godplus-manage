<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChatCommand;

class ChatCommandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chatArray = array(
            array(1, NULL, '我想返崇拜', 'God+ 😃 星期六3:00pm期待見你\n\nhttps://godplus-manage.site/member/service/join?m=__MOBILE__', 'God+ 😃 星期六3:00pm期待見你, __NAME__\n\n請用以下link報名\nhttps://godplus-manage.site/member/service/join?m=__MOBILE__', NULL, NULL, 1, NULL, '2024-04-24 18:20:41'),
            array(2, NULL, '我想加入GodPlus', '請填妥以下表格❤️\nhttps://godplus-manage.site/member/join?m=__MOBILE__', '請填妥以下表格❤️\nhttps://godplus-manage.site/member/join?m=__MOBILE__', NULL, NULL, 1, NULL, '2024-04-26 18:45:00'),
            array(3, NULL, 'news', '今週GodPlus資訊有🥳\n1. 報名下週崇拜\nhttps://godplus-manage.site/member/service/join?m=__MOBILE__\n\n2. 大豆蠟燭製作班🕯️\n歡迎您參加大豆蠟燭製作班，希望與您一起製作、一起傾下計，進而享受生活、認識新的關係。\n15/06 (六) ｜晚上7:30-9:30\n報名 👉👉 https://bit.ly/3UYjFlm\n\n經文：🔖\n我必在曠野開道路, 在沙漠開江河。(以賽亞書 43:19下)', '__NAME__ 很關心教會唷🥹\n\n今週GodPlus資訊有🥳\n1. 報名下週崇拜\nhttps://godplus-manage.site/member/service/join?m=__MOBILE__\n\n2. 大豆蠟燭製作班🕯️\n歡迎您參加大豆蠟燭製作班，希望與您一起製作、一起傾下計，進而享受生活、認識新的關係。\n15/06 (六) ｜晚上7:30-9:30\n報名 👉👉 https://bit.ly/3UYjFlm\n\n經文：🔖\n我必在曠野開道路, 在沙漠開江河。(以賽亞書 43:19下)', NULL, NULL, 1, '2024-04-24 03:35:20', '2024-05-29 18:07:47'),
            array(4, NULL, '查看我的報名記錄', '🥰 你已報名的崇拜：\n__SERVICE__', '您好 __NAME__ 🤗\n你已報名的崇拜：\n__SERVICE__', NULL, NULL, 0, '2024-04-26 12:54:30', '2024-04-26 12:56:13'),
            array(5, NULL, '我係新朋友', 'WELCOME HOME！\n初次見面！好高興認識你!', 'WELCOME HOME！__NAME__\n初次見面！好高興認識你!', NULL, NULL, 0, '2024-04-28 11:09:15', '2024-04-28 11:09:15'),
        );

        foreach($chatArray as $chat){
            ChatCommand::create([
                "command" => $chat[2],
                "reply" => $chat[3],
                "reply_with_name" => $chat[4],
            ]);
        }

    }
}


