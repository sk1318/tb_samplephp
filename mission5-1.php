<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="sample.css">
</head>
    <body>
<?php 

////データベース接続
  $dsn = 'データベース名';
  $user = 'ユーザー名';
  $password = 'パスワード';
  $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  
  
  //テーブル作成
  $sql = "CREATE TABLE IF NOT EXISTS tbtest"
  ." ("
  . "id INT AUTO_INCREMENT PRIMARY KEY,"
  . "name char(32),"
  . "comment TEXT,"
  . "date TEXT,"
  . "password TEXT"
  .");";
  $stmt = $pdo->query($sql);

$name = $_POST['name'];
$comment = $_POST['comment'];
$date = date("Y/m/d H:i:s");
$password = $_POST['password'];
?>
<div>
<?php
//新規投稿処理
if(!empty($comment) && !empty($name) && !empty($password)){
    //INSERT文を使ってデータ（レコード）の登録
	$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment,date,password) VALUES (:name, :comment, :date, :password)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
	$sql -> execute();
}
?>
</div>

<div>
<?php

//編集番号とパスワードがある場合の処理
$edit = $_POST['edit'];
if(!empty($edit)){
   $id = $edit;
   $memos = $pdo->prepare('SELECT * FROM tbtest WHERE id=?');
   $memos->execute(array($id));
   $memo = $memos->fetch();
   if($memo['password'] == $_POST['editpass']){
      $edit_comment = $memo['comment'];
      $edit_number = $id;
      echo "コメントを編集してください"."<br>"."<hr>";
   }
   else{
       echo "編集する番号もしくはパスワードが間違っています"."<br>"."<hr>";
   }
}
?>
</div>

<div>
<?php

//編集番号がhidden属性のフォームにあったときの処理
if(!empty($_POST['comment']) && !empty($_POST['edit_post'])){
    //入力されているデータレコードの内容を編集
	$id = $_POST['edit_post']; //変更する投稿番号
	$comment = $_POST['comment']; //変更したい名前、変更したいコメントは自分で決めること
	$prepare = $pdo->prepare('UPDATE tbtest SET comment=:comment WHERE id=:id');
	$prepare->bindParam(':comment', $comment, PDO::PARAM_STR);
	$prepare->bindParam(':id', $id, PDO::PARAM_INT);
	$prepare->execute();
	echo "編集しました!!"."<br>"."<hr>";
}
?>
</div>

<div>
<?php
//削除機能
if(!empty($_POST['deleteNo'])){
    //入力したデータレコードを削除する
	$id = $_POST['deleteNo'];
	$memos = $pdo->prepare('SELECT * FROM tbtest WHERE id=?');
   $memos->execute(array($id));
   $memo = $memos->fetch();
   
   if($memo['password'] == $_POST['delpass']){
      $prepare = $pdo->prepare('delete from tbtest where id=:id');
	$prepare->bindParam(':id', $id, PDO::PARAM_INT);
	$prepare->execute();
	echo "削除しました！！"."<br>"."<hr>";
   }
   else{
       echo "削除する番号もしくはパスワードが間違っています"."<br>"."<hr>";
   }
	
}
?>
</div>

<?php
//データレコードを抽出し、表示する
	$serect = $pdo->query('SELECT * FROM tbtest');
	$results = $serect->fetchAll();
	?>
	<div>
	    <?php  
	    foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
	echo $row['id'].',';
	echo $row['name'].',';
	echo $row['comment'].',';
	echo $row['date'].',';
	echo $row['password'].'<br>';
	echo "<hr>";
	}
	    ?>
	    
	</div>




<form method="post" action="">
    <input class="big" type ="password"name="password"placeholder ="パスワード" value="<?php echo $edit_pass; ?>"><br>
    <input  class="big" type="text" name="name" placeholder="名前" value="<?php echo $edit_name; ?>"><br>
   <input type="hidden" name="edit_post" value="<?php echo $edit_number; ?>">
   <input  class="big" type="text" name="comment" placeholder="コメント" value="<?php echo $edit_comment; ?>"><br>
    <input  class="big submit" type="submit" value="送信">
    </form>
<form method="post" action="">
    <input class="big"  type="text" name="deleteNo" placeholder="削除対象番号"><br>
    <input class="big"  type ="password"name="delpass"placeholder ="パスワード"><br>
    <input class="big submit"  type="submit"  value="削除"><br>
</form>
<form method="post" action="">
    <input class="big"  type"text" name="edit" placeholder="編集対象番号"><br>
    <input class="big"  type ="password"name="editpass"placeholder ="パスワード"><br>
    <input class="big submit"  type="submit" value="編集">
</form>
 </body>
</html>