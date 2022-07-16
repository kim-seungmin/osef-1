<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gugi&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/7395e48b31.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>억새풀</title>
</head>

<body>        

<?php
    include_once("header.html"); 

    //현재 페이지탐색
    if(!isset($_COOKIE["docsPageCookie".$_POST["searchWord"]])) {
        setcookie("docsPageCookie".$_POST["searchWord"],"1",time()+(86400),"/") ; //86400=1day
        $currentPage = 1;
      } else {
        $currentPage = $_COOKIE["docsPageCookie".$_POST["searchWord"]];
      }

    $servername = "localhost";
    $DBname = "root";
    $DBpassword = "1234";    

    $conn = new mysqli($servername,$DBname,$DBpassword,"farm");
    $conn -> set_charset('utf8mb4');
    //전체 페이지수
    $stmt = $conn -> prepare("SELECT COUNT(*) AS \"num\" FROM sell_info"); 
    $stmt -> execute();
    $result = $stmt -> get_result();
    $row = $result -> fetch_assoc();
    $totalPageNum = ceil($row['num']/20);
    
    $stmt = $conn -> prepare("SELECT * FROM sell_info where title = ?  LIMIT ?,20");

    $stmt -> bind_param("si",$_POST["searchWord"],$page);
    $page = ($currentPage-1)*20;
    $stmt -> execute();
    $result = $stmt -> get_result();

    //--------------------------------------------게시물
    echo ("<table border=\"1\">");
    echo("<th>제목</th><th>등록자</th><th>등록일</th>");
    while($row = $result -> fetch_assoc()){
        echo("<tr>");
        echo("<td>".$row['title']."</td>");
        echo("<td>".$row['member_name']."</td>");
        echo("<td>".$row['upload']."</td>");        
        echo("</tr>");
    }  
    echo("</table>");
    $stmt->close();
    $conn->close();
    
    echo("페이지");
    //--------------------------------------------페이지
    $showingPage=4; //앞뒤로 보여지는 페이지 수
    echo("<form method=\"post\">");
    echo("<table border=\"1\"> <tr>");
    if (($currentPage-$showingPage)<=1){
        for($i=1;$i<$currentPage;$i++){
            echo("<td onclick=\"refresh(".$i.")\" style=\"cursor:pointer\">".$i."</td>");  
        }
    }else{
        echo("<td  onclick=\"refresh(".($currentPage-$showingPage-1).")\" style=\"cursor:pointer\" > ... </td>");
        for($i=$currentPage-$showingPage;$i<$currentPage;$i++){
            echo("<td onclick=\"refresh(".$i.")\" style=\"cursor:pointer\">".$i."</td>");  
        }
    }

    echo("<td style=\"background-color: aqua;\">".$currentPage."</td>");

    if(($currentPage+$showingPage)>=$totalPageNum){
        for($i=$currentPage+1;$i<=$totalPageNum;$i++){
            echo("<td onclick=\"refresh(".$i.")\" style=\"cursor:pointer\">".$i."</td>");  
        }
    }else{
        for($i=$currentPage+1;$i<=($currentPage+$showingPage);$i++){
            echo("<td onclick=\"refresh(".$i.")\" style=\"cursor:pointer\">".$i."</td>");              
        }
        echo("<td  onclick=\"refresh(".($currentPage+$showingPage+1).")\" style=\"cursor:pointer\" > ... </td>");
    }
    echo("</tr> </table>");    
?>
<form name = "docsForm" method="post" action="docs-search-submit.php" enctype="multipart/form-data" > 
    <input type="text" name="searchWord" required class="searchInput" value=<?php $_POST["searchWord"]?>/>
    <input type="submit" value="업로드" class="searchSubmit" name="submit">
</form>

<?php include_once("footer.html"); ?>
<script>
function refresh(page) {
  document.cookie = ("docsPageCookie ="+page);
  location.reload();
}
</script>
</body>
</html>