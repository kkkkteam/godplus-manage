<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChatWelcomeCommand;


class WelcomeMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chatArray = array(
            '好開心今日係到見到你😊\n歡迎你回家！！🥳\n願你係呢到更多認識並經歷上帝既同在🤍',
            'Welcome Home！！✝️\n好開心相隔一個星期又見到你啊🤩\n上帝係你生命中安排左一啲mission😎\n無論係啲咩mission\n我地都一齊信靠並跟隨上帝既心意💪🏻💪🏻\n',
            'Welcome Home！！✝️\n好開心相隔一個星期又見到你啊🤩\n上帝係你生命中安排左一啲mission😎\n無論係啲咩mission\n我地都一齊信靠並跟隨上帝既心意💪🏻💪🏻\n',
        );

        foreach($chatArray as $chat){
            ChatWelcomeCommand::create([
                "message" => $chat,
            ]);
        }
    }
}
