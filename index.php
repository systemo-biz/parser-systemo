<?php
/*
Plugin Name: Parser by Systemo
Version: 0.1
Plugin URI: https://github.com/systemo-biz/parser-systemo
Description: Плагин стартер для написания простого парсера контента.
Author: Systemo
Author URI: https://github.com/systemo-biz/
*/


class ParserSystemo {

  function __construct(){
    add_shortcode( 'parser_s', array($this, 'parser_s_cb'));
  }

  function parser_s_cb($attr){

    extract(shortcode_atts(array(
      'url'         => 'http://systemo.biz/blog',
      'user_agent'  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0',
    ),$atts));

    $args = array(
    	'method'     => 'GET',
      'timeout'     => 5,
    	'redirection' => 5,
    	'httpversion' => '1.0',
    	'user-agent'  => $user_agent,
    	'blocking'    => true,
    	'headers'     => array(),
    	'cookies'     => array(),
    	'body'        => null,
    	'compress'    => false,
    	'decompress'  => true,
    	'sslverify'   => false,
    	'stream'      => false,
    	'filename'    => null
    );

      $result = wp_safe_remote_request( $url, $args );

      ob_start();
      ?>
        <div>
          Запрос данных по адресу:
          <pre><?php echo $url ?></pre>

          <h1>Ответ</h1>
          <pre><?php var_dump($result['response']); ?></pre>

          <h1>Заголовок</h1>
          <pre><?php var_dump($result['headers']); ?></pre>

          <h1>Результат</h1>

          <ul>
            <?php
              //Подключаем парсер DOM дерева http://simplehtmldom.sourceforge.net/manual.htm
              include_once 'inc/simplehtmldom/simple_html_dom.php';
              $body = new simple_html_dom();
              $body->load($result['body']);

              //Получаем список элементов для обработку в список
              $list = $body->find('#main .status-publish');
            ?>

            <?php foreach($list as $element): ?>
                <li>
                  <?php
                    //получаем ИД атрибут элемента
                    $id = $element->id;
                    echo $id;
                  ?>
                  <br/>
                  <?php
                    //Находим внутри элемента заголовок с ссылкой и выдергиваем текст ссылки
                    $title = $element->find('h1.entry-title a', 0);
                    echo $title->plaintext;
                  ?>
                  <br/>
                  <?php
                    //Находим внутри элемента ссылку
                    $title = $element->find('h1.entry-title a', 0);
                    echo $title->href;
                  ?>
                  <hr>
                </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php
      $html = ob_get_contents();
      ob_get_clean();
      return $html;
  }


}
$TheParserSystemo = new ParserSystemo;
