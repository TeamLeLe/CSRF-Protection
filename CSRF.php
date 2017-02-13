<?php 
//CSRF Protection to prevent extend Form Attacks
session_start();
ob_start();

if (!isset($_SESSION['SEC_TOKEN'])) $_SESSION['SEC_TOKEN'] = md5(uniqid().uniqid().mt_rand());
if (count($_POST) > 0 && (!isset($_POST['token']) || $_POST['token'] != $_SESSION['SEC_TOKEN'])) exit;
register_shutdown_function(function() {
    $data = ob_get_clean();
    preg_match_all('\'<form .*?>(.*?)</form>\'si', $data, $match, PREG_OFFSET_CAPTURE);
    $toAdd       = 0;
    $hiddenToken = '<input type="hidden" name="token" value="'.$_SESSION['SEC_TOKEN'].'" />';
    if($match) {
        foreach ($match[0] as $entry) {
            $data = substr($data, 0, ($offset = (strpos($entry[0], '</form>') + $entry[1] + $toAdd))) . $hiddenToken . substr($data, $offset);
            $toAdd += strlen($hiddenToken);
        }
    }
    echo $data;
});
?>
