<?hh
newtype config = array;

class TypetalkException extends Exception {}

/**
 * TypetalkClient 
 * 
 * @author yone098
 */
class TypetalkClient {
  private config $config;

    /**
     * contructor.
     */
  public function __construct() {
    $this->config = parse_ini_file('typetalk_config.ini');
  }
  
  /**
   * get profile
   * @return profile array
   */
  public function get_profile(): mixed {
    $access_token = $this->get_access_token($this->config['scope_my']);
    $url = $this->config['url_profile'];
    $contents = $this->get_contents($url, array(), $access_token);
    //$this->debug('profile:'. $contents);

    $profile = json_decode($contents, true)['account'];

    return $profile;
  }

  /**
   * get topic list
   * @return topic list array
   */
  public function get_topic_list(): mixed {
    $access_token = $this->get_access_token($this->config['scope_my']);
    $url = $this->config['url_topics'];
    $contents = $this->get_contents($url, array(), $access_token);
    //$this->debug('topic_list:'. $contents);

    $topic_list = json_decode($contents, true)['topics'];

    return $topic_list;
  }

  /**
   * get topic message list
   * @param $topic_id topic_id
   * @param $param query parameter
   * @return topic message list array
   */
  public function get_topic_message_list(string $topic_id, array $param = array()): mixed {
    $access_token = $this->get_access_token($this->config['scope_topic_read']);
    $url = $this->trailingslashit($this->config['url_topics']) . $topic_id;
    $query = http_build_query($param);
    $url = $url . '?' . $query;
    $contents = $this->get_contents($url, array(), $access_token);
    //$this->debug('topic_message_list:'. $contents);

    $topic_message_list = json_decode($contents, true)['posts'];

    return $topic_message_list;
  }

  /**
   * post topic message.
   * @param $topic_id topic_id
   * @param $param form pamameter
   * @return post message array
   */
  public function post_topic_message(string $topic_id, array $param = array()): mixed {
    $access_token = $this->get_access_token($this->config['scope_topic_post']);
    $url = $this->trailingslashit($this->config['url_topics']) . $topic_id;
    $contents = $this->get_contents($url, $param, $access_token, 'POST');
    //$this->debug('post_topic_message:'. $contents);
    $post_topic_message = json_decode($contents, true)['post'];

    return $post_topic_message;
  }

  ///////////////////////////////////////////////////////////////////
  // private method

  private function trailingslashit(string $url): string {
    return rtrim($url, '/') . '/';
  }

  private function debug(string $msg): void {
    echo($msg . "<br/>\n");
  }

  private function get_access_token(string $scope) : string {
    $content = array(
      'client_id'     => $this->config['client_id'],
      'client_secret' => $this->config['client_secret'],
      'grant_type'    => 'client_credentials',
      'scope'         => $scope
    );
    $url = $this->config['url_access_token'];
    $contents = $this->get_contents($url, $content, '', 'POST');
    if (!$contents) {
      throw new TypetalkException('Failed to get access token.');
    }
    $access_token = json_decode($contents, true)['access_token'];
    
    return $access_token;
  }    

  private function get_contents(string $url, array $content, string $access_token, string $method = 'GET'): ?mixed {
    $query = http_build_query($content);
    $header = array(
      'Content-Type: application/x-www-form-urlencoded',
      'Content-Length: ' . strlen($query)
    );
    if ($access_token !== '') {
      $header[] = 'Authorization: Bearer '  . $access_token;
    }
    $context = stream_context_create(array(
      'http' => array(
        'method' => $method,
        'content' => $query,
        'header' => implode("\r\n", $header)
      )
    ));
    $result = file_get_contents($url, false, $context);

    return $result;
  }

}
