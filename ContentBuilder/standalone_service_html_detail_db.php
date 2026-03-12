<?php
include_once("session.php");
include LIB."dbdriver/PDO.php";
include_once(CLASSCORE."pages/StandaloneItemAdminMainPage.php");

$page= new  StandaloneItemAdminMainPage();

// Get editor content
$editorContent =  trim($_POST['htmlX']);
$serviceID = $_POST['serviceID'];
$userID = $_POST['userID'];
	
$action = $_POST["actionX"];

if (! empty($action)) {
    switch ($action) {
        case "add":
				$pdo->beginTransaction();
				
				$statement = $pdo->prepare("INSERT INTO standalone_services_html_detail (html, serviceID, userID, last_updated) VALUES (?,?,?,NOW())");
				$statement->execute([$editorContent,$serviceID,$userID]);

				$pdo->commit();
				
				// regenerate the temp file with actual content
				$tempFile = '_temp/temp_' . $serviceID . '.html';
				file_put_contents($tempFile, $editorContent);
				
					if($statement)
					DrawUtils::drawSuccessPanel('Data saved',false,false);
            break;
        case "edit":
				$pdo->beginTransaction();
				
				$statement = $pdo->prepare(" UPDATE standalone_services_html_detail SET html=?, userID=?, last_updated=NOW() WHERE serviceID=".$serviceID." ");
				$statement->execute([$editorContent, $userID]);

				$pdo->commit();
				
				// regenerate the temp file with actual content
				$tempFile = '_temp/temp_' . $serviceID . '.html';
				file_put_contents($tempFile, $editorContent);
				
					if($statement)
					DrawUtils::drawSuccessPanel('Data saved',false,false);
            break;
    }
}
?>