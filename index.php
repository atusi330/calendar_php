<?php

// URLのパラメータから値を取得する
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


function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

try{//取得が失敗した場合の処理を書く
  if(!isset($_GET['t']) || !preg_match('/\A\d{4}-\d{2}\z/',$_GET['t'])){//tが入力されなかった場合や入力の書式が違う場合にエラーに投げる
    throw new Exception();
  }
  $thisMonth = new DateTime($_GET['t']);//urlにtの値が入力されたらそれを取得する
}catch(Exception $e){
  $thisMonth = new DateTime('first day of this month');//うまく取得できなかった場合、今月の1日の値が入力されるようにする
}


// var_dump($thisMonth);// 出力が正しいかの確認
// exit;

//先月のパラメータ作成
$dt = clone $thisMonth;//$thisMonth に対して直接 modify() というメソッドを呼ぶと$thisMonthの値が変わってしまうので複製する
$prev = $dt->modify('-1 month')->format('Y-m');//複製したオブジェクトのタイムスタンプを変更して書式を変える

//来月のパラメータ作成
$dt = clone $thisMonth;
$next = $dt->modify('+1 month')->format('Y-m');

$yearMonth = $thisMonth->format('F Y');// オブジェクトからフォーマットを指定した値を$yearMonthにセットする。F(年)Y(月)ここでは2015 09が渡されている

//先月の残りを作成。先月の末日の曜日を探して日曜まで埋めていく
$tail = '';
$lastDayOfNextMonth = new DateTime('last day of ' . $yearMonth . '-1 month');//先月の末日のオブジェクトを作成
while ($lastDayOfNextMonth->format('w') < 6){//先月の末日の曜日を調べて土曜日まで繰り返し
  $tail = sprintf('<td class="gray">%d</td>', $lastDayOfNextMonth->format('d')) . $tail; //tailの前部分に連結
  $lastDayOfNextMonth->sub(new DateInterval('P1D'));//add()を使用して日にちを1日進める
}

$body = '';
$period = new DatePeriod( // DatePeriod:特定の期間の日付オブジェクトを作成するクラス
  new DateTime('first day of '. $yearMonth),//this monthを$yearMonthに置き換える
  new DateInterval('P1D'),//第二引数はどのくらい間隔をあけて日付を作成するか。DateIntarval というにクラスがあるので、そちらに (P1D)1 日ごとという風に書く
  new DateTime('first day of '. $yearMonth . ' +1 month')//next monthはthis month +1 month と表記できる
);

$today = new DateTime('today');//$dayと比較して今日を判別するために$todayを作成
foreach ($period as $day) {
  if($day->format('w') % 7 === 0){ $body .='</tr><tr>';}//日曜日で区切る
  $todayClass = ($day->format('Y-m-d') === $today->format('Y-m-d')) ? 'today' : '';//$dayと$todayの年月日を比較して真ならばtodayを、偽なら空文字を$todayClassに渡す（三項演算子）
  $body .= sprintf('<td class="youbi_%d %s">%d</td>' ,$day->format('w'), $todayClass, $day->format('d'));//classにtodayを追加させる。%sは文字列が入るってこと。
}

//翌月の日にちを作成。翌月の1日の曜日を探して土曜日まで埋める
$head = '';
$firstDayOfNextMonth = new DateTime('first day of ' . $yearMonth . '+1 month');//翌月の1日のオブジェクトを作成
while ($firstDayOfNextMonth->format('w') > 0){//翌月の1日の曜日を調べて日曜日まで繰り返し
  $head .= sprintf('<td class="gray">%d</td>', $firstDayOfNextMonth->format('d'));//headの後に連結+=みたなもの
  $firstDayOfNextMonth->add(new DateInterval('P1D'));//add()を使用して日にちを1日進める
}

$html = '<tr>' . $tail . $body . $head . '</tr>';

 ?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Calender</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <table>
    <thead>
      <tr>
        <th><a href="/?t=<?php echo h($prev);?>">&laquo;</a></th>
        <th colspan="5"><?php echo h($yearMonth); ?></th>
        <th><a href="/?t=<?php echo h($next);?>">&raquo;</a></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Sun</td>
        <td>Mon</td>
        <td>Tue</td>
        <td>Wed</td>
        <td>Thu</td>
        <td>Fri</td>
        <td>Sat</td>
      </tr>
      <?php echo $html ?>
    </tbody>
    <tfoot>
      <th colspan="7"><a href="/">Today<a/></th>
    </tfoot>
  </table>
</body>
