
<?php
// 페이지 호출 처리
switch($context1){
    // case "sample":
    // case "calendar":
    case "album":
        $filepath = "./pages/".$context1."/".$context2;
        if (substr($filepath, -4) !== ".php") {
            $filepath .= ".php";
        }
        if (file_exists($filepath)) {
            include($filepath);
        } else {
            echo "<p>페이지가 존재하지 않습니다.</p>";
        }
    break;

    default:
        include("./pages/error.php");
        break;
}
?>