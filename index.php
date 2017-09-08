<?php

$body = '';
$period = new DatePeriod( // DatePeriod:特定の期間の日付オブジェクトを作成するクラス
  new DateTime('first day of this month'),//第一引数は最初の日の DateTime オブジェクト。DateTime() に文字列で「first day of this month」と書けば柔軟に最初の日と解釈してくれる
  new DateInterval('P1D'),//第二引数はどのくらい間隔をあけて日付を作成するか。DateIntarval というにクラスがあるので、そちらに (P1D)1 日ごとという風に書く
  new DateTime('first day of next month')//第三引数は期間の終わりを指定。指定した日は含まないので「first day of next month」と書けば翌月の1日の前日（今月の末）とかける。
);

foreach ($period as $day) {
  if($day->format('w') % 7 === 0){ $body .='</tr><tr>';}//日曜日で区切る
  $body .= sprintf('<td class="youbi_%d">%d</td>' ,$day->format('w'), $day->format('d'));//sprintf()書式付きで文字列を作成する命令。format():DateTimeオブジェクトを好きな書式で表示する。書式の種類は公式参照「date」で検索で出る
}

//翌月の日にちを作成。翌月の1日の曜日を探して土曜日まで埋める
$head = '';
$firstDayOfNextMonth = new DateTime('first day of next month');//翌月の1日のオブジェクトを作成
while ($firstDayOfNextMonth->format('w') > 0){//翌月の1日の曜日を調べて日曜日まで繰り返し
  $head .= sprintf('<td class="gray">%d</td>', $firstDayOfNextMonth->format('d'));//headの後に連結+=みたなもの
  $firstDayOfNextMonth->add(new DateInterval('P1D'));//add()を使用して日にちを1日進める
}

//先月の残りを作成。先月の末日の曜日を探して日曜まで埋めていく
$tail = '';
$lastDayOfNextMonth = new DateTime('last day of previous month');//先月の末日のオブジェクトを作成
while ($lastDayOfNextMonth->format('w') < 6){//先月の末日の曜日を調べて土曜日まで繰り返し
  $tail = sprintf('<td class="gray">%d</td>', $lastDayOfNextMonth->format('d')) . $tail; //tailの前部分に連結
  $lastDayOfNextMonth->sub(new DateInterval('P1D'));//add()を使用して日にちを1日進める
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
        <th><a href="">&laquo;</a></th>
        <th colspan="5">August 2015</th>
        <th><a href="">&raquo;</a></th>
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
      <!-- <tr> -->
        <!-- <?php echo $tail . $body . $head; ?> -->
       <!-- <td class="youbi_0">1</td>
       <td class="youbi_1">2</td>
       <td class="youbi_2">3</td>
       <td class="youbi_3">4</td>
       <td class="youbi_4 today">5</td>
       <td class="youbi_5">6</td>
       <td class="youbi_6">8</td>
     </tr>
     <tr>
       <td class="youbi_0">30</td>
       <td class="youbi_1">31</td>
       <td class="gray">1</td>
       <td class="gray">2</td>
       <td class="gray">3</td>
       <td class="gray">4</td>
       <td class="gray">5</td> -->
     <!-- </tr> -->
    </tbody>
    <tfoot>
      <th colspan="7"><a href="">Today<a/></th>
    </tfoot>
  </table>
</body>
