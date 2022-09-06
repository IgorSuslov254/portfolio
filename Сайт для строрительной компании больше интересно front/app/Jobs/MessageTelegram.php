<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use CURLFile;

class MessageTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $params;

    const TELEGRAM_TOKEN = '5003667921:AAGH6ipNSKVsA02dMbAh4XPuOwiC9hUwWQc';
    const TELEGRAM_CHATID = '-1001536908023';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        if (empty($params)) return false;

        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->params['sendDocument']){
            $this->SendDocument();
        } else {
            fopen("https://api.telegram.org/bot".self::TELEGRAM_TOKEN."/sendMessage?chat_id=".self::TELEGRAM_CHATID."&parse_mode=html&text=".urlencode($this->params['message']),"r");
        }
    }

    private function SendDocument()
    {
        $url = "https://api.telegram.org/bot".self::TELEGRAM_TOKEN."/sendDocument";

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                "chat_id" => self::TELEGRAM_CHATID,
                "document" => new CURLFile($this->params['name'], $this->params['mime'], $this->params['postname']),
                "caption" => $this->params['message'],
            ]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $out = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
//            return response($e->getMessage(), 201);
        }
    }
}
