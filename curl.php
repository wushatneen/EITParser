<meta charset="utf-8">
<?php

include_once 'simple_html_dom.php';
$cookie = ''; //переменная нужна для сохранения печенек
 
 
    function pageVK(/*Передавайте параметры, если нужно*/) {
        global $cookie;   
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);      
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl,CURLOPT_AUTOREFERER, true);
        curl_setopt($curl,CURLOPT_POST, true);
        curl_setopt($curl,CURLOPT_COOKIEFILE, "cookiefile");
        curl_setopt($curl,CURLOPT_COOKIE, $_COOKIE['Cookie_Login']);
        curl_setopt($curl,CURLOPT_NOBODY, false);
        curl_setopt($curl,CURLOPT_HEADER, true);
        curl_setopt($curl,CURLOPT_ENCODING, "");
        curl_setopt($curl,CURLOPT_TIMEOUT, 30);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl,CURLOPT_MAXREDIRS, 5);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', 'Accept-Encoding:gzip, deflate, sdch', 'Accept-Language: ru', 'Accept-Charset: utf-8', 'user-agent: Opera/9.1(Opera Mini/7.1)'));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', 'Accept-Encoding:gzip, deflate, sdch', 'Accept-Language: ru', 'Accept-Charset: utf-8', 'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0'));
        curl_setopt($curl,CURLOPT_URL, 'https://vk.com/apps?tab=notifications');
        curl_setopt($curl,CURLOPT_POSTFIELDS, null);
        $page = curl_execR($curl);
        echo "pageVk - OK!<br>";
        //Ну а тут делайте свое грязное дело :-)
        //apps_notification_info
        //$html = new simple_html_dom();
        //$html = file_get_html('');
        echo file_get_html('https://vk.com/apps?tab=notifications')->find('div[class=apps_notification_info]',0)->plaintext;
 
    }
    
    function checkLoginVk($Login, $Password) {
        global $cookie;   
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);      
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl,CURLOPT_AUTOREFERER, true);
        curl_setopt($curl,CURLOPT_POST, true);
        curl_setopt($curl,CURLOPT_COOKIEFILE, "cookiefile");
        curl_setopt($curl,CURLOPT_COOKIE, $_COOKIE['Cookie_Login']);
        curl_setopt($curl,CURLOPT_NOBODY, false);
        curl_setopt($curl,CURLOPT_HEADER, true);
        curl_setopt($curl,CURLOPT_ENCODING, "");
        curl_setopt($curl,CURLOPT_TIMEOUT, 30);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl,CURLOPT_MAXREDIRS, 5);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', 'Accept-Encoding:gzip, deflate, sdch', 'Accept-Language: ru', 'Accept-Charset: utf-8', 'user-agent: Opera/4.1(Opera Mini/7.1)')); //Заголовки можете поизменять, но User-Agent всегда оставляйте мобильный. Как никак в мобильной версии ВК все задачи делаются намного проще, чем в полной.
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', 'Accept-Encoding:gzip, deflate, sdch', 'Accept-Language: ru', 'Accept-Charset: utf-8', 'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0'));
        curl_setopt($curl,CURLOPT_URL, 'https://m.vk.com/login?role=fast&to=&s=1&m=1&email='.$Login);
        curl_setopt($curl,CURLOPT_POSTFIELDS, null);
        $page = curl_execR($curl);
        preg_match("/<form method=\"post\" action=\"(.*?)\" novalidate>/",$page, $hash); //Регулярку можно подправить чтоб работала наверника и novalidate убрать. А вообще, все регулярки требуют либо контроля, либо обновления на многозадачные, а то если ВК изменит одну цифру или букву в некоторых местах, скрипт падёт :-(  
        curl_setopt($curl,CURLOPT_URL, $hash[1]);
        curl_setopt($curl,CURLOPT_COOKIE, $cookie);
        curl_setopt($curl,CURLOPT_POSTFIELDS, http_build_query(array('email' => $Login, 'pass' => $Password)));
        $page = curl_execR($curl);
        curl_setopt($curl,CURLOPT_URL, 'https://m.vk.com/feed'); //Тут можно указать любую страницу авторизованного юзера
        curl_setopt($curl,CURLOPT_COOKIE, $cookie);
        $page = curl_execR($curl);
        
        $CheckAuth = preg_match("/<a.*?href=\"https:\/\/login\.vk\.com\/\?act=logout(.*?)\">/",$page); //Проверка регулярками, что мы вошли. Единственное что нужно парсить, это кнопка выход. Она всегда у авторизованного юзера будет :-)
        $checkSecure = preg_match('/id=\"security_check\"/', $page); //В моем случае попадалось много аккаунтов других стран, а точнее Украины. Естественно ВК попросит ввести цифры от телефона, это как раз проверка ))
        if($checkSecure) {
                $code = NumericPhoneVk($Login);
                preg_match("/hash: '(.*?)'/",$page, $hash);
                curl_setopt($curl,CURLOPT_URL, 'http://vk.com/login.php?act=security_check'); //Ввод недостающих цифр может хромать, так как оживил скрипт только недавно, а последний раз он у меня работал с вводом кода еще в далеком 2015-м году )))))
                curl_setopt($curl,CURLOPT_POSTFIELDS, http_build_query(array('code' => $code, 'to' => '', 'al_page' => '3', 'hash' => $hash[1])));
                $page = curl_execR($curl);      
        }
        if($CheckAuth) {
            SetCookie("Cookie_Login", deleteCopyCookies($cookie), time()+3600); //Куки служат для хранения кук, как глупо это бы не звучало. На серверных решениях Вы можете хранить их в БД и т.д.
            curl_setopt($curl, CURLOPT_URL, 'https://vk.com/id0'); //Мне нужно было имя юзера, поэтому id0 спокойно с этим справляется)))
            curl_setopt($curl,CURLOPT_COOKIE, $cookie);
            $page = curl_execR($curl);
            if(mb_check_encoding($page, 'Windows-1251') && !mb_check_encoding($page, 'UTF-8')) { $page = mb_convert_encoding($page, 'UTF-8', 'Windows-1251'); } //Какие бы заголовки я в запросе не кидал, ВК все же на кирилице отдает текст, поэтому я его конвертирую в любимую UTF-8 :-)
            preg_match("/<title>(.*?)<\/title>/", $page, $user); 
            $data = array('login' => 'ok', 'msg'=> $user[1]);
        } else { $data = array('login' => 'fail', 'msg'=> "Неправильный логин или пароль"); }
        curl_close($curl);
        echo "pre checkLoginVk - OK!<br>";
        return $data; //Я возвращаю массив, Вы можете переписать под себя, как угодно.
    }
    
    
    function curl_execR($curl) {            //альтернативная функция curl_exec для эмуляции CURLOPT_FOLLOWLOCATION (Для тех, кто пользуется халявными хостингами, где оригинальной функции нет)
        global $cookie;
        $loops = 0;
        $max_loops = 10;
            if ($loops++ >= $max_loops) { $loops = 0;  FALSE; } 
        $data = curl_exec($curl);
        preg_match_all('/Set-Cookie: (.*?; )/', $data, $cookieArr);
        for($i=0;  $i<count($cookieArr[1]); $i++) {
            $cookie = $cookie.$cookieArr[1][$i];
        }
        $temp = $data;
        list($header, $data) = explode("\n\n", $data, 2);
        $http = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http == 301 || $http == 302) {
            $matches = array();
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $url = @parse_url(trim(array_pop($matches)));
            if (!$url) { $loops = 0; return $data; }
            $last_url = parse_url(curl_getinfo($curl, CURLINFO_EFFECTIVE_URL));
            if (!$url['scheme'])
            $url['scheme'] = $last_url['scheme'];
            if (!$url['host'])
            $url['host'] = $last_url['host'];
            if (!$url['path'])
            $url['path'] = $last_url['path'];
            $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:'');
            curl_setopt($curl, CURLOPT_URL, $new_url);
            echo "curl_execR - OK!<br>";
            return curl_execR($curl);
        } else {
            echo "curl_execR - FAILED!<br>";
            $loops=0; return $temp; }
    }   
    
    
    //Удаление повторяющихся Cookies из строки (Совсем не обязательная, но я люблю чтоб все как надо было)
    function deleteCopyCookies($cookieStr) {
        $cookieKV = array();
        $cookieStrS = '';
        $cookieStr = str_replace(array("\r\n", "\n", "\r", " "), "", $cookieStr);
        $cookieArray = explode(";",$cookieStr);
        $cookieArray = array_filter($cookieArray);
            foreach($cookieArray as $item) {
                $arr = explode('=', $item);
                $cookieKV[$arr[0]] = $arr[1];
            }
            foreach ($cookieKV as $key => $value) {
                $cookieStrS = $cookieStrS.$key.'='.$value.'; ';
            }
        echo "deleteCopyCookies - OK!<br>";
        return ($cookieStrS !== '') ? $cookieStrS : false;
    }
 
 
    //Проверка страны мобильного номера ВКонтакте(Сюда Вы можете дописать нужные страны)
    function BringPhoneVk($phone) {
        $sim = substr($phone, 0, 1);
        if($sim == '+') { $phone = substr($phone, 1); }
        $prifixRus = substr($phone, 0, 1);
        $prifixUkr = substr($phone, 0, 2);
            if($prifixRus == '7' || $prifixRus == '8') { $phone = array('tel' => $phone, 'country' => 'Russia'); }
                else if($prifixUkr == '38') { $phone = array('tel' => $phone, 'country' => 'Ukraine'); }
                    else { $phone = array('tel' => $phone, 'country' => 'Unknown'); }
        echo "BringPhoneVk - OK!<br>";
        return $phone;
    }
    
    //Вывод недостающих цифр мобильного номера ВКонтакте(Соответственно тоже нужно будет подкорректировать при добавлении стран)
    function NumericPhoneVk($phone) {
        $phone = BringPhoneVk($phone);
        if(is_array($phone) && array_key_exists('tel', $phone) && array_key_exists('country', $phone)) {
            if($phone['country'] == 'Russia') {
                $numeric = substr($phone['tel'], 1, 8);
            } else if($phone['country'] == 'Ukraine') {
                $numeric = substr($phone['tel'], 1, 9);
                } else if($phone['country'] == 'Unknown') { 
                    $numeric = $phone['tel'];
                    } else { $numeric = 'Страны не существует'; }
        } else { $numeric = 'Ошибка!'; }
        echo "NumericPhone - OK!<br>";
        return $numeric;
    }
        
        
//В принципе все. Я свой функционал отсюда вырезал, если нужна будет помощь, пишите.
//Работаем следующим образом
//-Вызываем checkLoginVk с параметрами, проверяем на успех, если все норм, то вызываем pageVK и работаем уже под данным юзером :-)
 
//О недостатках если заметили, говорите. Будет полезно Ваше мнение. На данный момент знаю один неприятный - это каптча. Хотя, где то валяется скрипт позволяющий ее обойти(предварительно введя).
//Заметил что она появляется спустя большое кол-во неправильных входов. Впрочем это везде так. Защита.
//Ну на этом все, если что в теме пишите

    if(checkLoginVk('380671921907','2GPKc2y9ySiO') == true){
        echo "checkLoginVk - OK!<br>";
        pageVK();
    }else{
        echo "checkLoginVk - FAILED!<br>";
    }