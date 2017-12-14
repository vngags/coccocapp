<?php
namespace App\Traits;
use App\Chat;
use LRedis;
use App\User;
use DateTime;

trait Chatable
{
    public function __get_messages($user_code)
    {
        $mgs = array();
        if(LRedis::EXISTS('private_messages:' . $this->code . ':' . $user_code)) {
            // $messages = LRedis::HGETALL('private_messages:' . $this->code . ':' . $user_code);
            // foreach ($messages as $message) {
            //     array_push($mgs, json_decode($message));
            // }
            // return $mgs;
            $messages = LRedis::LRANGE('private_messages:' . $this->code . ':' . $user_code, 0, -1);
            foreach ($messages as $message) {
                array_push($mgs, json_decode($message));
            }
            return $mgs;
        }elseif(LRedis::EXISTS('private_messages:' . $user_code . ':' . $this->code)) {
            // $messages = LRedis::HGETALL('private_messages:' . $user_code . ':' . $this->code);
            // foreach ($messages as $message) {
            //     array_push($mgs, json_decode($message));
            // }
            // return $mgs;
            $messages = LRedis::LRANGE('private_messages:' . $user_code . ':' . $this->code, 0, -1);
            foreach ($messages as $message) {
                array_push($mgs, json_decode($message));
            }
            return $mgs;
        }else{
            // LRedis::HSET('private_messages:' . $this->code . ':' . $user_code , 'message:' . $this->code . ':' . time(), '');
            return ['status' => 'avalible'];
        }
    }

    public function __store_message($user_code, $message)
    {
        $dt = new DateTime();

        $data = [
            'from' => $this->code,
            'message' => $message,
            'time' => $dt->format('Y-m-d H:i:s')
        ];
        if(LRedis::EXISTS('private_messages:' . $this->code . ':' . $user_code)) {
            return LRedis::RPUSH('private_messages:' . $this->code . ':' . $user_code, json_encode($data));
        }else{
            return LRedis::RPUSH('private_messages:' . $user_code . ':' . $this->code, json_encode($data));
        }
    }
}
