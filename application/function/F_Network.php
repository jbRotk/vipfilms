<?php

/**
 * 获取请求ip
 * @return array|false|string
 */
function network_ip_get()
{
    if (getenv('HTTP_CLIENT_IP')) {
        $clientIP = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $clientIP = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR')) {
        $clientIP = getenv('REMOTE_ADDR');
    } else {
        $clientIP = $HTTP_SERVER_VARS['REMOTE_ADDR'];
    }

    return $clientIP;
}

/**
 * 返回浏览器类别
 * @return string
 */
function network_user_agent_type() {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return '';
    }

    $browser = $browser_ver = '';
    $agent = $_SERVER['HTTP_USER_AGENT'];

    if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
        $browser = 'Internet Explorer';
        $browser_ver = $regs[1];
    } elseif (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
        $browser = 'FireFox';
        $browser_ver = $regs[1];
    } elseif (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
        $browser = 'Opera';
        $browser_ver = $regs[1];
    } elseif (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $browser = 'Netscape';
        $browser_ver = $regs[2];
    } elseif (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
        $browser = 'Safari';
        $browser_ver = $regs[1];
    } elseif (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
        $browser = '(Internet Explorer ' . $browser_ver . ') NetCaptor';
        $browser_ver = $regs[1];
    }

    if (!empty($browser)) {
        return addslashes($browser . ' ' . $browser_ver);
    } else {
        return 'Unknow browser';
    }
}

/**
 * 模拟提交
 * @param $url
 * @param $params
 * @param int $timeout
 * @return mixed
 * @throws Exception
 */
function network_http_post($url, $params, $timeout = 0, $header='') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    if($header){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }

    /**
     *  Post ?
     */
    if (is_array($params) && sizeof($params) > 0) {
        $postBodyString = '';
        foreach ($params as $key => $value) {
            $postBodyString .= "$key=" . urlencode($value) . '&';
        }
        unset($key, $value);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
    }

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch), 0);
    }

    curl_close($ch);
    return $response;
}

/**
 *  邮件发送函数
 *  @param  string  $toMail     接收者邮箱
 *  @param  string  $subject    邮件标题
 *  @param  string  $body       邮件内容
 *  @return string  $message    发送成功或失败消息
 */
function network_send_email($toMail, $subject, $body) {
    Yaf_loader::import(LIB_PATH . '/PHPMailer/class.phpmailer.php');
    Yaf_Loader::import(LIB_PATH . '/PHPMailer/class.smtp.php');
    $config = Yaf_Application::app()->getConfig();

    $mail = new PHPMailer();
    if(1 == $config['mail_type']){
        $mail->IsSMTP();                               // 经smtp发送
        $mail->SMTPAuth = true;                        // 打开SMTP 认证
        $mail->Host     = $config['mail_server'];      // SMTP 服务器
        $mail->Port     = $config['mail_port'];        // SMTP 端口
        $mail->Username = $config['mail_user'];        // 用户名
        $mail->Password = $config['mail_password'];    // 密码
        $mail->From     = $config['mail_from'];        // 发信人
        $mail->FromName = $config['mail_name'];        // 发信人别名
    }else{
        $mail->IsSendmail();                           // 系统自带的 SENDMAIL 发送
        $mail->From     = $config['mail_sender'];      // 发信人
        $mail->FromName = $config['mail_name'];        // 发信人别名
        $mail->AddAddress($toMail);					   //设置发件人的姓名
    }

    $mail->AddAddress($toMail);                             // 收信人
    $mail->WordWrap = 50;
    $mail->CharSet = "utf-8";
    $mail->IsHTML(true);                                    // 以html方式发送
    $mail->Subject = $subject;                              // 邮件标题
    $mail->Body = $body;                                    // 邮件内空
    $mail->AltBody = "请使用HTML方式查看邮件。";

    $code = '';
    if (!@$mail->Send()) {
        $code = 0;
    } else {
        $code = 1;
    }
    return $code;
}
