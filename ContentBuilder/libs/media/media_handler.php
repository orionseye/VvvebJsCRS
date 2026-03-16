<?php
include_once("session.php");
include_once(LIB."Utils.php");

$serviceID = $_POST['serviceID'] ?? $_GET['serviceID'] ?? 0;
$action = $_GET['action'] ?? $_POST['action'] ?? 'scan';
$basePath = USER_DATA.'standalone/'.$serviceID.'/editor/';

switch($action) {
	case 'upload':
		// whole case code is merged upload.php
		$uploadDenyExtensions  = ['php'];
		$uploadAllowExtensions = ['ico','jpg','jpeg','png','gif','webp','svg'];
		$resizableExtensions   = ['jpg','jpeg','png','gif','webp'];
		$maxFileSize           = 2 * 1024 * 1024; // 2MB
		$maxDimension          = 1000;

		function showError($error) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
			die($error);
		}

		function sanitizeFileName($file) {
			$disallow = ['.htaccess', 'passwd'];
			$file = str_replace($disallow, '', $file);
			//sanitize, remove double dot .. and remove get parameters if any
			$file = preg_replace('@\?.*$@' , '', preg_replace('@\.{2,}@' , '', preg_replace('@[^\/\\a-zA-Z0-9\-\._]@', '', $file)));
			return $file;
		}

		// Use $basePath directly
		$uploadDir = $basePath;
		$subPath   = '';

		$fileName = sanitizeFileName($_FILES['file']['name']);
		if (!$fileName) showError('Invalid filename!');

		$extension = strtolower(substr($fileName, strrpos($fileName, '.') + 1));

		if (in_array($extension, $uploadDenyExtensions))  showError("File type $extension not allowed!");
		if (!in_array($extension, $uploadAllowExtensions)) showError("File type $extension not allowed!");

		// 2MB size check
		if ($_FILES['file']['size'] > $maxFileSize) showError("File too large! Maximum size is 2MB.");

		$destination = $uploadDir . $subPath . $fileName;
		move_uploaded_file($_FILES['file']['tmp_name'], $destination);

		// Resize if raster image and exceeds 1000px
		if (in_array($extension, $resizableExtensions)) {
			[$width, $height] = getimagesize($destination);

			if ($width > $maxDimension || $height > $maxDimension) {
				if ($width >= $height) {
					$newWidth  = $maxDimension;
					$newHeight = intval($height * $maxDimension / $width);
				} else {
					$newHeight = $maxDimension;
					$newWidth  = intval($width * $maxDimension / $height);
				}

				switch($extension) {
					case 'jpg': case 'jpeg': $source = imagecreatefromjpeg($destination); break;
					case 'png':              $source = imagecreatefrompng($destination);  break;
					case 'gif':              $source = imagecreatefromgif($destination);  break;
					case 'webp':             $source = imagecreatefromwebp($destination); break;
				}

				$resized = imagecreatetruecolor($newWidth, $newHeight);

				// Preserve transparency for png/gif
				if (in_array($extension, ['png', 'gif'])) {
					imagealphablending($resized, false);
					imagesavealpha($resized, true);
				}

				imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

				switch($extension) {
					case 'jpg': case 'jpeg': imagejpeg($resized, $destination, 90); break;
					case 'png':              imagepng($resized, $destination);       break;
					case 'gif':              imagegif($resized, $destination);       break;
					case 'webp':             imagewebp($resized, $destination, 90);  break;
				}

				imagedestroy($source);
				imagedestroy($resized);
			}
		}

		if (isset($_POST['onlyFilename'])) {
			echo $fileName;
		} else {
			echo $subPath . $fileName;
		}
	    break;
    case 'scan':
    default:
			// whole case code is merged scan.php
			function sanitizePath($path) {
				//sanitize, remove double dot .. and remove get parameters if any
				$path = preg_replace('@/+@' , '/', preg_replace('@\?.*$@' , '', preg_replace('@\.{2,}@' , '', preg_replace('@[^\/\\a-zA-Z0-9\-\._]@', '', $path))));
				
				return $path;
			}

			$scanPath = $basePath;

			$scandir = $scanPath;

			// Run the recursive function
			$scan = function ($dir) use ($scandir, &$scan) {
				$files = [];

				if (file_exists($dir)) {
					foreach (scandir($dir) as $f) {
						if (! $f || $f[0] == '.') {
							continue;
						}

						if (is_dir($dir . '/' . $f)) {
							$files[] = [
								'name'  => $f,
								'type'  => 'folder',
								'path'  => str_replace($scandir, '', $dir) . '/' . $f,
								'items' => $scan($dir . '/' . $f),
							];
						} else {
							$files[] = [
								'name' => $f,
								'type' => 'file',
								'path' => str_replace($scandir, '', $dir) . '/' . $f,
								'size' => filesize($dir . '/' . $f),
							];
						}
					}
				}

				return $files;
			};

			$response = $scan($scandir);

			header('Content-type: application/json');
			echo json_encode([
				'name'  => '',
				'type'  => 'folder',
				'path'  => '',
				'items' => $response,
			]);
        break;
}