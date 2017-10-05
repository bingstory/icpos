<?php
echo '<form action="mp.php" method="post">
	<font color="red">第一步：</font>
  <p>下载这个txt</p>
  <img src="http://www.gulaitec.com/1.jpg"><br/>
 <font color="red">第二步：</font>
  <img src="http://www.gulaitec.com/2.jpg"><br/>
  <p>复制txt里面的内容，并填写(比如：ZNPSJx9BdYN5MHVf): <input type="text" name="content" /></p>
  
   <p></p>
    <p></p>
  <input type="submit" value="提交" />
</form>';


if($_POST){
	
		if(strlen($_POST['content'])<20){
			if($_POST){
					$file = fopen('MP_verify_'.$_POST['content'].'.txt','w');
					fwrite($file,$_POST['content']);
					fclose($file);
					echo '<font size="10px" color="red">添加成功</font>';
				}else{
					echo '添加失败';
				}
			
		}else{
			echo '文件内容有误,请重新填写';
		}
	

	
	
}