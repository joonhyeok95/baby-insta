<?php
switch($context1){
    case "api":
        $filepath = "/api/".$context2."/".$action;
        if (substr($filepath, -4) !== ".php") {
            $filepath .= ".php";
        }
        if (file_exists($_SERVER['DOCUMENT_ROOT'].$filepath)) {
            include($_SERVER['DOCUMENT_ROOT'].$filepath);
        } else {
            echo json_encode(['error' => '페이지가 존재하지 않습니다.'], JSON_UNESCAPED_UNICODE);
        }
    break;

}
?>