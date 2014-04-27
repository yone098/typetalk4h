<?hh
ini_set('display_errors', 1);
require_once('TypetalkClient.php');

// create typetalk client
$tt_client = new TypetalkClient();
try {
	// get profile
	echo "=== GET PROFILE ===<br/>\n";
	$profile = $tt_client->get_profile();
	echo "profile name:" . $profile['name'] . "<br/>\n";

	// get topic list
	echo "=== GET TOPIC LIST ===<br/>\n";
	$topic_list = $tt_client->get_topic_list();
	foreach ($topic_list as $topic) {
		$topic_id = $topic['topic']['id'];
		echo "topic:" . $topic['topic']['name'] . " topic_id:" . $topic_id . "<br/>\n";
		
	}
	// get topic message
	echo "=== GET TOPIC MESSAGE LIST ===<br/>\n";
	$query = array('count' => '3', 'from' => '252395', 'direction' => 'forward');
	$topic_message_list = $tt_client->get_topic_message_list('4800', $query);
	foreach ($topic_message_list as $topic_message) {
		$msg = $topic_message['message'];
		$msg_id = $topic_message['id'];
		echo "msg_id:" . $msg_id . " msg:" . $msg . "<br/>\n";
	}

	// post topic message
	echo "=== POST TOPIC MESSAGE ===<br/>\n";
	$query = array('message' => 'ポストテスト!');
	$post_topic_message = $tt_client->post_topic_message('4800', $query);
	echo "post topic_id:" . $post_topic_message['id'] . "<br/>\n";
} catch (TypetalkException $e) {
	echo "ERROR:" . $e->getMessage() . "<br/>\n";
}

