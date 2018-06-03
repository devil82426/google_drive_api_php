<?php
require __DIR__ . '/vendor/autoload.php';

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
class GDrive {

	public static $key = 'client_secret.json';
	public static $token = 'credentials.json';
	public static $outclient;
	public function __construct() {
		// self::$outclient = self::getClient();
	}
	function expandHomeDirectory($path) {
		$homeDirectory = getenv('HOME');
		if (empty($homeDirectory)) {
			$homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
		}
		return str_replace('~', realpath($homeDirectory), $path);
	}
	function getClient() {
		$client = new Google_Client();
		$client->setApplicationName('Google Drive API');
		$client->setScopes(Google_Service_Drive::DRIVE);
		$client->setAuthConfig(self::$key);
		$client->setAccessType('offline');
		$client->setApprovalPrompt('force');
		// Load previously authorized credentials from a file.
		$credentialsPath = self::expandHomeDirectory(self::$token);
		if (file_exists($credentialsPath)) {
			$accessToken = json_decode(file_get_contents($credentialsPath), true);
		} else {
			// Request authorization from the user.
			$authUrl = $client->createAuthUrl();
			printf("Open the following link in your browser:\n%s\n", $authUrl);
			print 'Enter verification code: ';
			$authCode = trim(fgets(STDIN));

			// Exchange authorization code for an access token.
			$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

			// Store the credentials to disk.
			if (!file_exists(dirname($credentialsPath))) {
				mkdir(dirname($credentialsPath), 0700, true);
			}
			file_put_contents($credentialsPath, json_encode($accessToken));
			printf("Credentials saved to %s\n", $credentialsPath);
		}
		$client->setAccessToken($accessToken);

		// Refresh the token if it's expired.
		if ($client->isAccessTokenExpired()) {
			$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
		}
		return $client;
	}
	function GetRecentFile() {
		$client = self::getClient();
		$service = new Google_Service_Drive($client);
		$return_files;
		// 列出前10筆的檔名與ID.
		$optParams = array(
			'pageSize' => 10,
			'fields' => 'nextPageToken, files(id, name)',
		);
		$results = $service->files->listFiles($optParams);

		if (count($results->getFiles()) == 0) {
			$return_files = "No files found.\n";
		} else {
			$return_files .= "Files:" . "\n";
			foreach ($results->getFiles() as $file) {
				$return_files .= $file->getName() . " (" . $file->getId() . ")" . "\n";
			}
		}
		return $return_files;
	}
	function CreateFolder($folder_name) {
		$client = self::getClient();
		$service = new Google_Service_Drive($client);
		$fileMetadata = new Google_Service_Drive_DriveFile(array(
			'name' => $folder_name,
			'mimeType' => 'application/vnd.google-apps.folder'));
		$file = $service->files->create($fileMetadata, array(
			'fields' => 'id'));
		return $file->id;
	}
	function CreateFolderWithFolder($folder_name, $parent_folder_id) {
		$client = self::getClient();
		$service = new Google_Service_Drive($client);
		$fileMetadata = new Google_Service_Drive_DriveFile(array(
			'name' => $folder_name,
			'parents' => array($parent_folder_id),
			'mimeType' => 'application/vnd.google-apps.folder'));
		$file = $service->files->create($fileMetadata, array(
			'fields' => 'id'));
		return $file->id;
	}
	function UploadData($filename, $fulldir) {
		$client = self::getClient();
		$service = new Google_Service_Drive($client);
		$fileMetadata = new Google_Service_Drive_DriveFile(array(
			'name' => $filename,
		));
		$content = file_get_contents($fulldir);
		$file = $service->files->create($fileMetadata, array(
			'data' => $content,
			'uploadType' => 'multipart',
			'fields' => 'id'));
		return $file->id;
	}
	function UploadDataWithFolder($filename, $fulldir, $folderId) {
		$client = self::getClient();
		$service = new Google_Service_Drive($client);
		$fileMetadata = new Google_Service_Drive_DriveFile(array(
			'name' => $filename,
			'parents' => array($folderId),
		));
		$content = file_get_contents($fulldir);
		$file = $service->files->create($fileMetadata, array(
			'data' => $content,
			'uploadType' => 'multipart',
			'fields' => 'id'));
		return $file->id;
	}
	function Deletefile($fileId) {
		$client = self::getClient();
		$service = new Google_Service_Drive($client);
		try {
			$service->files->delete($fileId);
		} catch (Exception $e) {
			print "An error occurred: " . $e->getMessage();
		}
	}
	function PrintFileInfo($fileId) {
		$client = self::getClient();
		$service = new Google_Service_Drive($client);
		try {
			$file = $service->files->get($fileId);

			print "Title: " . $file->getTitle();
			print "Description: " . $file->getDescription();
			print "MIME type: " . $file->getMimeType();
		} catch (Exception $e) {
			print "An error occurred: " . $e->getMessage();
		}
	}
}

?>
