<?php

// 前月、翌月のリンク作成
// $_GETを使用
//isset():変数がセットされていること、そして NULL でないことを検査する
//preg_match:正規表現によるマッチングを行う
//int preg_match ( string $pattern , string $subject [, array &$matches [, int $flags = 0 [, int $offset = 0 ]]] )
//http://php.net/manual/ja/function.preg-match.php
//正規表現では完全一致の場合\A\zで囲むことが推奨されている
//clone:オブジェクトをシャローコピーする
//シャローコピー：オブジェクトがコピーされるが参照されるメモリは同じ（メモリの値が変われば両方の表示が同じように変わる）
//ディープコピー：オブジェクトがコピーされ参照メモリも変わる（コピー元と同じ値のメモリが複製される）
//modify():タイムスタンプ（ある出来事が発生した日時・日付・時刻などを示す文字列）を変更する
//privateのプロパティやメソッドは_をつけて区別する
//名前空間を使用している場合phpに標準で用意されているException()やDateTime()のようなクラスに関しては1番上位の名前空間から呼び出さなければならない


namespace MyApp;

class Calendar {
  public $prev;
  public $next;
  public $yearMonth;
  private $_thisMonth;//thisMonthは色々なところで使うのでprivateにしておく

  public function __construct(){
    try{
      if(!isset($_GET['t']) || !preg_match('/\A\d{4}-\d{2}\z/',$_GET['t'])){
        throw new \Exception();//名前空間の区切り文字である \ を入れる
      }
      $this->_thisMonth = new \DateTime($_GET['t']);//thisMonthをprivateに書き換え。DateTimeの前に//名前空間の区切り文字である \ を入れる
    }catch(\Exception $e){//名前空間の区切り文字である \ を入れる
      $this->_thisMonth = new \DateTime('first day of this month');
    }
    $this->$prev = $this->_createPervLink(); //処理がややこしいので別メソッドにする。
    $this->$next = $this->_createNextLink();
    $this->yearMonth = $this->_thisMonth->format('F Y');
  }

  //constructの中にあるプライベートメソッドの実装
  //先月分
  private function _createPervLink(){
    $dt = clone $this->_thisMonth;//thisMonthをprivateに書き換え
    return $dt->modify('-1 month')->format('Y-m');
  }
  //来月分
  private function _createPervLink(){
    $dt = clone $this->_thisMonth;//thisMonthをprivateに書き換え
    $next = $dt->modify('+1 month')->format('Y-m');
  }

  public function show(){
    $tail = $this->_getTail();
    $body = $this->_getBody();
    $head = $this->_getHead();
    $html = '<tr>' . $tail . $body . $head . '</tr>';
    echo $html;
  }

//名前空間とプロパティに気をつける

//先月の残りを作成。先月の末日の曜日を探して日曜まで埋めていく
  private function _getTail(){
    $tail = '';
    $lastDayOfNextMonth = new \DateTime('last day of ' . $this->yearMonth . '-1 month');
    while ($lastDayOfNextMonth->format('w') < 6){
      $tail = sprintf('<td class="gray">%d</td>', $lastDayOfNextMonth->format('d')) . $tail;
      $lastDayOfNextMonth->sub(new \DateInterval('P1D'));
    }
    return $tail;
  }

//今月分の日にちを表示させる
  private function _getBody(){
    $body = '';
    $period = new \DatePeriod(
      new \DateTime('first day of '. $this->yearMonth),
      new \DateInterval('P1D'),
      new \DateTime('first day of '. $this->yearMonth . ' +1 month')
    );

    $today = new \DateTime('today');
    foreach ($period as $day) {
      if($day->format('w') === '0'){ $body .='</tr><tr>';}
      $todayClass = ($day->format('Y-m-d') === $today->format('Y-m-d')) ? 'today' : '';
      $body .= sprintf('<td class="youbi_%d %s">%d</td>' ,$day->format('w'), $todayClass, $day->format('d'));
    }
    return $body;
  }

//翌月の日にちを作成。翌月の1日の曜日を探して土曜日まで埋める
  private function _getHead(){
    $head = '';
    $firstDayOfNextMonth = new \DateTime('first day of ' . $this->yearMonth . '+1 month');
    while ($firstDayOfNextMonth->format('w') > 0){
      $head .= sprintf('<td class="gray">%d</td>', $firstDayOfNextMonth->format('d'));
      $firstDayOfNextMonth->add(new \DateInterval('P1D'));
    }
    return $head;
  }

}
